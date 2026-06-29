<?php

namespace App\Observers;

use App\Models\Deduction;
use App\Services\FinancialSyncDispatcher;

class DeductionObserver
{
    public function __construct(
        private FinancialSyncDispatcher $dispatcher
    ) {}

    public function created(
        Deduction $deduction
    ): void {

        $this->dispatcher
            ->dispatchPerformanceId(
                $deduction->performance_id
            );
    }

    public function updated(
        Deduction $deduction
    ): void {

        if (! $deduction->wasChanged([
            'amount',
            'date',
            'performance_id',
            'installment_value',
            'installments',
            'is_installment',
        ])) {
            return;
        }

        $this->dispatcher
            ->dispatchPerformanceId(
                $deduction->performance_id
            );
    }

    public function deleted(
        Deduction $deduction
    ): void {

        $this->dispatcher
            ->dispatchPerformanceId(
                $deduction->performance_id
            );
    }
}