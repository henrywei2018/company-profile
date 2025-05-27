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

/**
 * Client Dashboard Service
 * File: app/Services/ClientDashboardService.php
 */
namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ClientDashboardService
{
    protected ClientAccessService $clientAccessService;

    public function __construct(ClientAccessService $clientAccessService)
    {
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Get comprehensive dashboard data for client.
     */
    public function getDashboardData(User $user): array
    {
        $cacheKey = "client_dashboard_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            return [
                'statistics' => $this->clientAccessService->getClientDashboardStats($user),
                'recent_projects' => $this->getRecentProjects($user),
                'recent_quotations' => $this->getRecentQuotations($user),
                'recent_messages' => $this->getRecentMessages($user),
                'upcoming_deadlines' => $this->getUpcomingDeadlines($user),
                'quick_actions' => $this->getQuickActions($user),
            ];
        });
    }

    /**
     * Get recent activities for dashboard.
     */
    public function getRecentActivities(User $user): Collection
    {
        return $this->clientAccessService->getRecentActivity($user);
    }

    /**
     * Get notifications for client.
     */
    public function getNotifications(User $user): array
    {
        return [
            'unread_messages' => $this->getUnreadMessagesCount($user),
            'pending_approvals' => $this->getPendingApprovalsCount($user),
            'overdue_items' => $this->getOverdueItemsCount($user),
            'system_announcements' => $this->getSystemAnnouncements(),
        ];
    }

    /**
     * Get recent projects for dashboard.
     */
    protected function getRecentProjects(User $user): Collection
    {
        return $this->clientAccessService
            ->getClientProjects($user)
            ->limit(5)
            ->get();
    }

    /**
     * Get recent quotations for dashboard.
     */
    protected function getRecentQuotations(User $user): Collection
    {
        return $this->clientAccessService
            ->getClientQuotations($user)
            ->limit(5)
            ->get();
    }

    /**
     * Get recent messages for dashboard.
     */
    protected function getRecentMessages(User $user): Collection
    {
        return $this->clientAccessService
            ->getClientMessages($user)
            ->limit(5)
            ->get();
    }

    /**
     * Get upcoming deadlines.
     */
    protected function getUpcomingDeadlines(User $user): Collection
    {
        $baseCondition = $user->hasAnyRole(['super-admin', 'admin', 'manager']) 
            ? [] 
            : ['client_id' => $user->id];

        return Project::where($baseCondition)
            ->where('status', 'in_progress')
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->orderBy('end_date')
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project_deadline',
                    'title' => $project->title,
                    'date' => $project->end_date,
                    'url' => route('client.projects.show', $project->id),
                    'urgency' => $this->calculateUrgency($project->end_date),
                ];
            });
    }

    /**
     * Get quick actions available to client.
     */
    protected function getQuickActions(User $user): array
    {
        $actions = [];

        if ($user->can('create quotations')) {
            $actions[] = [
                'title' => 'Request Quote',
                'description' => 'Submit a new quotation request',
                'url' => route('client.quotations.create'),
                'icon' => 'document-add',
                'color' => 'blue',
            ];
        }

        if ($user->can('create messages')) {
            $actions[] = [
                'title' => 'Send Message',
                'description' => 'Contact our support team',
                'url' => route('client.messages.create'),
                'icon' => 'mail',
                'color' => 'green',
            ];
        }

        if ($user->can('create testimonials')) {
            $actions[] = [
                'title' => 'Leave Review',
                'description' => 'Share your experience with us',
                'url' => route('client.testimonials.create'),
                'icon' => 'star',
                'color' => 'yellow',
            ];
        }

        $actions[] = [
            'title' => 'Start Chat',
            'description' => 'Get instant support via chat',
            'url' => '#',
            'icon' => 'chat',
            'color' => 'purple',
            'action' => 'start-chat',
        ];

        return $actions;
    }

    /**
     * Get unread messages count.
     */
    protected function getUnreadMessagesCount(User $user): int
    {
        $baseCondition = $user->hasAnyRole(['super-admin', 'admin', 'manager']) 
            ? [] 
            : ['client_id' => $user->id];

        return Message::where($baseCondition)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get pending approvals count.
     */
    protected function getPendingApprovalsCount(User $user): int
    {
        $baseCondition = $user->hasAnyRole(['super-admin', 'admin', 'manager']) 
            ? [] 
            : ['client_id' => $user->id];

        return Quotation::where($baseCondition)
            ->where('status', 'approved')
            ->whereNull('client_approved')
            ->count();
    }

    /**
     * Get overdue items count.
     */
    protected function getOverdueItemsCount(User $user): int
    {
        $baseCondition = $user->hasAnyRole(['super-admin', 'admin', 'manager']) 
            ? [] 
            : ['client_id' => $user->id];

        return Project::where($baseCondition)
            ->where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->count();
    }

    /**
     * Get system announcements.
     */
    protected function getSystemAnnouncements(): array
    {
        // This could be expanded to fetch from a database table
        return [
            [
                'id' => 1,
                'title' => 'System Maintenance Scheduled',
                'message' => 'Scheduled maintenance will occur this weekend.',
                'type' => 'info',
                'created_at' => now()->subDays(2),
                'is_read' => false,
            ],
        ];
    }

    /**
     * Calculate urgency level for deadlines.
     */
    protected function calculateUrgency(\Carbon\Carbon $deadline): string
    {
        $daysUntil = now()->diffInDays($deadline, false);
        
        return match(true) {
            $daysUntil <= 1 => 'critical',
            $daysUntil <= 3 => 'high',
            $daysUntil <= 7 => 'medium',
            default => 'low',
        };
    }
}

/**
 * Client Notification Service
 * File: app/Services/ClientNotificationService.php
 */
namespace App\Services;

use App\Models\User;
use App\Notifications\ClientProjectUpdated;
use App\Notifications\ClientQuotationUpdated;
use App\Notifications\ClientMessageReceived;
use Illuminate\Support\Facades\Notification;

class ClientNotificationService
{
    /**
     * Send project update notification to client.
     */
    public function notifyProjectUpdate(User $client, $project, string $updateType): void
    {
        $client->notify(new ClientProjectUpdated($project, $updateType));
    }

    /**
     * Send quotation update notification to client.
     */
    public function notifyQuotationUpdate(User $client, $quotation, string $updateType): void
    {
        $client->notify(new ClientQuotationUpdated($quotation, $updateType));
    }

    /**
     * Send new message notification to client.
     */
    public function notifyNewMessage(User $client, $message): void
    {
        $client->notify(new ClientMessageReceived($message));
    }

    /**
     * Send bulk notifications to multiple clients.
     */
    public function sendBulkNotification(array $clientIds, $notification): void
    {
        $clients = User::whereIn('id', $clientIds)->get();
        Notification::send($clients, $notification);
    }

    /**
     * Send welcome notification to new client.
     */
    public function sendWelcomeNotification(User $client): void
    {
        $client->notify(new \App\Notifications\ClientWelcome());
    }
}