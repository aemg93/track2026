<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PerformanceRecalculationRequested;
use App\Listeners\RecalculatePerformanceRanking;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PerformanceRecalculationRequested::class => [
            RecalculatePerformanceRanking::class,
        ],
    ];
}