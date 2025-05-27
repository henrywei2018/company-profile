<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use App\Models\CompanyProfile;
use App\Models\Message;
use App\Models\Quotation;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ChatSession;
use App\View\Composers\ChatSidebarComposer;
use App\Services\ClientAccessService;
use App\Services\FileUploadService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register core services
        $this->app->singleton(FileUploadService::class, function ($app) {
            return new FileUploadService();
        });

        // Register client access service
        $this->app->singleton(ClientAccessService::class, function ($app) {
            return new ClientAccessService();
        });

        // Register development tools in local environment
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL older than 5.7.7
        Schema::defaultStringLength(191);
        
        // Register custom Blade directives
        $this->registerBladeDirectives();
        
        // Register view composers
        $this->registerViewComposers();
        
        // Register custom gates
        $this->registerCustomGates();
        
        // Register event listeners
        $this->registerEventListeners();
    }

    /**
     * Register custom Blade directives.
     */
    protected function registerBladeDirectives(): void
    {
        // Client role checking directive
        Blade::if('client', function () {
            return Auth::check() && Auth::user()->hasRole('client');
        });

        // Admin role checking directive
        Blade::if('admin', function () {
            return Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']);
        });

        // Specific role checking directive
        Blade::if('hasRole', function ($role) {
            return Auth::check() && Auth::user()->hasRole($role);
        });

        // Permission checking directive
        Blade::if('canDo', function ($permission) {
            return Auth::check() && Auth::user()->can($permission);
        });

        // Client resource access directive
        Blade::if('canAccess', function ($resourceType, $resourceId = null) {
            if (!Auth::check()) return false;
            
            $clientService = app(ClientAccessService::class);
            return $resourceId 
                ? $clientService->canAccessResource(Auth::user(), $resourceType, $resourceId)
                : $clientService->hasClientAccess(Auth::user());
        });

        // Admin viewing client area directive
        Blade::if('adminViewing', function () {
            return Auth::check() && 
                   Auth::user()->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']) &&
                   request()->is('client/*');
        });
    }

    /**
     * Register view composers.
     */
    protected function registerViewComposers(): void
    {
        // Global view composers for admin views
        View::composer([
            'admin.*', 
            'layouts.admin', 
            'components.admin.admin-header', 
            'components.admin.admin-sidebar'
        ], function ($view) {
            if (Auth::check()) {
                try {
                    // Get comprehensive statistics for admin views
                    $quotationStats = $this->getQuotationStats();
                    $projectStats = $this->getProjectStats();
                    $messageStats = $this->getMessageStats();
                    $chatStats = $this->getChatStats();
                    
                    $view->with([
                        // Basic counts
                        'unreadMessages' => $messageStats['unread'],
                        'pendingQuotations' => $quotationStats['pending'],
                        'companyProfile' => CompanyProfile::getInstance(),
                        
                        // Enhanced statistics
                        'quotationStats' => $quotationStats,
                        'projectStats' => $projectStats,
                        'messageStats' => $messageStats,
                        'chatStats' => $chatStats,
                        
                        // Post statistics
                        'totalPostsCount' => Post::count(),
                        'draftPostsCount' => Post::where('status', 'draft')->count(),
                        'publishedPostsCount' => Post::where('status', 'published')->count(),
                        'categoriesCount' => PostCategory::count(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error fetching admin view stats: ' . $e->getMessage());
                    $view->with($this->getDefaultStats());
                }
            } else {
                $view->with($this->getDefaultStats());
            }
        });

        // Client view composers
        View::composer([
            'client.*', 
            'layouts.client', 
            'components.client.*'
        ], function ($view) {
            if (Auth::check()) {
                try {
                    $clientService = app(ClientAccessService::class);
                    $user = Auth::user();
                    
                    $view->with([
                        'clientStats' => $clientService->getClientDashboardStats($user),
                        'clientNavigation' => $clientService->getClientNavigationMenu($user),
                        'clientPermissions' => $clientService->getClientPermissions($user),
                        'companyProfile' => CompanyProfile::getInstance(),
                        'isAdminViewing' => $user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']),
                        'user' => $user,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error fetching client view stats: ' . $e->getMessage());
                    $view->with([
                        'clientStats' => [],
                        'clientNavigation' => [],
                        'clientPermissions' => [],
                        'companyProfile' => CompanyProfile::getInstance(),
                        'isAdminViewing' => false,
                    ]);
                }
            }
        });

        // Chat sidebar composer
        View::composer('components.admin.chat-sidebar', ChatSidebarComposer::class);
    }

    /**
     * Register custom gates.
     */
    protected function registerCustomGates(): void
    {
        // Client area access gate
        Gate::define('access-client-area', function ($user) {
            $clientService = app(ClientAccessService::class);
            return $clientService->hasClientAccess($user);
        });

        // Client resource access gates
        Gate::define('access-client-project', function ($user, $projectId) {
            $clientService = app(ClientAccessService::class);
            return $clientService->canAccessResource($user, 'project', $projectId);
        });

        Gate::define('access-client-quotation', function ($user, $quotationId) {
            $clientService = app(ClientAccessService::class);
            return $clientService->canAccessResource($user, 'quotation', $quotationId);
        });

        Gate::define('access-client-message', function ($user, $messageId) {
            $clientService = app(ClientAccessService::class);
            return $clientService->canAccessResource($user, 'message', $messageId);
        });

        // Enhanced admin gates
        Gate::define('admin-support-access', function ($user) {
            return $user->hasAnyRole(['super-admin', 'admin']) && 
                   $user->can('provide client support');
        });

        // Client verification gate
        Gate::define('verify-clients', function ($user) {
            return $user->hasAnyRole(['super-admin', 'admin', 'manager']) &&
                   $user->can('verify clients');
        });
    }

    /**
     * Register event listeners.
     */
    protected function registerEventListeners(): void
    {
        // Clear client cache when relevant models are updated
        $this->app['events']->listen([
            'eloquent.saved: App\Models\Project',
            'eloquent.deleted: App\Models\Project',
        ], function ($event, $models) {
            if (isset($models[0]) && $models[0]->client_id) {
                $clientService = app(ClientAccessService::class);
                $client = \App\Models\User::find($models[0]->client_id);
                if ($client) {
                    $clientService->clearClientCache($client);
                }
            }
        });

        // Similar listeners for quotations and messages
        $this->app['events']->listen([
            'eloquent.saved: App\Models\Quotation',
            'eloquent.deleted: App\Models\Quotation',
        ], function ($event, $models) {
            if (isset($models[0]) && $models[0]->client_id) {
                $clientService = app(ClientAccessService::class);
                $client = \App\Models\User::find($models[0]->client_id);
                if ($client) {
                    $clientService->clearClientCache($client);
                }
            }
        });
    }

    /**
     * Get quotation statistics.
     */
    protected function getQuotationStats(): array
    {
        return [
            'total' => Quotation::count(),
            'pending' => Quotation::where('status', 'pending')->count(),
            'reviewed' => Quotation::where('status', 'reviewed')->count(),
            'approved' => Quotation::where('status', 'approved')->count(),
            'rejected' => Quotation::where('status', 'rejected')->count(),
            'urgent' => Quotation::where('priority', 'urgent')->where('status', 'pending')->count(),
            'high_priority' => Quotation::whereIn('priority', ['high', 'urgent'])->where('status', 'pending')->count(),
            'today' => Quotation::whereDate('created_at', today())->count(),
            'this_week' => Quotation::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => Quotation::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'overdue' => Quotation::where('status', 'pending')
                ->where('created_at', '<', now()->subDays(3))
                ->count(),
            'needs_attention' => Quotation::where('status', 'pending')
                ->where(function($query) {
                    $query->whereIn('priority', ['high', 'urgent'])
                          ->orWhere('created_at', '<', now()->subDays(3));
                })
                ->count(),
            'client_approved' => Quotation::where('client_approved', true)->count(),
            'awaiting_client_response' => Quotation::where('status', 'approved')
                ->whereNull('client_approved')
                ->count(),
            'conversion_rate' => $this->calculateConversionRate(),
        ];
    }

    /**
     * Get project statistics.
     */
    protected function getProjectStats(): array
    {
        return [
            'total' => Project::count(),
            'active' => Project::whereIn('status', ['in_progress', 'on_hold'])->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'pending' => Project::where('status', 'pending')->count(),
            'overdue' => Project::where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->count(),
        ];
    }

    /**
     * Get message statistics.
     */
    protected function getMessageStats(): array
    {
        return [
            'total' => Message::count(),
            'unread' => Message::where('is_read', false)->count(),
            'today' => Message::whereDate('created_at', today())->count(),
            'replied' => Message::whereHas('replies')->count(),
        ];
    }

    /**
     * Get chat statistics.
     */
    protected function getChatStats(): array
    {
        return [
            'active_sessions' => ChatSession::where('status', 'active')->count(),
            'waiting_sessions' => ChatSession::where('status', 'waiting')->count(),
            'today_sessions' => ChatSession::whereDate('created_at', today())->count(),
        ];
    }

    /**
     * Calculate quotation conversion rate.
     */
    protected function calculateConversionRate(): float
    {
        $total = Quotation::count();
        $approved = Quotation::where('status', 'approved')->count();
        
        return $total > 0 ? round(($approved / $total) * 100, 1) : 0;
    }

    /**
     * Get default statistics for unauthenticated users.
     */
    protected function getDefaultStats(): array
    {
        return [
            'unreadMessages' => 0,
            'pendingQuotations' => 0,
            'companyProfile' => CompanyProfile::getInstance(),
            'quotationStats' => [],
            'projectStats' => [],
            'messageStats' => [],
            'chatStats' => [],
            'totalPostsCount' => 0,
            'draftPostsCount' => 0,
            'publishedPostsCount' => 0,
            'categoriesCount' => 0,
        ];
    }
}