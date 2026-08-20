<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Performance;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PerformanceDashboardService
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

    public function __construct(
        private StatisticsService $statistics,
        private FinancialSummaryService $financialSummary,
    ) {
    }

    public function data(User $user): array
    {
        return match (true) {

            $user->hasRole('Super Admin') =>
                $this->buildDashboard(
                    Performance::query()
                        ->with(self::PERFORMANCE_RELATIONS)
                        ->get(),
                    'super_admin'
                ),

            $user->hasRole('Admin'),
            $user->hasRole('Monitor') =>
                $this->buildDashboard(
                    Performance::query()
                        ->where(
                            'studio_id',
                            $user->studio_id
                        )
                        ->with(self::PERFORMANCE_RELATIONS)
                        ->get(),
                    $user->hasRole('Admin')
                        ? 'admin'
                        : 'monitor'
                ),

            $user->hasRole('Performance') =>
                $this->performanceData($user),

            default => [
                'view' => 'unknown',
                'models' => [],
            ],
        };
    }

    private function buildDashboard(
        Collection $performances,
        string $view
    ): array {
        return [
            'view' => $view,

            'total_models' =>
                $performances->count(),

            'ranking' =>
                $performances
                    ->sortByDesc(
                        fn (Performance $performance) =>
                            $performance->ranking_score ?? 0
                    )
                    ->take(10)
                    ->values()
                    ->map(
                        fn (Performance $performance) =>
                            $this->buildPerformance(
                                $performance,
                                $view
                            )
                    )
                    ->toArray(),

            'models' =>
                $performances
                    ->map(
                        fn (Performance $performance) =>
                            $this->buildPerformance(
                                $performance,
                                $view
                            )
                    )
                    ->values()
                    ->toArray(),
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
            'id' =>
                $performance->id,

            'first_name' =>
                $performance->first_name,

            'last_name' =>
                $performance->last_name,

            'name' =>
                trim(
                    "{$performance->first_name} {$performance->last_name}"
                ),

            'nickname' =>
                $performance->nickname,

            'work_shift' =>
                $performance->work_shift?->value,

            'ranking' =>
                $performance->ranking_score ?? 0,

            'hours' =>
                $performance->hours_streamed ?? 0,

            'platforms' =>
                $this->buildPlatforms($performance),

            'financial' =>
                $summary,

            'statistics' =>
                $stats,
        ];
    }

    private function buildPlatforms(
        Performance $performance
    ): array {
        return $performance
            ->platforms
            ->map(
                fn ($platform) => [
                    'id' =>
                        $platform->id,

                    'name' =>
                        $platform->name,

                    'type' =>
                        $platform->type,
                ]
            )
            ->values()
            ->toArray();
    }
}