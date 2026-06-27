<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Earning;
use App\Services\EarningService;

class SyncEarningsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $service = app(EarningService::class);

        Earning::with('performance')
            ->get()
            ->each(function ($earning) use ($service) {
                $service->syncEarningTotals($earning);
            });
    }
}