<?php

namespace App\Services;

use App\Enums\ShiftStatus;
use App\Models\Performance;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PerformanceDashboardService
{
    private const PERFORMANCE_RELATIONS = [
        'user:id,name,email',
        'platforms:id,name,type,conversion_rate,multiplier',
        'earnings',
        'split',
    ];


    public function __construct(
        private StatisticsService $statistics,
        private FinancialSummaryService $financialSummary,
    ) {}


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


            $user->hasRole('Admin') =>
                $this->buildDashboard(
                    Performance::query()
                        ->where(
                            'studio_id',
                            $user->studio_id
                        )
                        ->with(self::PERFORMANCE_RELATIONS)
                        ->get(),
                    'admin'
                ),


            $user->hasRole('Monitor') =>
                $this->buildDashboard(
                    Performance::query()
                        ->where(
                            'studio_id',
                            $user->studio_id
                        )
                        ->with(self::PERFORMANCE_RELATIONS)
                        ->get(),
                    'monitor'
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
                        fn(Performance $performance) =>
                            $performance->ranking_score ?? 0
                    )
                    ->take(10)
                    ->values()
                    ->map(
                        fn(Performance $performance) =>
                            $this->buildPerformance(
                                $performance,
                                $view
                            )
                    )
                    ->toArray(),

            'models' =>
                $performances
                    ->map(
                        fn(Performance $performance) =>
                            $this->buildPerformance(
                                $performance,
                                $view
                            )
                    )
                    ->values()
                    ->toArray(),

            'active_performances' =>
                $this->activePerformances(),
        ];
    }

    private function performanceData(User $user): array
    {
        $performance =
            $user
                ->performance()
                ->with(self::PERFORMANCE_RELATIONS)
                ->first();

        if (! $performance) {

            return [
                'view' =>
                    'performance',

                'message' =>
                    'Performance no asociada',
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


        $stats =
            $this->statistics
                ->performanceStats($performance);

        $summary =
            $this->financialSummary
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
                fn($platform) => [

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

    private function activePerformances(): array
    {

        return Shift::query()

            ->whereHas('performance')

            ->whereIn(
                'status',
                [
                    ShiftStatus::Active->value,
                    ShiftStatus::Paused->value,
                ]
            )

            ->with([
                'performance:id,studio_id,first_name,last_name,nickname',
            ])

            ->get()

            ->map(
                fn(Shift $shift) => [

                    'id' =>
                        $shift->performance->id,


                    'name' =>
                        trim(
                            "{$shift->performance->first_name} {$shift->performance->last_name}"
                        ),


                    'nickname' =>
                        $shift->performance->nickname,


                    'status' =>
                        $shift->status?->value,

                ]
            )

            ->values()

            ->toArray();
    }
}