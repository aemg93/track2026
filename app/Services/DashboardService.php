<?php

namespace App\Services;

use App\Models\User;
use App\Models\Performance;
use App\Models\Earning;

class DashboardService
{
    public function __construct(
        private StatisticsService $statistics
    ) {}



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
    $earnings = Earning::query();


    return [

        'totals' => [

            'earnings' =>
                round(
                    (float) $earnings->sum('gross_usd'),
                    2
                ),


            'bonuses' =>
                round(
                    (float) $earnings->sum('bonus_usd'),
                    2
                ),


            'penalties' =>
                round(
                    (float) $earnings->sum('penalty_usd'),
                    2
                ),


            'deductions' =>
                round(
                    (float) $earnings->sum('deduction_usd'),
                    2
                ),

        ],


        'net_balance' =>
            round(
                (float) Earning::sum('net_usd'),
                2
            ),


        'installments'=>[

            'active'=>0,

            'pending_amount'=>
                (float) Earning::where(
                    'status',
                    'pending'
                )
                ->sum('net_usd'),

        ],

    ];
}
    
}