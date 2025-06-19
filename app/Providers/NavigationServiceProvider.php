<?php

namespace App\Providers;

use App\Services\NavigationService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class NavigationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the NavigationService as a singleton
        $this->app->singleton(NavigationService::class, function ($app) {
            return new NavigationService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share navigation data with admin views
        View::composer([
            'components.admin.*', 
            'admin.*',
            'components.layouts.admin'
        ], function ($view) {
            // Only load navigation data if user is authenticated
            if (Auth::check()) {
                $navigationService = app(NavigationService::class);
                
                try {
                    $view->with([
                        'adminNavigation' => $navigationService->getFilteredAdminNavigation(),
                        'adminQuickActions' => $navigationService->getAdminQuickActions(),
                        'breadcrumbs' => $navigationService->getBreadcrumbs(),
                    ]);
                } catch (\Exception $e) {
                    // Fallback in case of errors
                    $view->with([
                        'adminNavigation' => [],
                        'adminQuickActions' => [],
                        'breadcrumbs' => [],
                    ]);
                }
            }
        });

        // Share navigation data with client views
        View::composer([
            'components.client.*', 
            'client.*',
            'components.layouts.client'
        ], function ($view) {
            // Only load navigation data if user is authenticated
            if (Auth::check()) {
                $navigationService = app(NavigationService::class);
                
                try {
                    $view->with([
                        'clientNavigation' => $navigationService->getFilteredClientNavigation(),
                        'clientQuickActions' => $navigationService->getClientQuickActions(),
                        'breadcrumbs' => $navigationService->getBreadcrumbs(),
                    ]);
                } catch (\Exception $e) {
                    // Fallback in case of errors
                    $view->with([
                        'clientNavigation' => [],
                        'clientQuickActions' => [],
                        'breadcrumbs' => [],
                    ]);
                }
            }
        });

        // Share with specific layout components that need navigation
        View::composer([
            'components.admin.admin-sidebar',
            'components.admin.admin-header',
            'components.client.client-sidebar',
            'components.client.client-header',
        ], function ($view) {
            if (Auth::check()) {
                $navigationService = app(NavigationService::class);
                
                try {
                    // Determine if this is admin or client context
                    $isAdmin = request()->is('admin/*') || request()->routeIs('admin.*');
                    
                    if ($isAdmin) {
                        $view->with([
                            'navigation' => $navigationService->getFilteredAdminNavigation(),
                            'quickActions' => $navigationService->getAdminQuickActions(),
                        ]);
                    } else {
                        $view->with([
                            'navigation' => $navigationService->getFilteredClientNavigation(),
                            'quickActions' => $navigationService->getClientQuickActions(),
                        ]);
                    }
                    
                    $view->with('breadcrumbs', $navigationService->getBreadcrumbs());
                    
                } catch (\Exception $e) {
                    \Log::warning('Error loading navigation data in view composer', [
                        'error' => $e->getMessage(),
                        'user_id' => Auth::id(),
                    ]);
                    
                    $view->with([
                        'navigation' => [],
                        'quickActions' => [],
                        'breadcrumbs' => [],
                    ]);
                }
            }
        });
    }
}