<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\Bonus;
use App\Models\Penalty;
use App\Models\Deduction;

class FinancialService
{
    public function getSummary($user): array
    {
        /*
        |--------------------------------------------------------------------------
        | BASE QUERIES
        |--------------------------------------------------------------------------
        */

        $performances = Performance::query();

        $bonuses = Bonus::query();
        $penalties = Penalty::query();
        $deductions = Deduction::query();

        /*
        |--------------------------------------------------------------------------
        | MULTI TENANT
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Admin')) {

            $performances->where(
                'studio_id',
                $user->studio_id
            );

            $bonuses->whereHas('performance', fn ($q) =>
                $q->where('studio_id', $user->studio_id)
            );

            $penalties->whereHas('performance', fn ($q) =>
                $q->where('studio_id', $user->studio_id)
            );

            $deductions->whereHas('performance', fn ($q) =>
                $q->where('studio_id', $user->studio_id)
            );
        }

        if ($user->hasRole('Performance')) {

            $performances->where(
                'user_id',
                $user->id
            );

            $bonuses->where(
                'user_id',
                $user->id
            );

            $penalties->where(
                'user_id',
                $user->id
            );

            $deductions->where(
                'user_id',
                $user->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | INGRESOS REALES (SIN N+1)
        |--------------------------------------------------------------------------
        */

        $totalEarnings = (float) $performances
            ->withSum(
                'platforms as total_usd',
                'performance_platform.earnings_usd'
            )
            ->get()
            ->sum('total_usd');

        /*
        |--------------------------------------------------------------------------
        | AJUSTES
        |--------------------------------------------------------------------------
        */

        $totalBonuses = (float) $bonuses
            ->sum('amount');

        $totalPenalties = (float) $penalties
            ->sum('amount');

        $totalDeductions = (float) $deductions
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | CUOTAS
        |--------------------------------------------------------------------------
        */

        $activeInstallments = (clone $deductions)
            ->where('is_installment', true)
            ->count();

        $pendingInstallments = (clone $deductions)
            ->where('is_installment', true)
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | BALANCE NETO
        |--------------------------------------------------------------------------
        */

        $netBalance =
            ($totalEarnings + $totalBonuses)
            - ($totalPenalties + $totalDeductions);

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return [

            'totals' => [

                'earnings' => $totalEarnings,

                'bonuses' => $totalBonuses,

                'penalties' => $totalPenalties,

                'deductions' => $totalDeductions,
            ],

            'installments' => [

                'active' => $activeInstallments,

                'pending_amount' => (float) $pendingInstallments,
            ],

            'net_balance' => (float) $netBalance,
        ];
    }
}