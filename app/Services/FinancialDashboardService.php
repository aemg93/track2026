<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\User;

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
    ) {}





    public function data(User $user): array
    {

        $totals = [

            'earnings'=>0,
            'bonuses'=>0,
            'penalties'=>0,
            'deductions'=>0,
            'net'=>0,
            'model_share'=>0,
            'studio_share'=>0,
            'pending'=>0,

        ];



        $query =
            Performance::with(
                self::FINANCIAL_RELATIONS
            );



        if (! $user->hasRole('Super Admin')) {

            $query->where(
                'studio_id',
                $user->studio_id
            );

        }



        foreach($query->get() as $performance){


            $summary =
                $this->financialSummary
                    ->summary($performance);



            $totals['earnings'] +=
                $summary['gross_usd'];


            $totals['bonuses'] +=
                $summary['bonus_usd'];


            $totals['penalties'] +=
                $summary['penalty_usd'];


            $totals['deductions'] +=
                $summary['deduction_usd'];


            $totals['net'] +=
                $summary['net_usd'];


            $totals['model_share'] +=
                $summary['model_share_usd'];


            $totals['studio_share'] +=
                $summary['studio_share_usd'];

        }



        return [

            'totals'=>[

                'earnings'=>
                    round($totals['earnings'],2),

                'bonuses'=>
                    round($totals['bonuses'],2),

                'penalties'=>
                    round($totals['penalties'],2),

                'deductions'=>
                    round($totals['deductions'],2),

            ],


            'net_balance'=>
                round($totals['net'],2),


            'distribution'=>[

                'models'=>
                    round($totals['model_share'],2),


                'studio'=>
                    round($totals['studio_share'],2),

            ],

        ];
    }
}