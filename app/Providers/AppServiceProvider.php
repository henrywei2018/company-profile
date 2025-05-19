<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyProfile;
use App\Models\Message;
use App\Models\Quotation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\FileUploadService::class, function ($app) {
            return new \App\Services\FileUploadService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL older than 5.7.7
        Schema::defaultStringLength(191);
        
        // Global view composers for admin views
        View::composer(['admin.*', 'layouts.admin', 'components.admin.admin-header', 'components.admin.admin-sidebar'], function ($view) {
            // Only fetch notification counts when user is authenticated
            if (Auth::check()) {
                $view->with([
                    'unreadMessages' => Message::unread()->count(),
                    'pendingQuotations' => Quotation::pending()->count(),
                    'companyProfile' => CompanyProfile::getInstance()
                ]);
            } else {
                $view->with([
                    'unreadMessages' => 0,
                    'pendingQuotations' => 0,
                    'companyProfile' => CompanyProfile::getInstance()
                ]);
            }
        });
    }
}