<?php

namespace App\Providers;

use App\Services\GoogleAnalyticsService;
use App\Services\AnalyticsDashboardService;
use Illuminate\Support\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register GoogleAnalyticsService as singleton
        $this->app->singleton(GoogleAnalyticsService::class, function ($app) {
            return new GoogleAnalyticsService();
        });

        // Register AnalyticsDashboardService as singleton
        $this->app->singleton(AnalyticsDashboardService::class, function ($app) {
            return new AnalyticsDashboardService(
                $app->make(GoogleAnalyticsService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}