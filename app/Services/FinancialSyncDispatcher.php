<?php

namespace App\Services;

use App\Jobs\SyncEarningsJob;
use App\Models\Performance;

class FinancialSyncDispatcher
{
    public function dispatchPerformance(
        Performance $performance
    ): void {
        $this->dispatchPerformanceId(
            $performance->id
        );
    }

    public function dispatchPerformanceId(
        int $performanceId
    ): void {
        SyncEarningsJob::dispatch(
            $performanceId
        );
    }
}