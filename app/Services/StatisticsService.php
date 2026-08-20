<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Performance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StatisticsService
{
    private const CACHE_VERSION = 'v7';

    public function __construct(
        private FinancialSummaryService $financialSummary
    ) {
    }

    public function performanceStats(
        Performance $performance
    ): array {
        $now = Carbon::now();

        return [
            'today' => $this->cached(
                $performance,
                'today',
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay()
            ),

            'weekly' => $this->cached(
                $performance,
                'weekly',
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek()
            ),

            'biweekly' => $this->cached(
                $performance,
                'biweekly',
                $now->copy()->subDays(13)->startOfDay(),
                $now->copy()->endOfDay()
            ),

            'monthly' => $this->cached(
                $performance,
                'monthly',
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth()
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
            fn (): array => $this->financialSummary->summaryBetween(
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