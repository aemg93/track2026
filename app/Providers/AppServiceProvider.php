<?php

namespace App\Providers;

use App\Models\Performance;
use App\Models\Bonus;
use App\Models\Penalty;
use App\Models\Deduction;

use App\Observers\PerformanceObserver;
use App\Observers\BonusObserver;
use App\Observers\PenaltyObserver;
use App\Observers\DeductionObserver;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Performance::observe(
            PerformanceObserver::class
        );

        Bonus::observe(
            BonusObserver::class
        );

        Penalty::observe(
            PenaltyObserver::class
        );

        Deduction::observe(
            DeductionObserver::class
        );
    }
}