<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Performance;
use Illuminate\Support\Facades\Cache;

class FinancialSynchronizationService
{
    private const CACHE_VERSION = 'v4';

    public function __construct(
        private EarningService $earningService
    ) {
    }

    public function synchronizePerformance(
        Performance $performance
    ): void {

        $performance
            ->earnings()
            ->each(function (Earning $earning): void {

                $this->earningService
                    ->syncEarning($earning);

            });

        $this->clearStatisticsCache(
            $performance->id
        );

    }

    private function clearStatisticsCache(
        int $performanceId
    ): void {

        foreach (
            ['today', 'weekly', 'biweekly', 'monthly']
            as $range
        ) {

            Cache::forget(
                "stats:" .
                self::CACHE_VERSION .
                ":performance:{$performanceId}:{$range}"
            );

        }

    }
}