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
            60,
            function () use ($performance) {

                $earnings = $performance->earnings->sum(fn ($e) =>
                    $e->amount_usd ?? $e->amount ?? 0
                );

                $bonuses = $performance->bonuses->sum('amount');

                $penalties = $performance->penalties->sum('amount');

                return [
                    'earnings' => (float) $earnings,
                    'bonuses' => (float) $bonuses,
                    'penalties' => (float) $penalties,
                    'deductions' => 0,
                ];
            }
        );
    }

    public function raw(Performance $performance): array
    {
        return [
            'earnings' => $performance->earnings,
            'bonuses' => $performance->bonuses,
            'penalties' => $performance->penalties,
        ];
    }

    public function clear(Performance $performance): void
    {
        Cache::forget("performance:financials:{$performance->id}");
    }
}