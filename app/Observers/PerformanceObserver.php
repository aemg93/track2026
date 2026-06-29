<?php

namespace App\Observers;

use App\Models\Performance;
use App\Services\FinancialSyncDispatcher;

class PerformanceObserver
{
    public function __construct(
        private FinancialSyncDispatcher $dispatcher
    ) {}

    public function created(
        Performance $performance
    ): void {

        $this->dispatcher
            ->dispatchPerformance(
                $performance
            );
    }

    public function updated(
        Performance $performance
    ): void {

        if (! $performance->wasChanged([
            'hours_streamed',
            'ranking_score',
        ])) {
            return;
        }

        $this->dispatcher
            ->dispatchPerformance(
                $performance
            );
    }

    public function deleted(
        Performance $performance
    ): void
    {
        //
    }

    public function restored(
        Performance $performance
    ): void
    {
        //
    }

    public function forceDeleted(
        Performance $performance
    ): void
    {
        //
    }
}