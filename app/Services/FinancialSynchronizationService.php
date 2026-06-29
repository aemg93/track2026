<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Performance;

class FinancialSynchronizationService
{
    public function __construct(
        private EarningService $earningService
    ) {}

    public function synchronizePerformance(
        Performance $performance
    ): void {

        $performance
            ->earnings()
            ->get()
            ->each(function (Earning $earning): void {

                $this->earningService
                    ->syncEarning($earning);

            });
    }
}