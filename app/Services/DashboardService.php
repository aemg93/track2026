<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\User;

class DashboardService
{
    public function __construct(
        private StatisticsService $statistics,
        private FinancialSummaryService $financialSummary
    ) {
    }

    public function getData(User $user): array
    {
        $dashboard = match (true) {
            $user->hasRole('Super Admin') =>
                $this->superAdminData(),

            $user->hasRole('Admin') =>
                $this->studioData($user, 'admin'),

            $user->hasRole('Monitor') =>
                $this->studioData($user, 'monitor'),

            $user->hasRole('Performance') =>
                $this->performanceData($user),

            default => [],
        };

        return [
            'dashboard' => $dashboard,
            'finance'   => $this->financeData(),
        ];
    }

    private function superAdminData(): array
    {
        $performances = Performance::query()
            ->with([
                'user:id,name,email',
                'platforms:id,name,type,conversion_rate,multiplier',
                'earnings',
                'bonuses',
                'penalties',
                'deductions',
                'split',
            ])
            ->get();

        return [
            'view'        => 'super_admin',
            'total_models'=> $performances->count(),
            'ranking'     => $performances
                ->sortByDesc('ranking_score')
                ->take(10)
                ->values(),
            'models'      => $performances
                ->map(fn (Performance $performance) =>
                    $this->formatPerformance($performance)),
        ];
    }

    private function studioData(User $user, string $view): array
    {
        $performances = Performance::query()
            ->where('studio_id', $user->studio_id)
            ->with([
                'platforms:id,name,type,conversion_rate,multiplier',
                'earnings',
                'bonuses',
                'penalties',
                'deductions',
                'split',
            ])
            ->get();

        return [
            'view'        => $view,
            'total_models'=> $performances->count(),
            'ranking'     => $performances
                ->sortByDesc('ranking_score')
                ->take(10)
                ->values(),
            'models'      => $performances
                ->map(fn (Performance $performance) =>
                    $this->formatPerformance($performance, $view)),
        ];
    }

    private function performanceData(User $user): array
    {
        $performance = $user->performance()
            ->with([
                'platforms',
                'earnings',
                'bonuses',
                'penalties',
                'deductions',
                'split',
            ])
            ->first();

        if (!$performance) {
            return [
                'view'    => 'performance',
                'message' => 'Performance no asociada',
            ];
        }

        return array_merge(
            ['view' => 'performance'],
            $this->formatPerformance($performance)
        );
    }

    private function formatPerformance(Performance $performance, ?string $view = null): array
    {
        $stats   = $this->statistics->performanceStats($performance);
        $summary = $this->financialSummary->summary($performance);

        return [
            'id'         => $performance->id,
            'first_name' => $performance->first_name,
            'last_name'  => $performance->last_name,
            'name'       => trim($performance->first_name . ' ' . $performance->last_name),
            'nickname'   => $performance->nickname,
            'ranking'    => $performance->ranking_score,
            'hours'      => $performance->hours_streamed,
            'platforms'  => $performance->platforms,
            'financial'  => $summary,
            'statistics' => $view === 'monitor'
                ? [
                    'today' => [
                        'gross_usd' => $stats['today']['gross_usd'] ?? 0,
                        'model_usd' => $stats['today']['model_usd'] ?? 0,
                    ],
                    'weekly' => [
                        'gross_usd' => $stats['weekly']['gross_usd'] ?? 0,
                        'model_usd' => $stats['weekly']['model_usd'] ?? 0,
                    ],
                ]
                : $stats,
        ];
    }

    private function financeData(): array
    {
        $performances = Performance::with([
            'earnings',
            'bonuses',
            'penalties',
            'deductions',
            'split',
        ])->get();

        $earnings    = 0;
        $bonuses     = 0;
        $penalties   = 0;
        $deductions  = 0;
        $net         = 0;
        $modelShare  = 0;
        $studioShare = 0;
        $pending     = 0;

        foreach ($performances as $performance) {
            $summary = $this->financialSummary->summary($performance);

            $earnings    += $summary['gross_usd'];
            $bonuses     += $summary['bonus_usd'];
            $penalties   += $summary['penalty_usd'];
            $deductions  += $summary['deduction_usd'];
            $net         += $summary['net_usd'];
            $modelShare  += $summary['model_share_usd'];
            $studioShare += $summary['studio_share_usd'];

            $pending += $performance
                ->earnings()
                ->where('status', 'pending')
                ->sum('gross_usd');
        }

        return [
            'totals' => [
                'earnings'   => round($earnings, 2),
                'bonuses'    => round($bonuses, 2),
                'penalties'  => round($penalties, 2),
                'deductions' => round($deductions, 2),
            ],
            'net_balance' => round($net, 2),
            'distribution'=> [
                'models' => round($modelShare, 2),
                'studio' => round($studioShare, 2),
            ],
            'installments'=> [
                'active'         => 0,
                'pending_amount' => round($pending, 2),
            ],
        ];
    }
}
