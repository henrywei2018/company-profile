<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    PortfolioController,
    QuotationController,
    MessageController,
    ProfileController,
    ProjectController,
    BlogController,
    ContactController,
    AboutController,
    ServiceController,
    TeamController,
    ChatController,
    SitemapController
};
use App\Http\Controllers\Client\{
    DashboardController as ClientDashboardController,
    ProfileController as ClientProfileController,
    NotificationPreferencesController
};
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\UnifiedProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Services\GoogleAnalyticsService;

require __DIR__ . '/auth.php';



/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('about')->name('about.')->group(function () {
    Route::get('/', [AboutController::class, 'index'])->name('index');
    Route::get('/team', [AboutController::class, 'team'])->name('team');
    Route::get('/team/{slug}', [TeamController::class, 'show'])->name('team.show');
});

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/{services:slug}', [ServiceController::class, 'show'])->name('show'); // ✅ Fix parameter
});
Route::group(['prefix' => 'products'], function () {
    Route::get('/', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('/{slug}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
    
    // Category-specific routes
    Route::get('/category/{categorySlug}', [App\Http\Controllers\ProductController::class, 'getByCategory'])->name('products.category');
});
Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::get('/', [PortfolioController::class, 'index'])->name('index');
    Route::get('/{project:slug}', [PortfolioController::class, 'show'])->name('show');
    
});


Route::prefix('team')->group(function () {
    Route::get('/', [TeamController::class, 'index'])->name('team.index');
    Route::get('/{slug}', [TeamController::class, 'show'])->name('team.show');
});

Route::prefix('blog')->name('blog.')->group(function () {
    // Main blog page
    Route::get('/', [App\Http\Controllers\BlogController::class, 'index'])->name('index');
    Route::get('/{post:slug}', [App\Http\Controllers\BlogController::class, 'show'])->name('show');
});

Route::prefix('contact')->name('contact.')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('index');
    Route::post('/', [ContactController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('store');        
    Route::get('/thank-you', [ContactController::class, 'thankYou'])->name('thank-you');
});

Route::prefix('quotation')->name('quotation.')->group(function () {
    Route::get('/', [QuotationController::class, 'create'])->name('create');
    Route::post('/', [QuotationController::class, 'store'])->middleware('throttle:5,1')->name('quotation.store');
    Route::get('/thank-you', [QuotationController::class, 'thankYou'])->name('quotation.thank-you');
});

Route::post('/messages', [MessageController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('messages.store');

Route::prefix('api/chat')->group(function () {
    Route::get('/online-status', [App\Http\Controllers\ChatController::class, 'onlineStatus'])->name('api.chat.public-online-status');
    Route::get('/status', [App\Http\Controllers\ChatController::class, 'onlineStatus'])->name('api.chat.public-status');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Prevent redirect loop
        if (request()->routeIs('admin.dashboard') || request()->routeIs('client.dashboard')) {
            abort(404);
        }

        if ($user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('client')) {
            return redirect()->route('client.dashboard');
        }

        abort(403, 'Unauthorized');
    })->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        
        Route::get('/', [UnifiedProfileController::class, 'show'])->name('show');
        Route::get('/edit', [UnifiedProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [UnifiedProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [UnifiedProfileController::class, 'showChangePasswordForm'])->name('change-password');
        Route::patch('/password', [UnifiedProfileController::class, 'updatePassword'])->name('password.update');
        Route::get('/preferences', [UnifiedProfileController::class, 'preferences'])->name('preferences');
        Route::patch('/preferences', [UnifiedProfileController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/completion', [UnifiedProfileController::class, 'completion'])->name('completion');
        Route::get('/export', [UnifiedProfileController::class, 'export'])->name('export');
        Route::get('/delete', [UnifiedProfileController::class, 'showDeleteForm'])->name('delete');
        Route::delete('/', [UnifiedProfileController::class, 'destroy'])->name('destroy');
        Route::get('/completion-status', [UnifiedProfileController::class, 'completionStatus'])->name('completion-status');
        Route::get('/activity-summary', [UnifiedProfileController::class, 'activitySummary'])->name('activity-summary');
        
    });
    

    Route::get('/chat/notifications', function () {
        $user = auth()->user();
        
        if ($user->hasAdminAccess()) {
            // Get chat notifications for operators
            $notifications = $user->notifications()
                ->where('type', 'like', '%Chat%')
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } else {
            // Get chat notifications for clients
            $notifications = $user->notifications()
                ->where('type', 'like', '%chat%')
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $notifications->count()
        ]);
    })->name('chat.notifications');

    Route::post('/api/analytics/track', fn() => response()->json(['success' => true]))
        ->name('api.analytics.track');

    Route::get('/chat/websocket-auth', function () {
        return response()->json([
            'auth' => auth()->check(),
            'user_id' => auth()->id(),
            'is_admin' => auth()->check() && auth()->user()->hasAdminAccess(),
        ]);
    })->name('chat.websocket-auth');
    
    Route::prefix('api/chat')->group(function () {
        Route::post('/start', [App\Http\Controllers\ChatController::class, 'start'])->name('api.chat.start');
        Route::get('/session', [App\Http\Controllers\ChatController::class, 'getSession'])->name('api.chat.session');
        Route::post('/close', [App\Http\Controllers\ChatController::class, 'close'])->name('api.chat.close');
        Route::post('/send-message', [App\Http\Controllers\ChatController::class, 'sendMessage'])
            ->middleware('throttle:30,1')
            ->name('api.chat.send-message');
        Route::post('/typing', [App\Http\Controllers\ChatController::class, 'sendTyping'])
            ->middleware('throttle:60,1')
            ->name('api.chat.typing');
        Route::get('/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('api.chat.messages');
        Route::post('/update-info', [App\Http\Controllers\ChatController::class, 'updateClientInfo'])->name('api.chat.update-info');
        Route::get('/history', [App\Http\Controllers\ChatController::class, 'history'])->name('api.chat.history');
        Route::get('/online-status', [App\Http\Controllers\ChatController::class, 'onlineStatus'])->name('api.chat.online-status');
    });

    Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
        Route::prefix('analytics')->name('analytics.')->group(function () {
            
            // Basic Analytics Operations
            Route::post('/refresh', [DashboardController::class, 'refreshAnalytics'])->name('refresh');
            Route::get('/status', [DashboardController::class, 'getAnalyticsStatus'])->name('status');
            Route::post('/clear-cache', [DashboardController::class, 'clearAnalyticsCache'])->name('clear-cache');
            
            // KPI Dashboard
            Route::prefix('kpi')->name('kpi.')->group(function () {
                
                // Main KPI Endpoints
                Route::get('/dashboard', [DashboardController::class, 'getKPIDashboard'])->name('dashboard');
                Route::get('/realtime', [DashboardController::class, 'getRealTimeKPISummary'])->name('realtime');
                Route::get('/alerts', [DashboardController::class, 'getKPIAlerts'])->name('alerts');
                Route::get('/export', [DashboardController::class, 'exportKPIData'])->name('export');
                
                // Category-Specific KPIs
                Route::get('/category/{category}', [DashboardController::class, 'getKPICategory'])
                    ->name('category')
                    ->where('category', 'overview|traffic|engagement|conversion|audience|acquisition|behavior|technical');
                
                // Bulk Operations
                Route::post('/bulk', function(Request $request) {
                    try {
                        $categories = $request->input('categories', ['overview']);
                        $period = $request->input('period', 30);
                        
                        $googleAnalytics = app(GoogleAnalyticsService::class);
                        
                        $data = [];
                        foreach ($categories as $category) {
                            $methodName = 'get' . ucfirst($category) . 'KPIs';
                            if (method_exists($googleAnalytics, $methodName)) {
                                $data[$category] = $googleAnalytics->$methodName($period);
                            }
                        }
                        
                        return response()->json([
                            'success' => true,
                            'data' => $data,
                            'meta' => [
                                'categories' => $categories,
                                'period' => $period,
                                'generated_at' => now()->toISOString()
                            ]
                        ]);
                        
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Bulk KPI data fetch failed',
                            'error' => $e->getMessage()
                        ], 500);
                    }
                })->name('bulk');
                
                // Specific Metrics
                Route::get('/metrics/{metric}', function($metric, Request $request) {
                    try {
                        $period = $request->get('period', 30);
                        $googleAnalytics = app(GoogleAnalyticsService::class);
                        
                        $validMetrics = [
                            'users' => 'getOverviewKPIs',
                            'sessions' => 'getOverviewKPIs', 
                            'pageviews' => 'getOverviewKPIs',
                            'bounce_rate' => 'getEngagementKPIs',
                            'session_duration' => 'getEngagementKPIs',
                            'conversions' => 'getConversionKPIs'
                        ];
                        
                        if (!isset($validMetrics[$metric])) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid metric',
                                'valid_metrics' => array_keys($validMetrics)
                            ], 400);
                        }
                        
                        $methodName = $validMetrics[$metric];
                        $data = $googleAnalytics->$methodName($period);
                        
                        // Extract specific metric if it's from overview
                        if ($methodName === 'getOverviewKPIs' && isset($data[$metric])) {
                            $data = $data[$metric];
                        }
                        
                        return response()->json([
                            'success' => true,
                            'data' => $data,
                            'meta' => [
                                'metric' => $metric,
                                'period' => $period,
                                'method' => $methodName
                            ]
                        ]);
                        
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => "Failed to fetch {$metric} data",
                            'error' => $e->getMessage()
                        ], 500);
                    }
                })->name('metric')
                ->where('metric', 'users|sessions|pageviews|bounce_rate|session_duration|conversions');
                
                // Comparisons
                Route::post('/compare', function(Request $request) {
                    try {
                        $periods = $request->input('periods', [30, 30]); // [current, previous]
                        $category = $request->input('category', 'overview');
                        
                        $googleAnalytics = app(GoogleAnalyticsService::class);
                        $methodName = 'get' . ucfirst($category) . 'KPIs';
                        
                        if (!method_exists($googleAnalytics, $methodName)) {
                            throw new \Exception("Invalid category: {$category}");
                        }
                        
                        $currentData = $googleAnalytics->$methodName($periods[0]);
                        $previousData = $googleAnalytics->$methodName($periods[1] ?? $periods[0]);
                        
                        // Calculate comparison
                        $comparison = [];
                        if (is_array($currentData) && is_array($previousData)) {
                            foreach ($currentData as $key => $current) {
                                if (isset($previousData[$key])) {
                                    $previous = $previousData[$key];
                                    if (is_numeric($current) && is_numeric($previous)) {
                                        $change = $previous > 0 ? round((($current - $previous) / $previous) * 100, 2) : 0;
                                        $comparison[$key] = [
                                            'current' => $current,
                                            'previous' => $previous,
                                            'change_percent' => $change,
                                            'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable')
                                        ];
                                    }
                                }
                            }
                        }
                        
                        return response()->json([
                            'success' => true,
                            'data' => [
                                'current' => $currentData,
                                'previous' => $previousData,
                                'comparison' => $comparison
                            ],
                            'meta' => [
                                'category' => $category,
                                'periods' => $periods,
                                'compared_at' => now()->toISOString()
                            ]
                        ]);
                        
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Comparison failed',
                            'error' => $e->getMessage()
                        ], 500);
                    }
                })->name('compare');
                
                // Cache Management
                Route::post('/cache/clear', function() {
                    try {
                        $user = auth()->user();
                        $googleAnalytics = app(GoogleAnalyticsService::class);
                        
                        $googleAnalytics->clearKPICache();
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'KPI cache cleared successfully',
                            'data' => [
                                'cleared_at' => now()->toISOString(),
                                'cleared_by' => $user->name,
                                'cache_types' => ['kpi_dashboard', 'realtime_summary', 'category_data']
                            ]
                        ]);
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to clear KPI cache',
                            'error' => $e->getMessage()
                        ], 500);
                    }
                })->name('cache.clear');
                
                // Connection Testing
                Route::get('/test-connection', function() {
                    try {
                        $googleAnalytics = app(GoogleAnalyticsService::class);
                        $connectionTest = $googleAnalytics->testAnalyticsConnection();
                        
                        return response()->json([
                            'success' => $connectionTest['status'] === 'connected',
                            'data' => $connectionTest,
                            'meta' => [
                                'tested_by' => auth()->user()->name,
                                'tested_at' => now()->toISOString(),
                                'service' => 'Google Analytics 4 API'
                            ]
                        ]);
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Connection test failed',
                            'error' => $e->getMessage()
                        ], 500);
                    }
                })->name('test-connection');
                
                // Refresh Operations
                Route::middleware('throttle:30,1')->post('/refresh/{category?}', function($category = null, Request $request) {
                    try {
                        $period = $request->get('period', 30);
                        $googleAnalytics = app(GoogleAnalyticsService::class);
                        
                        // Clear cache first
                        $googleAnalytics->clearKPICache();
                        
                        if ($category) {
                            $methodName = 'get' . ucfirst($category) . 'KPIs';
                            if (!method_exists($googleAnalytics, $methodName)) {
                                throw new \Exception("Invalid category: {$category}");
                            }
                            $data = $googleAnalytics->$methodName($period);
                        } else {
                            // Refresh all KPI data
                            $data = $googleAnalytics->getKPIDashboard($period);
                        }
                        
                        return response()->json([
                            'success' => true,
                            'data' => $data,
                            'meta' => [
                                'refreshed_at' => now()->toISOString(),
                                'category' => $category ?: 'all',
                                'cache_cleared' => true,
                                'period' => $period
                            ]
                        ]);
                        
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Refresh failed',
                            'error' => $e->getMessage()
                        ], 500);
                    }
                })->name('refresh');
            });
            
            // Analytics Health & Trends
            Route::get('/health', function() {
                try {
                    $googleAnalytics = app(GoogleAnalyticsService::class);
                    $connectionTest = $googleAnalytics->testAnalyticsConnection();
                    
                    $health = [
                        'status' => $connectionTest['status'] === 'connected' ? 'healthy' : 'degraded',
                        'connection' => $connectionTest,
                        'cache' => [
                            'status' => 'operational',
                            'last_clear' => \Cache::get('analytics.cache.last_clear', 'Never'),
                        ],
                        'api' => [
                            'response_time' => 'Normal',
                            'quota_usage' => 'Within limits',
                            'last_error' => \Cache::get('analytics.last_error', null),
                        ],
                        'timestamp' => now()->toISOString()
                    ];
                    
                    return response()->json([
                        'success' => true,
                        'health' => $health
                    ]);
                    
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'health' => [
                            'status' => 'error',
                            'message' => 'Health check failed',
                            'error' => $e->getMessage(),
                            'timestamp' => now()->toISOString()
                        ]
                    ], 500);
                }
            })->name('health');
            
            Route::get('/trends/{period?}', function($period = 30, Request $request) {
                try {
                    $period = max(1, min(365, (int)$period));
                    $googleAnalytics = app(GoogleAnalyticsService::class);
                    
                    $trends = $googleAnalytics->getTrendAnalysis($period);
                    
                    return response()->json([
                        'success' => true,
                        'data' => $trends,
                        'meta' => [
                            'period' => $period,
                            'generated_at' => now()->toISOString(),
                            'analysis_type' => 'trend_analysis'
                        ]
                    ]);
                    
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Trends analysis failed',
                        'error' => $e->getMessage()
                    ], 500);
                }
            })->name('trends')->where('period', '[0-9]+');
        });
        
    });
});

Route::get('/robots.txt', [RobotsController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Role-Based Area Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/client.php';
require __DIR__ . '/admin.php';

/*
|--------------------------------------------------------------------------
| Redirect Routes
|--------------------------------------------------------------------------
*/
Route::redirect('/admin', '/admin/dashboard');
Route::redirect('/client', '/client/dashboard');

// SEO & Sitemap Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-client.xml', [SitemapController::class, 'clientSitemap'])->name('sitemap.client');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

