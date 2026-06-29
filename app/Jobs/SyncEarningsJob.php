<?php

namespace App\Jobs;

use App\Models\Performance;
use App\Services\FinancialSynchronizationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncEarningsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $performanceId
    ) {}

    public function handle(
        FinancialSynchronizationService $financialSynchronizationService
    ): void {

        $performance = Performance::find($this->performanceId);

        if (!$performance) {
            return;
        }

        $financialSynchronizationService
            ->synchronizePerformance($performance);
    }
}