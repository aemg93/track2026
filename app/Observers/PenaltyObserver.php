<?php

namespace App\Observers;

use App\Models\Penalty;
use App\Services\FinancialSyncDispatcher;

class PenaltyObserver
{
    public function __construct(
        private FinancialSyncDispatcher $dispatcher
    ) {}

    public function created(
        Penalty $penalty
    ): void {

        $this->dispatcher
            ->dispatchPerformanceId(
                $penalty->performance_id
            );
    }

    public function updated(
        Penalty $penalty
    ): void {

        if (! $penalty->wasChanged([
            'amount',
            'date',
            'performance_id',
        ])) {
            return;
        }

        $this->dispatcher
            ->dispatchPerformanceId(
                $penalty->performance_id
            );
    }

    public function deleted(
        Penalty $penalty
    ): void {

        $this->dispatcher
            ->dispatchPerformanceId(
                $penalty->performance_id
            );
    }
}