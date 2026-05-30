<?php

namespace App\Services;

use App\Models\Earning;
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

        $earnings = Earning::query();
        $bonuses = Bonus::query();
        $penalties = Penalty::query();
        $deductions = Deduction::query();

        /*
        |--------------------------------------------------------------------------
        | SCOPES (SEGURIDAD + MULTI-TENANT)
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Admin')) {

            $earnings->whereHas('performance', fn ($q) =>
                $q->where('studio_id', $user->studio_id)
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

            $earnings->where('user_id', $user->id);
            $bonuses->where('user_id', $user->id);
            $penalties->where('user_id', $user->id);
            $deductions->where('user_id', $user->id);
        }

        /*
        |--------------------------------------------------------------------------
        | TOTALS
        |--------------------------------------------------------------------------
        */

        $totalEarnings = (float) $earnings->sum('amount');
        $totalBonuses = (float) $bonuses->sum('amount');
        $totalPenalties = (float) $penalties->sum('amount');
        $totalDeductions = (float) $deductions->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | INSTALLMENTS (CORREGIDO - RESPETA SCOPES)
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
        | NET BALANCE (FORMULA CLARA)
        |--------------------------------------------------------------------------
        */

        $netBalance = ($totalEarnings + $totalBonuses)
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
                'pending_amount' => $pendingInstallments,
            ],

            'net_balance' => $netBalance,
        ];
    }
}