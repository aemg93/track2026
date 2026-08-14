<?php

namespace App\Services;

use App\Models\Performance;
use Carbon\Carbon;

class FinancialSummaryService
{
    public function summary(
        Performance $performance
    ): array {
        $gross = $this->gross($performance);
        $bonuses = $this->bonuses($performance);
        $penalties = $this->penalties($performance);
        $deductions = $this->deductions($performance);

        return $this->buildSummary(
            $performance,
            $gross,
            $bonuses,
            $penalties,
            $deductions
        );
    }

    public function summaryBetween(
        Performance $performance,
        Carbon $start,
        Carbon $end
    ): array {
        $gross = round(
            (float) $performance
                ->earnings()
                ->whereBetween(
                    'earned_at',
                    [
                        $start->copy()->startOfDay(),
                        $end->copy()->endOfDay(),
                    ]
                )
                ->sum('gross_usd'),
            2
        );

        $bonuses = round(
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

        $penalties = round(
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

        $deductions = round(
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

        return $this->buildSummary(
            $performance,
            $gross,
            $bonuses,
            $penalties,
            $deductions
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Build Summary
    |--------------------------------------------------------------------------
    */

    private function buildSummary(
        Performance $performance,
        float $gross,
        float $bonuses,
        float $penalties,
        float $deductions
    ): array {
        $net = $this->net(
            $gross,
            $bonuses,
            $penalties,
            $deductions
        );

        $percentages = $this->percentages($performance);

        $shares = $this->shares(
            $net,
            $percentages
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
                $shares['model_share_usd'],

            'studio_share_usd' =>
                $shares['studio_share_usd'],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Gross
    |--------------------------------------------------------------------------
    */

    private function gross(
        Performance $performance
    ): float {
        if ($performance->relationLoaded('earnings')) {
            return round(
                (float) $performance
                    ->earnings
                    ->sum('gross_usd'),
                2
            );
        }

        return round(
            (float) $performance
                ->earnings()
                ->sum('gross_usd'),
            2
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Bonuses
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Penalties
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Deductions
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Net
    |--------------------------------------------------------------------------
    */

    private function net(
        float $gross,
        float $bonuses,
        float $penalties,
        float $deductions
    ): float {
        return round(
            $gross
            + $bonuses
            - $penalties
            - $deductions,
            2
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Percentages
    |--------------------------------------------------------------------------
    */

    private function percentages(
        Performance $performance
    ): array {
        $split = $performance->relationLoaded('split')
            ? $performance->split
            : $performance->split()->first();

        return [
            'model_percentage' => (float) (
                $split?->model_percentage ?? 60
            ),

            'studio_percentage' => (float) (
                $split?->studio_percentage ?? 40
            ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Shares
    |--------------------------------------------------------------------------
    */

    private function shares(
        float $net,
        array $percentages
    ): array {
        return [
            'model_share_usd' => round(
                $net *
                ($percentages['model_percentage'] / 100),
                2
            ),

            'studio_share_usd' => round(
                $net *
                ($percentages['studio_percentage'] / 100),
                2
            ),
        ];
    }
}