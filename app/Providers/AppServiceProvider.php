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
use App\Services\ClientAccessService;
use App\Services\FileUploadService;
use App\Services\DashboardService;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register singleton services
        $this->app->singleton(FileUploadService::class, function ($app) {
            return new FileUploadService();
        });

        $this->app->singleton(ClientAccessService::class, function ($app) {
            return new ClientAccessService();
        });

        $this->app->singleton(DashboardService::class);
        $this->app->singleton(\App\Services\SettingsService::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        $this->registerBladeDirectives();
        $this->registerViewComposers();
        $this->registerCustomGates();
        if (file_exists($helpers = app_path('helpers.php'))) {
            require_once $helpers;
        }
        if (class_exists('\App\Helpers\SeoHelper')) {
            $this->app->singleton(\App\Helpers\SeoHelper::class);
        }

        Route::bind('attachment', function ($value) {
        return \App\Models\QuotationAttachment::findOrFail($value);
        

        
    });
    }

    /**
     * Register Blade directives for easier template usage.
     */
    protected function registerBladeDirectives(): void
    {
        Blade::if('client', function () {
            return Auth::check() && Auth::user()->hasRole('client');
        });
        Blade::component('banner-slider', \App\View\Components\BannerSlider::class);

        Blade::if('admin', function () {
            return Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']);
        });

        Blade::if('hasRole', function ($role) {
            return Auth::check() && Auth::user()->hasRole($role);
        });

        Blade::if('canDo', function ($permission) {
            return Auth::check() && Auth::user()->can($permission);
        });

        Blade::if('canAccess', function ($resourceType, $resourceId = null) {
            if (!Auth::check()) return false;
            
        });

        Blade::if('adminViewing', function () {
            return Auth::check() && 
                   Auth::user()->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']) && 
                   request()->is('client/*');
        });
    }
    

    /**
     * Register view composers for shared data.
     */
    protected function registerViewComposers(): void
    {
        // Admin view composers
        View::composer([
            'admin.*', 
            'layouts.admin', 
            'components.admin.admin-header', 
            'components.admin.admin-sidebar'
        ], function ($view) {
            if (Auth::check()) {
                try {
                    $view->with($this->getAdminViewData());
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
                    $view->with($this->getClientViewData());
                } catch (\Exception $e) {
                    \Log::error('Error fetching client view stats: ' . $e->getMessage());
                    $view->with($this->getClientDefaultStats());
                }
            } else {
                $view->with($this->getClientDefaultStats());
            }
        });
        view()->composer('components.banner-slider', function ($view) {
            // Global data that all banner sliders might need
            $view->with([
                'defaultTracking' => config('banner.enable_tracking', true),
                'cdnUrl' => config('app.cdn_url', config('app.url')),
            ]);
        });

        // Chat sidebar composer - only if chat feature is enabled
        if (class_exists(ChatSession::class)) {
            View::composer('components.admin.chat-sidebar', function ($view) {
                if (Auth::check()) {
                    try {
                        $view->with([
                            'activeChatSessions' => ChatSession::where('status', 'active')->count(),
                            'waitingChatSessions' => ChatSession::where('status', 'waiting')->count(),
                        ]);
                    } catch (\Exception $e) {
                        $view->with([
                            'activeChatSessions' => 0,
                            'waitingChatSessions' => 0,
                        ]);
                    }
                }
            });
        }
    }

    /**
     * Register custom gates for authorization.
     */
    protected function registerCustomGates(): void
    {
        

        Gate::define('admin-support-access', function ($user) {
            return $user->hasAnyRole(['super-admin', 'admin']) && 
                   $user->can('provide client support');
        });

        Gate::define('verify-clients', function ($user) {
            return $user->hasAnyRole(['super-admin', 'admin', 'manager']) && 
                   $user->can('verify clients');
        });
    }


    /**
     * Get admin view data.
     */
    protected function getAdminViewData(): array
    {
        return [
            'unreadMessages' => $this->getMessageStats()['unread'],
            'pendingQuotations' => $this->getQuotationStats()['pending'],
            'companyProfile' => CompanyProfile::getInstance(),
            'quotationStats' => $this->getQuotationStats(),
            'projectStats' => $this->getProjectStats(),
            'messageStats' => $this->getMessageStats(),
            'chatStats' => $this->getChatStats(),
            'totalPostsCount' => $this->safeCount(Post::class),
            'draftPostsCount' => $this->safeCount(Post::class, ['status' => 'draft']),
            'publishedPostsCount' => $this->safeCount(Post::class, ['status' => 'published']),
            'categoriesCount' => $this->safeCount(PostCategory::class),
        ];
    }

    /**
     * Get client view data.
     */
    protected function getClientViewData(): array
    {
        $clientService = app(ClientAccessService::class);
        $user = Auth::user();

        return [
            'clientStats' => $clientService->getClientStatistics($user),
            'companyProfile' => CompanyProfile::getInstance(),
            'user' => $user,
        ];
    }

    /**
     * Clear client cache when model changes affect a client.
     */
    protected function clearClientCacheFromModel(array $models): void
    {
        if (isset($models[0]) && isset($models[0]->client_id)) {
            $client = User::where('id', $models[0]->client_id)->first();
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
        try {
            return [
                'total' => Quotation::count(),
                'pending' => Quotation::where('status', 'pending')->count(),
                'reviewed' => Quotation::where('status', 'reviewed')->count(),
                'approved' => Quotation::where('status', 'approved')->count(),
                'rejected' => Quotation::where('status', 'rejected')->count(),
                'urgent' => Quotation::where('priority', 'urgent')
                    ->where('status', 'pending')
                    ->count(),
                'high_priority' => Quotation::whereIn('priority', ['high', 'urgent'])
                    ->where('status', 'pending')
                    ->count(),
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
        } catch (\Exception $e) {
            \Log::error('Error getting quotation stats: ' . $e->getMessage());
            return $this->getDefaultQuotationStats();
        }
    }

    /**
     * Get project statistics.
     */
    protected function getProjectStats(): array
    {
        try {
            return [
                'total' => Project::count(),
                'active' => Project::whereIn('status', ['in_progress', 'on_hold'])->count(),
                'completed' => Project::where('status', 'completed')->count(),
                'pending' => Project::where('status', 'pending')->count(),
                'overdue' => Project::where('status', 'in_progress')
                    ->where('end_date', '<', now())
                    ->whereNotNull('end_date')
                    ->count(),
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting project stats: ' . $e->getMessage());
            return $this->getDefaultProjectStats();
        }
    }

    /**
     * Get message statistics.
     */
    protected function getMessageStats(): array
    {
        try {
            return [
                'total' => Message::count(),
                'unread' => Message::where('is_read', false)->count(),
                'today' => Message::whereDate('created_at', today())->count(),
                'replied' => Message::where('is_replied', true)->count(),
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting message stats: ' . $e->getMessage());
            return $this->getDefaultMessageStats();
        }
    }

    /**
     * Get chat statistics.
     */
    protected function getChatStats(): array
    {
        try {
            if (!class_exists(ChatSession::class)) {
                return $this->getDefaultChatStats();
            }

            return [
                'active_sessions' => ChatSession::where('status', 'active')->count(),
                'waiting_sessions' => ChatSession::where('status', 'waiting')->count(),
                'today_sessions' => ChatSession::whereDate('created_at', today())->count(),
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting chat stats: ' . $e->getMessage());
            return $this->getDefaultChatStats();
        }
    }

    /**
     * Calculate quotation conversion rate.
     */
    protected function calculateConversionRate(): float
    {
        try {
            $total = Quotation::count();
            $approved = Quotation::where('status', 'approved')->count();
            
            return $total > 0 ? round(($approved / $total) * 100, 1) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Safe count method to handle missing tables/models.
     */
    protected function safeCount(string $model, array $conditions = []): int
    {
        try {
            $query = $model::query();
            
            foreach ($conditions as $field => $value) {
                $query->where($field, $value);
            }
            
            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get default statistics for unauthenticated users or errors.
     */
    protected function getDefaultStats(): array
    {
        return [
            'unreadMessages' => 0,
            'pendingQuotations' => 0,
            'companyProfile' => CompanyProfile::getInstance(),
            'quotationStats' => $this->getDefaultQuotationStats(),
            'projectStats' => $this->getDefaultProjectStats(),
            'messageStats' => $this->getDefaultMessageStats(),
            'chatStats' => $this->getDefaultChatStats(),
            'totalPostsCount' => 0,
            'draftPostsCount' => 0,
            'publishedPostsCount' => 0,
            'categoriesCount' => 0,
        ];
    }

    /**
     * Get client default statistics.
     */
    protected function getClientDefaultStats(): array
    {
        return [
            'clientStats' => [],
            'companyProfile' => CompanyProfile::getInstance(),
        ];
    }

    protected function getDefaultQuotationStats(): array
    {
        return [
            'total' => 0,
            'pending' => 0,
            'reviewed' => 0,
            'approved' => 0,
            'rejected' => 0,
            'urgent' => 0,
            'high_priority' => 0,
            'today' => 0,
            'this_week' => 0,
            'this_month' => 0,
            'overdue' => 0,
            'needs_attention' => 0,
            'client_approved' => 0,
            'awaiting_client_response' => 0,
            'conversion_rate' => 0,
        ];
    }

    protected function getDefaultProjectStats(): array
    {
        return [
            'total' => 0,
            'active' => 0,
            'completed' => 0,
            'pending' => 0,
            'overdue' => 0,
        ];
    }

    protected function getDefaultMessageStats(): array
    {
        return [
            'total' => 0,
            'unread' => 0,
            'today' => 0,
            'replied' => 0,
        ];
    }

    protected function getDefaultChatStats(): array
    {
        return [
            'active_sessions' => 0,
            'waiting_sessions' => 0,
            'today_sessions' => 0,
        ];
    }
}