<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Performance;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FinancialDashboardService
{
    private const FINANCIAL_RELATIONS = [
        'earnings',
        'bonuses',
        'penalties',
        'deductions',
        'split',
    ];

    public function __construct(
        private FinancialSummaryService $financialSummary
    ) {
    }

    public function data(User $user): array
    {
        $performances = $this->performancesFor(
            $user
        );

        $totals = [
            'earnings' => 0.0,
            'bonuses' => 0.0,
            'penalties' => 0.0,
            'deductions' => 0.0,
            'net' => 0.0,
            'model_share' => 0.0,
            'studio_share' => 0.0,
        ];

        foreach (
            $performances as $performance
        ) {
            $summary =
                $this->financialSummary
                    ->summary($performance);

            $totals['earnings'] +=
                (float) $summary['gross_usd'];

            $totals['bonuses'] +=
                (float) $summary['bonus_usd'];

            $totals['penalties'] +=
                (float) $summary['penalty_usd'];

            $totals['deductions'] +=
                (float) $summary['deduction_usd'];

            $totals['net'] +=
                (float) $summary['net_usd'];

            $totals['model_share'] +=
                (float) $summary['model_share_usd'];

            $totals['studio_share'] +=
                (float) $summary['studio_share_usd'];
        }

        return [
            'total_models' =>
                $performances->count(),

            'totals' => [
                'earnings' =>
                    $this->money(
                        $totals['earnings']
                    ),

                'bonuses' =>
                    $this->money(
                        $totals['bonuses']
                    ),

                'penalties' =>
                    $this->money(
                        $totals['penalties']
                    ),

                'deductions' =>
                    $this->money(
                        $totals['deductions']
                    ),
            ],

            'net_balance' =>
                $this->money(
                    $totals['net']
                ),

            'distribution' => [
                'models' =>
                    $this->money(
                        $totals['model_share']
                    ),

                'studio' =>
                    $this->money(
                        $totals['studio_share']
                    ),
            ],
        ];
    }

    private function performancesFor(
        User $user
    ): Collection {
        $query = Performance::query()
            ->with(
                self::FINANCIAL_RELATIONS
            );

        if ($user->hasRole('Super Admin')) {
            return $query->get();
        }

        if (
            $user->hasRole('Admin') ||
            $user->hasRole('Monitor')
        ) {
            return $query
                ->where(
                    'studio_id',
                    $user->studio_id
                )
                ->get();
        }

        if ($user->hasRole('Performance')) {
            return $query
                ->where(
                    'user_id',
                    $user->id
                )
                ->get();
        }

        return new Collection();
    }

    private function money(
        float $value
    ): float {
        return round(
            $value,
            2
        );
    }
}
