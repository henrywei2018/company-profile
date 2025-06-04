<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Services\ClientAccessService;
use App\Services\ClientNotificationService;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\User;

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
        // Register client notification service
        $this->app->singleton(ClientNotificationService::class, function ($app) {
            return new ClientNotificationService();
        });

        // Register client repositories if they exist
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
        // Skip repository registration since they don't exist yet
        // These can be added later when repository pattern is implemented
        
        /*
        // Only register if interfaces exist
        if (interface_exists(\App\Repositories\Interfaces\ClientProjectRepositoryInterface::class)) {
            $this->app->bind(
                \App\Repositories\Interfaces\ClientProjectRepositoryInterface::class,
                \App\Repositories\ClientProjectRepository::class
            );
        }

        if (interface_exists(\App\Repositories\Interfaces\ClientQuotationRepositoryInterface::class)) {
            $this->app->bind(
                \App\Repositories\Interfaces\ClientQuotationRepositoryInterface::class,
                \App\Repositories\ClientQuotationRepository::class
            );
        }

        if (interface_exists(\App\Repositories\Interfaces\ClientMessageRepositoryInterface::class)) {
            $this->app->bind(
                \App\Repositories\Interfaces\ClientMessageRepositoryInterface::class,
                \App\Repositories\ClientMessageRepository::class
            );
        }
        */
    }

    /**
     * Register route model bindings for client resources.
     */
    protected function registerRouteBindings(): void
    {
        // Bind client projects with access control
        Route::bind('clientProject', function ($value) {
            /** @var Project $project */
            $project = Project::findOrFail($value);
            
            if (auth()->check()) {
                $clientService = app(ClientAccessService::class);
                if (!$clientService->canAccessProject(auth()->user(), $project)) {
                    abort(403, 'You do not have access to this project.');
                }
            }
            
            return $project;
        });

        // Bind client quotations with access control
        Route::bind('clientQuotation', function ($value) {
            /** @var Quotation $quotation */
            $quotation = Quotation::findOrFail($value);
            
            if (auth()->check()) {
                $clientService = app(ClientAccessService::class);
                if (!$clientService->canAccessQuotation(auth()->user(), $quotation)) {
                    abort(403, 'You do not have access to this quotation.');
                }
            }
            
            return $quotation;
        });

        // Bind client messages with access control
        Route::bind('clientMessage', function ($value) {
            /** @var Message $message */
            $message = Message::findOrFail($value);
            
            if (auth()->check()) {
                $clientService = app(ClientAccessService::class);
                if (!$clientService->canAccessMessage(auth()->user(), $message)) {
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
                    'clientPermissions' => $clientService->getClientPermissions(auth()->user()),
                ]);
            }
        });

        // Client dashboard composer
        View::composer('client.dashboard', function ($view) {
            if (auth()->check()) {
                $clientService = app(ClientAccessService::class);
                $view->with([
                    'dashboardData' => $clientService->getClientStatistics(auth()->user()),
                ]);
            }
        });

        // Client project views composer
        View::composer('client.projects.*', function ($view) {
            if (auth()->check()) {
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
                    'availableServices' => \App\Models\Service::where('is_active', true)->get(),
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
        if (class_exists(\Illuminate\Auth\Events\Registered::class)) {
            $this->app['events']->listen(
                \Illuminate\Auth\Events\Registered::class,
                function ($event) {
                    if ($event->user->hasRole('client')) {
                        \App\Facades\Notifications::send('user.welcome', $event->user, $event->user);
                    }
                }
            );
        }

        // Clear client cache on relevant model changes
        $this->app['events']->listen([
            'eloquent.saved: App\Models\Project',
            'eloquent.deleted: App\Models\Project',
            'eloquent.saved: App\Models\Quotation',
            'eloquent.deleted: App\Models\Quotation',
            'eloquent.saved: App\Models\Message',
            'eloquent.deleted: App\Models\Message',
        ], function ($event, $models) {
            // $models is an array, get the first model
            if (isset($models[0])) {
                $model = $models[0];
                
                // Check if model has client_id property
                if (property_exists($model, 'client_id') && $model->client_id) {
                    /** @var User|null $client */
                    $client = User::find($model->client_id);
                    if ($client instanceof User) {
                        $clientService = app(ClientAccessService::class);
                        $clientService->clearClientCache($client);
                    }
                }
            }
        });
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

        // Set client-specific configurations
        config([
            'client.dashboard.refresh_interval' => 30000, // 30 seconds
            'client.notifications.enabled' => true,
            'client.projects.per_page' => 10,
            'client.quotations.per_page' => 10,
            'client.messages.per_page' => 15,
        ]);
    }

    /**
     * Get available project statuses.
     */
    protected function getProjectStatuses(): array
    {
        return [
            'planning' => [
                'label' => 'Planning',
                'color' => 'yellow',
                'description' => 'Project is in planning phase'
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
            'reviewed' => [
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
        ];
    }
}