<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Services\ClientAccessService;
use App\Services\ClientNotificationService;
use App\Services\ClientDashboardService;

/**
 * Client Service Provider
 * 
 * This provider handles all client-specific service registrations,
 * route bindings, and view composers for the client area.
 */
class ClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register client access service
        $this->app->singleton(ClientAccessService::class, function ($app) {
            return new ClientAccessService();
        });

        // Register client notification service
        $this->app->singleton(ClientNotificationService::class, function ($app) {
            return new ClientNotificationService();
        });

        // Register client dashboard service
        $this->app->singleton(ClientDashboardService::class, function ($app) {
            return new ClientDashboardService(
                $app->make(ClientAccessService::class)
            );
        });

        // Register client repositories
        $this->registerClientRepositories();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register client route model bindings
        $this->registerRouteBindings();
        
        // Register client-specific view composers
        $this->registerClientViewComposers();
        
        // Register client event listeners
        $this->registerClientEventListeners();
        
        // Load client-specific configurations
        $this->loadClientConfigurations();
    }

    /**
     * Register client repositories.
     */
    protected function registerClientRepositories(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\ClientProjectRepositoryInterface::class,
            \App\Repositories\ClientProjectRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\ClientQuotationRepositoryInterface::class,
            \App\Repositories\ClientQuotationRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\ClientMessageRepositoryInterface::class,
            \App\Repositories\ClientMessageRepository::class
        );
    }

    /**
     * Register route model bindings for client resources.
     */
    protected function registerRouteBindings(): void
    {
        // Bind client projects with access control
        Route::bind('clientProject', function ($value) {
            $project = \App\Models\Project::findOrFail($value);
            
            if (auth()->check()) {
                $clientService = app(ClientAccessService::class);
                if (!$clientService->canAccessResource(auth()->user(), 'project', $project->id)) {
                    abort(403, 'You do not have access to this project.');
                }
            }
            
            return $project;
        });

        // Bind client quotations with access control
        Route::bind('clientQuotation', function ($value) {
            $quotation = \App\Models\Quotation::findOrFail($value);
            
            if (auth()->check()) {
                $clientService = app(ClientAccessService::class);
                if (!$clientService->canAccessResource(auth()->user(), 'quotation', $quotation->id)) {
                    abort(403, 'You do not have access to this quotation.');
                }
            }
            
            return $quotation;
        });

        // Bind client messages with access control
        Route::bind('clientMessage', function ($value) {
            $message = \App\Models\Message::findOrFail($value);
            
            if (auth()->check()) {
                $clientService = app(ClientAccessService::class);
                if (!$clientService->canAccessResource(auth()->user(), 'message', $message->id)) {
                    abort(403, 'You do not have access to this message.');
                }
            }
            
            return $message;
        });
    }

    /**
     * Register client-specific view composers.
     */
    protected function registerClientViewComposers(): void
    {
        // Client navigation composer
        View::composer('layouts.client', function ($view) {
            if (auth()->check()) {
                $clientService = app(ClientAccessService::class);
                $view->with([
                    'clientNavigation' => $clientService->getClientNavigationMenu(auth()->user()),
                    'clientPermissions' => $clientService->getClientPermissions(auth()->user()),
                ]);
            }
        });

        // Client dashboard composer
        View::composer('client.dashboard', function ($view) {
            if (auth()->check()) {
                $dashboardService = app(ClientDashboardService::class);
                $view->with([
                    'dashboardData' => $dashboardService->getDashboardData(auth()->user()),
                    'recentActivities' => $dashboardService->getRecentActivities(auth()->user()),
                    'notifications' => $dashboardService->getNotifications(auth()->user()),
                ]);
            }
        });

        // Client project views composer
        View::composer('client.projects.*', function ($view) {
            if (auth()->check()) {
                $clientService = app(ClientAccessService::class);
                $view->with([
                    'projectStatuses' => $this->getProjectStatuses(),
                    'canCreateProjects' => auth()->user()->can('create projects'),
                ]);
            }
        });

        // Client quotation views composer
        View::composer('client.quotations.*', function ($view) {
            if (auth()->check()) {
                $view->with([
                    'quotationStatuses' => $this->getQuotationStatuses(),
                    'availableServices' => \App\Models\Service::active()->get(),
                    'canCreateQuotations' => auth()->user()->can('create quotations'),
                ]);
            }
        });
    }

    /**
     * Register client event listeners.
     */
    protected function registerClientEventListeners(): void
    {
        // Listen for client registration events
        $this->app['events']->listen(
            \Illuminate\Auth\Events\Registered::class,
            \App\Listeners\SendClientWelcomeNotification::class
        );

        // Listen for client project updates
        $this->app['events']->listen(
            \App\Events\ProjectStatusChanged::class,
            \App\Listeners\NotifyClientProjectUpdate::class
        );

        // Listen for quotation status changes
        $this->app['events']->listen(
            \App\Events\QuotationStatusChanged::class,
            \App\Listeners\NotifyClientQuotationUpdate::class
        );

        // Listen for new messages to clients
        $this->app['events']->listen(
            \App\Events\MessageReceived::class,
            \App\Listeners\NotifyClientNewMessage::class
        );

        // Clear client cache on relevant model changes
        $this->app['events']->listen([
            'eloquent.saved: App\Models\Project',
            'eloquent.deleted: App\Models\Project',
            'eloquent.saved: App\Models\Quotation',
            'eloquent.deleted: App\Models\Quotation',
            'eloquent.saved: App\Models\Message',
            'eloquent.deleted: App\Models\Message',
        ], \App\Listeners\ClearClientCache::class);
    }

    /**
     * Load client-specific configurations.
     */
    protected function loadClientConfigurations(): void
    {
        // Load client-specific configuration files if they exist
        $clientConfigPath = config_path('client.php');
        if (file_exists($clientConfigPath)) {
            $this->mergeConfigFrom($clientConfigPath, 'client');
        }

        // Set client-specific mail configurations
        config([
            'mail.from.address' => config('client.mail.from.address', config('mail.from.address')),
            'mail.from.name' => config('client.mail.from.name', config('mail.from.name')),
        ]);
    }

    /**
     * Get available project statuses.
     */
    protected function getProjectStatuses(): array
    {
        return [
            'pending' => [
                'label' => 'Pending',
                'color' => 'yellow',
                'description' => 'Project is waiting to start'
            ],
            'in_progress' => [
                'label' => 'In Progress',
                'color' => 'blue',
                'description' => 'Project is actively being worked on'
            ],
            'on_hold' => [
                'label' => 'On Hold',
                'color' => 'orange',
                'description' => 'Project is temporarily paused'
            ],
            'completed' => [
                'label' => 'Completed',
                'color' => 'green',
                'description' => 'Project has been finished successfully'
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'color' => 'red',
                'description' => 'Project has been cancelled'
            ],
        ];
    }

    /**
     * Get available quotation statuses.
     */
    protected function getQuotationStatuses(): array
    {
        return [
            'pending' => [
                'label' => 'Pending',
                'color' => 'yellow',
                'description' => 'Quotation is being reviewed'
            ],
            'under_review' => [
                'label' => 'Under Review',
                'color' => 'blue',
                'description' => 'Quotation is being evaluated by our team'
            ],
            'approved' => [
                'label' => 'Approved',
                'color' => 'green',
                'description' => 'Quotation has been approved'
            ],
            'rejected' => [
                'label' => 'Rejected',
                'color' => 'red',
                'description' => 'Quotation has been declined'
            ],
            'expired' => [
                'label' => 'Expired',
                'color' => 'gray',
                'description' => 'Quotation has expired'
            ],
        ];
    }
}