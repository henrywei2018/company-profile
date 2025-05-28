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
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FileUploadService::class, fn($app) => new FileUploadService());
        $this->app->singleton(ClientAccessService::class, fn($app) => new ClientAccessService());

        if ($this->app->environment('local')) {
            if (class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
                $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            }

            if (class_exists(\App\Providers\TelescopeServiceProvider::class)) {
                $this->app->register(\App\Providers\TelescopeServiceProvider::class);
            }
        }
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        $this->registerBladeDirectives();
        $this->registerViewComposers();
        $this->registerCustomGates();
        $this->registerEventListeners();
    }

    // Blade directives...
    protected function registerBladeDirectives(): void
    {
        Blade::if('client', fn() => Auth::check() && Auth::user()->hasRole('client'));
        Blade::if('admin', fn() => Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']));
        Blade::if('hasRole', fn($role) => Auth::check() && Auth::user()->hasRole($role));
        Blade::if('canDo', fn($permission) => Auth::check() && Auth::user()->can($permission));
        Blade::if('canAccess', function ($resourceType, $resourceId = null) {
            if (!Auth::check()) return false;
            $clientService = app(ClientAccessService::class);
            return $resourceId ? $clientService->canAccessResource(Auth::user(), $resourceType, $resourceId) : $clientService->hasClientAccess(Auth::user());
        });
        Blade::if('adminViewing', fn() => Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']) && request()->is('client/*'));
    }

    // View composers...
    protected function registerViewComposers(): void
    {
        View::composer(['admin.*', 'layouts.admin', 'components.admin.admin-header', 'components.admin.admin-sidebar'], function ($view) {
            if (Auth::check()) {
                try {
                    $quotationStats = $this->getQuotationStats();
                    $projectStats = $this->getProjectStats();
                    $messageStats = $this->getMessageStats();
                    $chatStats = $this->getChatStats();

                    $view->with([
                        'unreadMessages' => $messageStats['unread'],
                        'pendingQuotations' => $quotationStats['pending'],
                        'companyProfile' => CompanyProfile::getInstance(),
                        'quotationStats' => $quotationStats,
                        'projectStats' => $projectStats,
                        'messageStats' => $messageStats,
                        'chatStats' => $chatStats,
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

        View::composer(['client.*', 'layouts.client', 'components.client.*'], function ($view) {
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

        View::composer('components.admin.chat-sidebar', ChatSidebarComposer::class);
    }

    protected function registerCustomGates(): void
    {
        Gate::define('access-client-area', fn($user) => app(ClientAccessService::class)->hasClientAccess($user));
        Gate::define('access-client-project', fn($user, $projectId) => app(ClientAccessService::class)->canAccessResource($user, 'project', $projectId));
        Gate::define('access-client-quotation', fn($user, $quotationId) => app(ClientAccessService::class)->canAccessResource($user, 'quotation', $quotationId));
        Gate::define('access-client-message', fn($user, $messageId) => app(ClientAccessService::class)->canAccessResource($user, 'message', $messageId));
        Gate::define('admin-support-access', fn($user) => $user->hasAnyRole(['super-admin', 'admin']) && $user->can('provide client support'));
        Gate::define('verify-clients', fn($user) => $user->hasAnyRole(['super-admin', 'admin', 'manager']) && $user->can('verify clients'));
    }

    protected function registerEventListeners(): void
    {
        $this->app['events']->listen([
            'eloquent.saved: App\\Models\\Project',
            'eloquent.deleted: App\\Models\\Project',
        ], fn($event, $models) => $this->clearClientCacheFromModel($models));

        $this->app['events']->listen([
            'eloquent.saved: App\\Models\\Quotation',
            'eloquent.deleted: App\\Models\\Quotation',
        ], fn($event, $models) => $this->clearClientCacheFromModel($models));
    }

    protected function clearClientCacheFromModel(array $models): void
    {
        if (isset($models[0]) && $models[0]->client_id) {
            $client = User::find($models[0]->client_id);
            if ($client) {
                app(ClientAccessService::class)->clearClientCache($client);
            }
        }
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