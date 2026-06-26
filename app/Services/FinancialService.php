<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\User;

class FinancialService
{
    public function getSummary(User $user): array
    {

        $earnings = Earning::query();



        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Super Admin')) {

            // acceso global

        }



        /*
        |--------------------------------------------------------------------------
        | PERFORMANCE
        |--------------------------------------------------------------------------
        */

        elseif ($user->hasRole('Performance')) {


            $earnings->whereHas(
                'performance',
                function ($q) use ($user) {

                    $q->where(
                        'user_id',
                        $user->id
                    );

                }
            );

        }



        /*
        |--------------------------------------------------------------------------
        | MONITOR
        |--------------------------------------------------------------------------
        */

        elseif ($user->hasRole('Monitor')) {


            /*
            |--------------------------------------------------------------------------
            | Pendiente:
            | definir tabla de asignación
            |
            | ejemplo:
            | monitor_performance
            |--------------------------------------------------------------------------
            */


            $earnings->whereRaw('1 = 0');

        }



        /*
        |--------------------------------------------------------------------------
        | TOTALES FINANCIEROS
        |--------------------------------------------------------------------------
        */


        $totals = [

            'gross' => (float)
                (clone $earnings)
                ->sum('gross_usd'),


            'bonus' => (float)
                (clone $earnings)
                ->sum('bonus_usd'),


            'penalty' => (float)
                (clone $earnings)
                ->sum('penalty_usd'),


            'deduction' => (float)
                (clone $earnings)
                ->sum('deduction_usd'),


            'net' => (float)
                (clone $earnings)
                ->sum('net_usd'),

        ];



        /*
        |--------------------------------------------------------------------------
        | ESTADOS DE PAGO
        |--------------------------------------------------------------------------
        */


        $payments = [

            'paid' => (float)
                (clone $earnings)
                ->where(
                    'status',
                    'paid'
                )
                ->sum('net_usd'),



            'pending' => (float)
                (clone $earnings)
                ->whereIn(
                    'status',
                    [
                        'draft',
                        'pending',
                        'approved'
                    ]
                )
                ->sum('net_usd'),

        ];



        return [

            'totals' => $totals,

            'payments' => $payments,

        ];

    }
}