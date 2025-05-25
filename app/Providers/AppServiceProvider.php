<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyProfile;
use App\Models\Message;
use App\Models\Quotation;
use App\Models\Post;
use App\Models\PostCategory;

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
                try {
                    // Get comprehensive quotation statistics
                    $quotationStats = [
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
                    ];
                    
                    // Calculate overdue and needs attention (simplified for performance)
                    $quotationStats['overdue'] = Quotation::where('status', 'pending')
                        ->where('created_at', '<', now()->subDays(3))
                        ->count();
                        
                    $quotationStats['needs_attention'] = Quotation::where('status', 'pending')
                        ->where(function($query) {
                            $query->whereIn('priority', ['high', 'urgent'])
                                  ->orWhere('created_at', '<', now()->subDays(3));
                        })
                        ->count();
                    
                    // Additional stats
                    $quotationStats['client_approved'] = Quotation::where('client_approved', true)->count();
                    $quotationStats['awaiting_client_response'] = Quotation::where('status', 'approved')
                        ->whereNull('client_approved')
                        ->count();
                    
                    // Conversion rate
                    $quotationStats['conversion_rate'] = $quotationStats['total'] > 0 ? 
                        round(($quotationStats['approved'] / $quotationStats['total']) * 100, 1) : 0;
                        
                } catch (\Exception $e) {
                    // Log error and use safe defaults
                    \Log::error('Error fetching quotation stats: ' . $e->getMessage());
                    
                    $quotationStats = [
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
                
                $view->with([
                    // Existing counts
                    'unreadMessages' => Message::unread()->count(),
                    'pendingQuotations' => $quotationStats['pending'],
                    'companyProfile' => CompanyProfile::getInstance(),
                    
                    // Enhanced quotation counts
                    'pendingQuotationsCount' => $quotationStats['pending'],
                    'urgentQuotationsCount' => $quotationStats['urgent'],
                    'highPriorityQuotationsCount' => $quotationStats['high_priority'],
                    'todayQuotationsCount' => $quotationStats['today'],
                    'approvedQuotationsCount' => $quotationStats['approved'],
                    'totalQuotationsCount' => $quotationStats['total'],
                    'overdueQuotationsCount' => $quotationStats['overdue'],
                    'needsAttentionCount' => $quotationStats['needs_attention'],
                    
                    'quotationStats' => $quotationStats,
                    'totalPostsCount' => Post::count(),
                    'draftPostsCount' => Post::where('status', 'draft')->count(),
                    'publishedPostsCount' => Post::where('status', 'published')->count(),
                    'categoriesCount' => PostCategory::count(),
                ]);
            } else {
                $view->with([
                    'unreadMessages' => 0,
                    'pendingQuotations' => 0,
                    'companyProfile' => CompanyProfile::getInstance(),
                    
                    // Default counts for unauthenticated users
                    'pendingQuotationsCount' => 0,
                    'urgentQuotationsCount' => 0,
                    'highPriorityQuotationsCount' => 0,
                    'todayQuotationsCount' => 0,
                    'approvedQuotationsCount' => 0,
                    'totalQuotationsCount' => 0,
                    'overdueQuotationsCount' => 0,
                    'needsAttentionCount' => 0,
                    'quotationStats' => [],
                ]);
            }
        });
}
}