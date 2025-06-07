<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FilePondService;

class FilePondServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FilePondService::class, function ($app) {
            return new FilePondService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\CleanupFilePondTempFiles::class,
            ]);
        }
    }
}