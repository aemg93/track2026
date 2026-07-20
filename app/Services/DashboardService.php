<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\User;
use App\Models\Shift;
use App\Enums\ShiftStatus;

class DashboardService
{
    private const PERFORMANCE_RELATIONS = [
        'user:id,name,email',
        'platforms:id,name,type,conversion_rate,multiplier',
        'earnings',
        'bonuses',
        'penalties',
        'deductions',
        'split',
    ];

    private const FINANCIAL_RELATIONS = [
        'earnings',
        'bonuses',
        'penalties',
        'deductions',
        'split',
    ];


    public function __construct(
        private StatisticsService $statistics,
        private FinancialSummaryService $financialSummary,
        private ShiftService $shiftService,
        private ShiftActivityService $shiftActivityService,
    ) {}


    public function getData(User $user): array
    {
        $dashboard = match (true) {

            $user->hasRole('Super Admin') =>
                $this->buildDashboard(
                    Performance::query()
                        ->with(self::PERFORMANCE_RELATIONS)
                        ->get(),
                    'super_admin'
                ),

            $user->hasRole('Admin') =>
                $this->buildDashboard(
                    Performance::query()
                        ->where('studio_id', $user->studio_id)
                        ->with(self::PERFORMANCE_RELATIONS)
                        ->get(),
                    'admin'
                ),

            $user->hasRole('Monitor') =>
                $this->buildDashboard(
                    Performance::query()
                        ->where('studio_id', $user->studio_id)
                        ->with(self::PERFORMANCE_RELATIONS)
                        ->get(),
                    'monitor'
                ),

            $user->hasRole('Performance') =>
                $this->performanceData($user),

            default =>
                [
                    'view' => 'unknown',
                    'models' => [],
                ],
        };


        return [
           'dashboard' => $dashboard,
           'finance' => $this->financeData($user),
           'operations' => $this->operationData($user),
        ];
    }


    private function buildDashboard($performances, string $view): array
    {
        return [
            'view' => $view,

            'total_models' => $performances->count(),

            'ranking' => $performances
                ->sortByDesc('ranking_score')
                ->take(10)
                ->values(),

            'models' => $performances
                ->map(fn(Performance $p) =>
                    $this->buildPerformance($p, $view)
                ),

            'active_performances' => $this->activePerformances(),
        ];
    }


    private function performanceData(User $user): array
    {
        $performance = $user
            ->performance()
            ->with(self::PERFORMANCE_RELATIONS)
            ->first();


        if (! $performance) {
            return [
                'view' => 'performance',
                'message' => 'Performance no asociada',
            ];
        }


        return array_merge(
            [
                'view' => 'performance',
            ],
            $this->buildPerformance($performance)
        );
    }


    private function buildPerformance(
        Performance $performance,
        ?string $view = null
    ): array {

        $stats = $this->statistics
            ->performanceStats($performance);

        $summary = $this->financialSummary
            ->summary($performance);


        return [
            'id' => $performance->id,
            'first_name' => $performance->first_name,
            'last_name' => $performance->last_name,
            'name' => trim(
                $performance->first_name . ' ' .
                $performance->last_name
            ),
            'nickname' => $performance->nickname,
            'ranking' => $performance->ranking_score,
            'hours' => $performance->hours_streamed,

            'platforms' =>
                $this->buildPlatforms($performance),

            'financial' => $summary,

            'statistics' =>
                $view === 'monitor'
                    ? [
                        'today' => [
                            'gross_usd' =>
                                $stats['today']['gross_usd'] ?? 0,

                            'model_usd' =>
                                $stats['today']['model_usd'] ?? 0,
                        ],

                        'weekly' => [
                            'gross_usd' =>
                                $stats['weekly']['gross_usd'] ?? 0,

                            'model_usd' =>
                                $stats['weekly']['model_usd'] ?? 0,
                        ],
                    ]
                    : $stats,
        ];
    }


    private function buildPlatforms(
        Performance $performance
    ): array {

        return $performance->platforms
            ->map(fn($platform) => [
                'id' => $platform->id,
                'name' => $platform->name,
                'type' => $platform->type,
            ])
            ->values()
            ->toArray();
    }


   private function financeData(User $user): array
{
    $totals = [
        'earnings' => 0,
        'bonuses' => 0,
        'penalties' => 0,
        'deductions' => 0,
        'net' => 0,
        'model_share' => 0,
        'studio_share' => 0,
        'pending' => 0,
    ];


    $query = Performance::with(
        self::FINANCIAL_RELATIONS
    );


    if (! $user->hasRole('Super Admin')) {

        $query->where(
            'studio_id',
            $user->studio_id
        );
    }


    $performances = $query->get();


    foreach ($performances as $performance) {

        $summary = $this->financialSummary
            ->summary($performance);


        $totals['earnings'] += $summary['gross_usd'];
        $totals['bonuses'] += $summary['bonus_usd'];
        $totals['penalties'] += $summary['penalty_usd'];
        $totals['deductions'] += $summary['deduction_usd'];
        $totals['net'] += $summary['net_usd'];
        $totals['model_share'] += $summary['model_share_usd'];
        $totals['studio_share'] += $summary['studio_share_usd'];


        $totals['pending'] += $performance
            ->earnings()
            ->where('status', 'pending')
            ->sum('gross_usd');
    }


    return [
        'totals' => [
            'earnings' =>
                round($totals['earnings'], 2),

            'bonuses' =>
                round($totals['bonuses'], 2),

            'penalties' =>
                round($totals['penalties'], 2),

            'deductions' =>
                round($totals['deductions'], 2),
        ],

        'net_balance' =>
            round($totals['net'], 2),

        'distribution' => [
            'models' =>
                round($totals['model_share'], 2),

            'studio' =>
                round($totals['studio_share'], 2),
        ],

        'installments' => [
            'active' => 0,

            'pending_amount' =>
                round($totals['pending'], 2),
        ],
    ];
}

    private function operationData(User $user): array
{
    $shifts = $user->hasRole('Super Admin')
        ? $this->shiftService->active()
        : $this->shiftService->activeByStudio(
            \App\Models\Studio::find($user->studio_id)
        );


    return [
        'total_active_shifts' =>
            $shifts->count(),


        'active_models' =>
            $shifts
                ->filter(fn(Shift $shift) =>
                    $shift->isActive()
                )
                ->pluck('performance_id')
                ->unique()
                ->count(),


        'paused_models' =>
            $shifts
                ->filter(fn(Shift $shift) =>
                    $shift->isPaused()
                )
                ->pluck('performance_id')
                ->unique()
                ->count(),


        'active_shifts' =>
            $shifts
                ->filter(fn(Shift $shift) =>
                    $shift->performance !== null
                )
                ->map(fn(Shift $shift) =>
                    $this->buildShift($shift)
                )
                ->values()
                ->toArray(),
    ];
}


    private function buildShift(Shift $shift): array
{
    $workedSeconds = $shift->workedSeconds();

    // Traer actividad completa del turno
    $activity = $this->shiftActivityService->activity($shift);

    return [
        'id' => $shift->id,
        'status' => $shift->status?->value,
        'started_at' => $shift->started_at,
        'last_resumed_at' => $shift->last_resumed_at,
        'paused_at' => $shift->paused_at,
        'ended_at' => $shift->ended_at,
        'worked_seconds' => $workedSeconds,
        'total_paused_seconds' => (int) $shift->total_paused_seconds,

        'duration' => [
            'minutes'   => intdiv($workedSeconds, 60),
            'hours'     => round($workedSeconds / 3600, 2),
            'formatted' => gmdate('H:i:s', $workedSeconds),
        ],

        'performance' => $shift->performance ? [
            'id' => $shift->performance->id,
            'name' => trim(
                $shift->performance->first_name .
                ' ' .
                $shift->performance->last_name
            ),
            'nickname' => $shift->performance->nickname,
            'platforms' => $this->buildPlatforms($shift->performance),
        ] : null,

        'studio' => $shift->studio
            ? [
                'id' => $shift->studio->id,
                'name' => $shift->studio->name,
            ]
            : null,

        'actions' => [
            'can_pause'  => $shift->isActive(),
            'can_resume' => $shift->isPaused(),
            'can_finish' => ! $shift->isFinished(),
        ],

        // Ajuste: ahora se expone también financial_summary
        'activity' => [
            'timeline'          => $activity['timeline'],
            'metrics'           => $activity['summary'],
            'financial_summary' => $activity['financial_summary'],
        ],
    ];
}



   private function activePerformances(): array
{
    return Shift::query()
        ->whereHas('performance')
        ->whereIn('status', [
            ShiftStatus::Active->value,
            ShiftStatus::Paused->value,
        ])
        ->with([
            'performance:id,studio_id,first_name,last_name,nickname',
        ])
        ->get()
        ->filter(fn(Shift $shift) =>
            $shift->performance !== null
        )
        ->map(fn(Shift $shift) => [

            'id' =>
                $shift->performance->id,

            'name' =>
                trim(
                    $shift->performance->first_name .
                    ' ' .
                    $shift->performance->last_name
                ),

            'nickname' =>
                $shift->performance->nickname,

            'status' =>
                $shift->status?->value,

        ])
        ->values()
        ->toArray();
}
}