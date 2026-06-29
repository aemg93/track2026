<?php

namespace App\Observers;

use App\Models\Bonus;
use App\Services\FinancialSyncDispatcher;

class BonusObserver
{
    public function __construct(
        private FinancialSyncDispatcher $dispatcher
    ) {}

    public function created(
        Bonus $bonus
    ): void {

        $this->dispatcher
            ->dispatchPerformanceId(
                $bonus->performance_id
            );
    }

    public function updated(
        Bonus $bonus
    ): void {

        if (! $bonus->wasChanged([
            'amount',
            'date',
            'performance_id',
        ])) {
            return;
        }

        $this->dispatcher
            ->dispatchPerformanceId(
                $bonus->performance_id
            );
    }

    public function deleted(
        Bonus $bonus
    ): void {

        $this->dispatcher
            ->dispatchPerformanceId(
                $bonus->performance_id
            );
    }
}