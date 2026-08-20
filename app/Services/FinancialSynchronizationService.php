<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Performance;
use Illuminate\Support\Facades\Cache;

class FinancialSynchronizationService
{
    private const CACHE_VERSION = 'v6';

    private const STATISTICS_RANGES = [
        'today',
        'weekly',
        'biweekly',
        'monthly',
    ];

    public function __construct(
        private EarningService $earningService
    ) {
    }

    /**
     * Recalcula los valores derivados de los earnings
     * y limpia las estadísticas financieras cacheadas.
     *
     * Los bonos, penalizaciones y deducciones NO se
     * distribuyen entre earnings. Se calculan a nivel
     * de Performance mediante FinancialSummaryService.
     */
    public function synchronizePerformance(
        Performance $performance
    ): void {
        $performance
            ->earnings()
            ->each(
                function (Earning $earning): void {
                    $this->earningService
                        ->syncEarning($earning);
                }
            );

        $this->clearStatisticsCache(
            $performance->id
        );
    }

    private function clearStatisticsCache(
        int $performanceId
    ): void {
        foreach (self::STATISTICS_RANGES as $range) {
            Cache::forget(
                sprintf(
                    'stats:%s:performance:%d:%s',
                    self::CACHE_VERSION,
                    $performanceId,
                    $range
                )
            );
        }
    }
}