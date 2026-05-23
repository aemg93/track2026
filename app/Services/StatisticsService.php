<?php

namespace App\Services;

use App\Models\Performance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StatisticsService
{
    public function performanceStats(Performance $performance): array
    {
        return [
            'today' => $this->cached($performance, 'today', fn () =>
                $this->sumRange(
                    $performance,
                    Carbon::today()->startOfDay(),
                    Carbon::today()->endOfDay()
                )
            ),

            'weekly' => $this->cached($performance, 'weekly', fn () =>
                $this->sumRange(
                    $performance,
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                )
            ),

            'biweekly' => $this->cached($performance, 'biweekly', fn () =>
                $this->sumRange(
                    $performance,
                    Carbon::now()->subDays(15)->startOfDay(),
                    Carbon::now()->endOfDay()
                )
            ),

            'monthly' => $this->cached($performance, 'monthly', fn () =>
                $this->sumRange(
                    $performance,
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                )
            ),
        ];
    }

    /**
     * CACHE LAYER
     */
    private function cached(Performance $performance, string $range, callable $callback)
    {
        $key = $this->cacheKey($performance->id, $range);

        return Cache::remember(
            $key,
            now()->addMinutes(10),
            $callback
        );
    }

    /**
     * CENTRAL CACHE KEY
     */
    private function cacheKey(int $performanceId, string $range): string
    {
        return "stats:v1:performance:{$performanceId}:{$range}";
    }

    /**
     * CORE CALCULATION
     */
    private function sumRange(
        Performance $performance,
        Carbon $start,
        Carbon $end
    ): array {

        $startDate = $start->toDateString();
        $endDate   = $end->toDateString();

        $earnings = $performance->earnings()
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount_usd');

        $bonuses = $performance->bonuses()
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $penalties = $performance->penalties()
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $gross = $earnings + $bonuses - $penalties;

        $split = app(SplitService::class)
            ->calculate($performance, $gross);

        return [
            'gross_usd' => round($gross, 2),

            'model_percentage' => $split['model_percentage'],
            'studio_percentage' => $split['studio_percentage'],

            'model_usd' => round($split['model_usd'], 2),
            'studio_usd' => round($split['studio_usd'], 2),
        ];
    }
}