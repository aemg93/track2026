<?php

namespace App\Services;

use App\Models\User;
use App\Models\Performance;

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


        default =>
            [],
    };


    return [

        'dashboard' => $dashboard,


        'finance' =>
            $this->financeData(),

    ];
}

    private function superAdminData(): array
    {
        $performances = Performance::query()

            ->select([
                'id',
                'user_id',
                'first_name',
                'last_name',
                'nickname',
                'ranking_score',
                'hours_streamed',
            ])

            ->with('user:id,name,email')

            ->get();



        $ranking = Performance::query()

            ->select([
                'id',
                'first_name',
                'last_name',
                'nickname',
                'ranking_score',
                'hours_streamed',
            ])

            ->orderByDesc('ranking_score')

            ->limit(10)

            ->get();



        return [

            'view' => 'super_admin',

            'total_models' =>
                $performances->count(),


            'ranking' =>
                $ranking,


            'models' =>
                $performances->map(
                    fn ($performance) =>
                    $this->formatPerformance(
                        $performance
                    )
                ),

        ];
    }



    private function studioData(
        User $user,
        string $view
    ): array {

        $performances = Performance::query()

            ->where(
                'studio_id',
                $user->studio_id
            )

            ->select([
                'id',
                'first_name',
                'last_name',
                'nickname',
                'ranking_score',
                'hours_streamed',
            ])

            ->get();



        $ranking = Performance::query()

            ->where(
                'studio_id',
                $user->studio_id
            )

            ->select([
                'id',
                'first_name',
                'last_name',
                'nickname',
                'ranking_score',
                'hours_streamed',
            ])

            ->orderByDesc('ranking_score')

            ->limit(10)

            ->get();



        return [

            'view' => $view,


            'total_models' =>
                $performances->count(),


            'ranking' =>
                $ranking,


            'models' =>
                $performances->map(
                    fn ($performance) =>
                    $this->formatPerformance(
                        $performance,
                        $view
                    )
                ),

        ];
    }



    private function performanceData(
        User $user
    ): array {


        $performance =
            $user->performance;



        if (!$performance) {

            return [

                'view'=>'performance',

                'message'=>'Performance no asociada',

            ];
        }



        return [

            'view'=>'performance',


            'id'=>$performance->id,


            'name'=>trim(
                $performance->first_name .
                ' ' .
                $performance->last_name
            ),


            'ranking'=>
                $performance->ranking_score,


            'hours'=>
                $performance->hours_streamed,


            'statistics'=>
                $this->statistics
                    ->performanceStats(
                        $performance
                    ),

        ];
    }



    private function formatPerformance(
        Performance $performance,
        ?string $view = null
    ): array {


        $stats =
            $this->statistics
                ->performanceStats(
                    $performance
                );



        return [

            'id'=>$performance->id,


            'name'=>trim(
                $performance->first_name .
                ' ' .
                $performance->last_name
            ),


            'nickname'=>
                $performance->nickname,


            'ranking'=>
                $performance->ranking_score,


            'hours'=>
                $performance->hours_streamed,



            'statistics' =>

                $view === 'monitor'

                ?

                [

                    'today'=>[

                        'gross_usd'=>
                            $stats['today']['gross_usd'] ?? 0,


                        'model_usd'=>
                            $stats['today']['model_usd'] ?? 0,

                    ],


                    'weekly'=>[

                        'gross_usd'=>
                            $stats['weekly']['gross_usd'] ?? 0,


                        'model_usd'=>
                            $stats['weekly']['model_usd'] ?? 0,

                    ],

                ]

                :

                $stats,

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

    $earnings = 0;
    $bonuses = 0;
    $penalties = 0;
    $deductions = 0;
    $net = 0;
    $modelShare = 0;
    $studioShare = 0;
    $pending = 0;

    foreach ($performances as $performance) {

        $summary = $this->financialSummary
            ->summary($performance);

        $earnings += $summary['gross_usd'];

        $bonuses += $summary['bonus_usd'];

        $penalties += $summary['penalty_usd'];

        $deductions += $summary['deduction_usd'];

        $net += $summary['net_usd'];

        $modelShare += $summary['model_share_usd'];

        $studioShare += $summary['studio_share_usd'];

        $pending += $performance
            ->earnings()
            ->where('status', 'pending')
            ->sum('gross_usd');
    }

    return [

        'totals' => [

            'earnings' => round($earnings, 2),

            'bonuses' => round($bonuses, 2),

            'penalties' => round($penalties, 2),

            'deductions' => round($deductions, 2),

        ],

        'net_balance' => round(
            $net,
            2
        ),

        'distribution' => [

            'models' => round(
                $modelShare,
                2
            ),

            'studio' => round(
                $studioShare,
                2
            ),

        ],

        'installments' => [

            'active' => 0,

            'pending_amount' => round(
                $pending,
                2
            ),

        ],

    ];
}
    
}