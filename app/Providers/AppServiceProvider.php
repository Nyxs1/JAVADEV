<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\ItemEvidence;
use App\Models\Portfolio;
use App\Models\PortfolioScreenshot;
use App\Policies\EventPolicy;
use App\Policies\ItemEvidencePolicy;
use App\Policies\PortfolioPolicy;
use App\Policies\PortfolioScreenshotPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register User Observer
        \App\Models\User::observe(\App\Providers\Observers\UserObserver::class);

        // Register Policies
        Gate::policy(Event::class, EventPolicy::class);
        Gate::policy(Portfolio::class, PortfolioPolicy::class);
        Gate::policy(PortfolioScreenshot::class, PortfolioScreenshotPolicy::class);
        Gate::policy(ItemEvidence::class, ItemEvidencePolicy::class);
    }
}
