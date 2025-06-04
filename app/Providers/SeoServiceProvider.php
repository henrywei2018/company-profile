<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class SeoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register SEO view components
        Blade::componentNamespace('App\\View\\Components\\Seo', 'seo');
        
        // Share global SEO data with all views
        View::composer('*', function ($view) {
            // Don't add SEO to admin views
            if (!request()->is('admin/*')) {
                $view->with('globalSeo', [
                    'siteName' => settings('site_name', config('app.name')),
                    'siteDescription' => settings('site_description', ''),
                    'contactEmail' => settings('contact_email'),
                    'contactPhone' => settings('contact_phone'),
                ]);
            }
        });
        
        // Add SEO helper to all views
        View::share('seoHelper', new \App\Helpers\SeoHelper());
    }
}