<?php

namespace App\Services;

use App\Models\Performance;
use Illuminate\Support\Facades\Cache;

class PerformanceFinancialService
{
    public function calculate(Performance $performance): array
    {
        return Cache::remember(
            "performance:financials:{$performance->id}",
            now()->addMinutes(10),
            function () use ($performance) {

                /*
                |--------------------------------------------------------------------------
                | INGRESOS REALES (PERFORMANCE_PLATFORM)
                |--------------------------------------------------------------------------
                */

                $earnings = (float) $performance
                    ->platforms()
                    ->sum('performance_platform.earnings_usd');

                /*
                |--------------------------------------------------------------------------
                | AJUSTES FINANCIEROS
                |--------------------------------------------------------------------------
                */

                $bonuses = (float) $performance
                    ->bonuses()
                    ->sum('amount');

                $penalties = (float) $performance
                    ->penalties()
                    ->sum('amount');

                $deductions = (float) $performance
                    ->deductions()
                    ->sum('amount');

                /*
                |--------------------------------------------------------------------------
                | BALANCE NETO
                |--------------------------------------------------------------------------
                */

                $net = ($earnings + $bonuses)
                    - ($penalties + $deductions);

                return [
                    'earnings' => $earnings,
                    'bonuses' => $bonuses,
                    'penalties' => $penalties,
                    'deductions' => $deductions,
                    'net' => (float) $net,
                ];
            }
        );
    }

    public function raw(Performance $performance): array
    {
        return [
            'platforms' => $performance->platforms,
            'bonuses' => $performance->bonuses,
            'penalties' => $performance->penalties,
            'deductions' => $performance->deductions,
        ];
    }

    public function clear(Performance $performance): void
    {
        Cache::forget(
            "performance:financials:{$performance->id}"
        );
    }
}