<?php

namespace App\Listeners;

use App\Events\PerformanceRecalculationRequested;
use App\Services\RankingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecalculatePerformanceRanking implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;
    public int $timeout = 10;

    public function handle(PerformanceRecalculationRequested $event): void
    {
        app(RankingService::class)
            ->recalculate($event->performanceId);
    }

    public function failed(PerformanceRecalculationRequested $event, \Throwable $exception): void
    {
        logger()->error('Ranking recalculation failed', [
            'performance_id' => $event->performanceId,
            'error' => $exception->getMessage(),
        ]);
    }
}