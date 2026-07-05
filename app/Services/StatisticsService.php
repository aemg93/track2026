<?php

namespace App\Services;

use App\Models\Performance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StatisticsService
{
    private const CACHE_VERSION = 'v6';

    public function __construct(
        private FinancialSummaryService $financialSummary
    ) {
    }

    public function performanceStats(
        Performance $performance
    ): array {

        return [

            'today' => $this->cached(
                $performance,
                'today',
                Carbon::today(),
                Carbon::today()
            ),

            'weekly' => $this->cached(
                $performance,
                'weekly',
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ),

            'biweekly' => $this->cached(
                $performance,
                'biweekly',
                Carbon::now()->subDays(14),
                Carbon::now()
            ),

            'monthly' => $this->cached(
                $performance,
                'monthly',
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ),

        ];
    }

    private function cached(
        Performance $performance,
        string $range,
        Carbon $start,
        Carbon $end
    ): array {

        return Cache::remember(

            $this->cacheKey(
                $performance->id,
                $range
            ),

            now()->addMinutes(10),

            fn () => $this->financialSummary
                ->summaryBetween(
                    $performance,
                    $start,
                    $end
                )

        );
    }

    private function cacheKey(
        int $performanceId,
        string $range
    ): string {

        return sprintf(
            'stats:%s:performance:%d:%s',
            self::CACHE_VERSION,
            $performanceId,
            $range
        );
    }
}