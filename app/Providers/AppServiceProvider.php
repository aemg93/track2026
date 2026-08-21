<?php

namespace App\Providers;

use App\Observers\PerformanceObserver;
use App\Observers\BonusObserver;
use App\Observers\PenaltyObserver;
use App\Observers\DeductionObserver;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\{User, Studio, Performance, Earning, Bonus, Deduction, Penalty};
use App\Policies\{UserPolicy, StudioPolicy, PerformancePolicy, EarningPolicy, BonusPolicy, DeductionPolicy, PenaltyPolicy};

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Studio::class, StudioPolicy::class);
        Gate::policy(Performance::class, PerformancePolicy::class);
        Gate::policy(Earning::class, EarningPolicy::class);
        Gate::policy(Bonus::class, BonusPolicy::class);
        Gate::policy(Deduction::class, DeductionPolicy::class);
        Gate::policy(Penalty::class, PenaltyPolicy::class);
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
