<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Earning;
use App\Enums\EarningStatus;
use App\Models\Performance;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinancialSummaryService
{
    public function summary(
        Performance $performance
    ): array {
        $earnings = $this->earnings($performance);

        return $this->buildSummary(
            $earnings,
            $this->bonuses($performance),
            $this->penalties($performance),
            $this->deductions($performance)
        );
    }

    public function summaryBetween(
        Performance $performance,
        Carbon $start,
        Carbon $end,
        bool $includeAllEarnings = false
    ): array {
        $earnings = $this->earningsBetween(
            $performance,
            $start,
            $end,
            $includeAllEarnings
        );

        return $this->buildSummary(
            $earnings,
            $this->bonusesBetween(
                $performance,
                $start,
                $end
            ),
            $this->penaltiesBetween(
                $performance,
                $start,
                $end
            ),
            $this->deductionsBetween(
                $performance,
                $start,
                $end
            )
        );
    }

    private function buildSummary(
        Collection $earnings,
        float $bonuses,
        float $penalties,
        float $deductions
    ): array {
        $gross = round(
            (float) $earnings->sum(
                static fn (Earning $earning): float =>
                    (float) ($earning->gross_usd ?? 0)
            ),
            2
        );

        $bonuses = round($bonuses, 2);
        $penalties = round($penalties, 2);
        $deductions = round($deductions, 2);

        $net = round(
            $gross
            + $bonuses
            - $penalties
            - $deductions,
            2
        );

        $percentages = $this->percentages($earnings);

        $earningModelShare = round(
            (float) $earnings->sum(
                static fn (Earning $earning): float =>
                    (float) ($earning->model_share_usd ?? 0)
            ),
            2
        );

        $earningStudioShare = round(
            (float) $earnings->sum(
                static fn (Earning $earning): float =>
                    (float) ($earning->studio_share_usd ?? 0)
            ),
            2
        );

        $earningNet = round(
            (float) $earnings->sum(
                static fn (Earning $earning): float =>
                    (float) ($earning->net_usd ?? 0)
            ),
            2
        );

        $adjustmentNet = round(
            $net - $earningNet,
            2
        );

        $modelAdjustment = round(
            $adjustmentNet
            * ($percentages['model_percentage'] / 100),
            2
        );

        $studioAdjustment = round(
            $adjustmentNet
            * ($percentages['studio_percentage'] / 100),
            2
        );

        $modelShare = round(
            $earningModelShare + $modelAdjustment,
            2
        );

        $studioShare = round(
            $earningStudioShare + $studioAdjustment,
            2
        );

        return [
            'gross_usd' => $gross,

            'bonus_usd' => $bonuses,

            'penalty_usd' => $penalties,

            'deduction_usd' => $deductions,

            'net_usd' => $net,

            'model_percentage' =>
                $percentages['model_percentage'],

            'studio_percentage' =>
                $percentages['studio_percentage'],

            'model_share_usd' =>
                $modelShare,

            'studio_share_usd' =>
                $studioShare,
        ];
    }

    private function earnings(
        Performance $performance
    ): Collection {
        if ($performance->relationLoaded('earnings')) {
            return $performance->earnings->filter(
                fn (Earning $earning): bool => in_array(
                    $earning->status,
                    [EarningStatus::Approved, EarningStatus::Paid],
                    true
                )
            )->values();
        }

        return $performance
            ->earnings()
            ->whereIn('status', [
                EarningStatus::Approved->value,
                EarningStatus::Paid->value,
            ])
            ->get();
    }

    private function earningsBetween(
        Performance $performance,
        Carbon $start,
        Carbon $end,
        bool $includeAllEarnings = false
    ): Collection {
        $query = $performance
            ->earnings()
            ->whereBetween(
                'earned_at',
                [
                    $start,
                    $end,
                ]
            );

        if (! $includeAllEarnings) {
            $query->whereIn('status', [
                EarningStatus::Approved->value,
                EarningStatus::Paid->value,
            ]);
        }

        return $query
            ->orderBy('earned_at')
            ->get();
    }

    private function bonuses(
        Performance $performance
    ): float {
        if ($performance->relationLoaded('bonuses')) {
            return round(
                (float) $performance
                    ->bonuses
                    ->sum('amount'),
                2
            );
        }

        return round(
            (float) $performance
                ->bonuses()
                ->sum('amount'),
            2
        );
    }

    private function bonusesBetween(
        Performance $performance,
        Carbon $start,
        Carbon $end
    ): float {
        return round(
            (float) $performance
                ->bonuses()
                ->whereBetween(
                    'date',
                    [
                        $start->toDateString(),
                        $end->toDateString(),
                    ]
                )
                ->sum('amount'),
            2
        );
    }

    private function penalties(
        Performance $performance
    ): float {
        if ($performance->relationLoaded('penalties')) {
            return round(
                (float) $performance
                    ->penalties
                    ->sum('amount'),
                2
            );
        }

        return round(
            (float) $performance
                ->penalties()
                ->sum('amount'),
            2
        );
    }

    private function penaltiesBetween(
        Performance $performance,
        Carbon $start,
        Carbon $end
    ): float {
        return round(
            (float) $performance
                ->penalties()
                ->whereBetween(
                    'date',
                    [
                        $start->toDateString(),
                        $end->toDateString(),
                    ]
                )
                ->sum('amount'),
            2
        );
    }

    private function deductions(
        Performance $performance
    ): float {
        if ($performance->relationLoaded('deductions')) {
            return round(
                (float) $performance
                    ->deductions
                    ->sum('amount'),
                2
            );
        }

        return round(
            (float) $performance
                ->deductions()
                ->sum('amount'),
            2
        );
    }

    private function deductionsBetween(
        Performance $performance,
        Carbon $start,
        Carbon $end
    ): float {
        return round(
            (float) $performance
                ->deductions()
                ->whereBetween(
                    'date',
                    [
                        $start->toDateString(),
                        $end->toDateString(),
                    ]
                )
                ->sum('amount'),
            2
        );
    }

    private function percentages(
        Collection $earnings
    ): array {
        if ($earnings->isEmpty()) {
            return [
                'model_percentage' => 0.0,
                'studio_percentage' => 0.0,
            ];
        }

        $gross = (float) $earnings->sum(
            static fn (Earning $earning): float =>
                (float) ($earning->gross_usd ?? 0)
        );

        if ($gross <= 0) {
            $earning = $earnings->first();

            return [
                'model_percentage' => $this->percentageFromShares(
                    (float) ($earning->gross_usd ?? 0),
                    (float) ($earning->model_share_usd ?? 0)
                ),

                'studio_percentage' => $this->percentageFromShares(
                    (float) ($earning->gross_usd ?? 0),
                    (float) ($earning->studio_share_usd ?? 0)
                ),
            ];
        }

        $modelShare = (float) $earnings->sum(
            static fn (Earning $earning): float =>
                (float) ($earning->model_share_usd ?? 0)
        );

        $studioShare = (float) $earnings->sum(
            static fn (Earning $earning): float =>
                (float) ($earning->studio_share_usd ?? 0)
        );

        return [
            'model_percentage' => round(
                ($modelShare / $gross) * 100,
                2
            ),

            'studio_percentage' => round(
                ($studioShare / $gross) * 100,
                2
            ),
        ];
    }

    private function percentageFromShares(
        float $gross,
        float $share
    ): float {
        if ($gross <= 0) {
            return 0.0;
        }

        return round(
            ($share / $gross) * 100,
            2
        );
    }
}
