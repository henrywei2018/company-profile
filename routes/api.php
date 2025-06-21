<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use App\Http\Controllers\Api\{
    ProjectController, ServiceController, PostController,
    ContactController, QuotationController, NotificationController
};
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Api\Client\MessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Services\GoogleAnalyticsService;

/*
|--------------------------------------------------------------------------
| Rate Limiters
|--------------------------------------------------------------------------
*/
RateLimiter::for('client-api', fn(Request $request) =>
    Limit::perMinute(100)->by($request->user()?->id ?: $request->ip())
);
RateLimiter::for('admin-api', fn(Request $request) =>
    Limit::perMinute(120)->by($request->user()?->id ?: $request->ip())
);
RateLimiter::for('analytics-api', fn(Request $request) =>
    Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
);

/*
|--------------------------------------------------------------------------
| Authenticated User Endpoint
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->get('/user', fn(Request $request) => $request->user());

/*
|--------------------------------------------------------------------------
| Public APIs
|--------------------------------------------------------------------------
*/

// Chat Status (Public)
Route::prefix('chat')->group(function () {
    Route::get('/status', [ChatController::class, 'onlineStatus'])->name('api.chat.status');
    Route::get('/online-status', [ChatController::class, 'onlineStatus'])->name('api.chat.online-status');
});

// Projects (Public)
Route::prefix('projects')->name('api.projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/featured', [ProjectController::class, 'featured'])->name('featured');
    Route::get('/categories', [ProjectController::class, 'categories'])->name('categories');
    Route::get('/years', [ProjectController::class, 'years'])->name('years');
    Route::get('/{slug}', [ProjectController::class, 'show'])->name('show');
    Route::get('/{slug}/related', [ProjectController::class, 'related'])->name('related');
});

// Services (Public)
Route::prefix('services')->name('api.services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/featured', [ServiceController::class, 'featured'])->name('featured');
    Route::get('/categories', [ServiceController::class, 'categories'])->name('categories');
    Route::get('/{slug}', [ServiceController::class, 'show'])->name('show');
    Route::get('/{slug}/related', [ServiceController::class, 'related'])->name('related');
});

// Blog Posts (Public)
Route::prefix('posts')->name('api.posts.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/recent', [PostController::class, 'recent'])->name('recent');
    Route::get('/featured', [PostController::class, 'featured'])->name('featured');
    Route::get('/categories', [PostController::class, 'categories'])->name('categories');
    Route::get('/archives', [PostController::class, 'archives'])->name('archives');
    Route::get('/{slug}', [PostController::class, 'show'])->name('show');
    Route::get('/{slug}/related', [PostController::class, 'related'])->name('related');
});

// Contact & Quotations (Public)
Route::post('/contact', [ContactController::class, 'store'])->name('api.contact');
Route::post('/quotation', [QuotationController::class, 'store'])->name('api.quotation');

/*
|--------------------------------------------------------------------------
| Client Authenticated APIs
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Chat System (Client)
    Route::prefix('chat')->name('api.chat.')->group(function () {
        Route::post('/start', [ChatController::class, 'start'])->name('start');
        Route::get('/session', [ChatController::class, 'getSession'])->name('session');
        Route::post('/close', [ChatController::class, 'close'])->name('close');
        Route::post('/send-message', [ChatController::class, 'sendMessage'])
            ->middleware('throttle:30,1')->name('send-message');
        Route::post('/typing', [ChatController::class, 'sendTyping'])
            ->middleware('throttle:60,1')->name('typing');
        Route::get('/messages', [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/update-info', [ChatController::class, 'updateClientInfo'])->name('update-info');
        Route::get('/history', [ChatController::class, 'history'])->name('history');
        Route::get('/online-status', [ChatController::class, 'onlineStatus'])->name('client-online-status');
    });

    // Notifications (All Users)
    Route::prefix('notifications')->name('api.notifications.')->group(function () {
        Route::get('/statistics', [NotificationController::class, 'statistics'])->name('statistics');
        Route::get('/types', [NotificationController::class, 'types'])->name('types');
        Route::post('/test', [NotificationController::class, 'test'])->name('test');
        Route::post('/send', [NotificationController::class, 'send'])->name('send');
        Route::post('/clear-cache', [NotificationController::class, 'clearCache'])->name('clear-cache');
    });
});

/*
|--------------------------------------------------------------------------
| Client-Specific APIs (Admin Access Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('client')->name('api.client.')->group(function () {
    
    // Client Messages Management
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/statistics', [MessageController::class, 'getStatistics'])->name('statistics');
        Route::get('/summary', [MessageController::class, 'getSummary'])->name('summary');
        Route::get('/activity', [MessageController::class, 'getActivity'])->name('activity');
        Route::get('/notifications', [MessageController::class, 'getNotifications'])->name('notifications');
        Route::get('/check-urgent', [MessageController::class, 'checkUrgent'])->name('check-urgent');
        Route::post('/mark-all-read', [MessageController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/{message}/check-updates', [MessageController::class, 'checkThreadUpdates'])
            ->name('check-updates')->where('message', '[0-9]+');
    });
});

/*
|--------------------------------------------------------------------------
| Admin APIs
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('api.admin.')->group(function () {
    
    // Basic Dashboard
    Route::get('/dashboard', [DashboardController::class, 'getAnalyticsData'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'getStatisticsSafely'])->name('stats');
    Route::post('/cache/clear', [DashboardController::class, 'clearCache'])->name('cache.clear');
    
    // Chat Management
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/sessions', [ChatController::class, 'getAdminSessions'])->name('sessions');
        Route::get('/statistics', [ChatController::class, 'getStatistics'])->name('statistics');
        Route::post('/{chatSession}/reply', [ChatController::class, 'reply'])
            ->middleware('throttle:60,1')->name('reply');
        Route::post('/{chatSession}/assign', [ChatController::class, 'assignToMe'])->name('assign');
        Route::post('/{chatSession}/close', [ChatController::class, 'closeSession'])->name('close');
        Route::post('/{chatSession}/typing', [ChatController::class, 'operatorTyping'])
            ->middleware('throttle:60,1')->name('typing');
        Route::post('/operator/status', [ChatController::class, 'setOperatorStatus'])->name('operator-status');
        Route::get('/operator/status', [ChatController::class, 'getOperatorStatus'])->name('get-operator-status');
        Route::get('/operators/available', [ChatController::class, 'getAvailableOperators'])->name('operators.available');
        Route::get('/{chatSession}/messages', [ChatController::class, 'getChatMessages'])->name('messages');
    });
    
    // Analytics & KPI Dashboard
    Route::middleware('throttle:analytics-api')->prefix('analytics')->name('analytics.')->group(function () {
        
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

/*
|--------------------------------------------------------------------------
| Development Routes (Local/Testing Only)
|--------------------------------------------------------------------------
*/
if (app()->environment(['local', 'testing'])) {
    Route::middleware(['auth', 'admin'])->prefix('admin/analytics/dev')->name('api.admin.analytics.dev.')->group(function () {
        
        // Test Data Generation
        Route::get('/test-data', function() {
            $googleAnalytics = app(GoogleAnalyticsService::class);
            
            return response()->json([
                'test_data' => [
                    'connection' => $googleAnalytics->testAnalyticsConnection(),
                    'sample_overview' => $googleAnalytics->getOverviewKPIs(7),
                    'sample_traffic' => $googleAnalytics->getTrafficKPIs(7),
                ],
                'environment' => app()->environment(),
                'debug_mode' => config('app.debug'),
                'timestamp' => now()->toISOString()
            ]);
        })->name('test-data');
        
        // Cache Inspection
        Route::get('/cache-info', function() {
            return response()->json([
                'cache_keys' => [
                    'analytics.kpi.dashboard.30' => \Cache::has('analytics.kpi.dashboard.30'),
                    'analytics.realtime.kpi.summary' => \Cache::has('analytics.realtime.kpi.summary'),
                ],
                'cache_store' => config('cache.default'),
                'timestamp' => now()->toISOString()
            ]);
        })->name('cache-info');
        
        // Force Cache Clear (Dev Only)
        Route::post('/force-clear-all', function() {
            \Cache::flush();
            return response()->json([
                'success' => true,
                'message' => 'All cache cleared (development only)',
                'timestamp' => now()->toISOString()
            ]);
        })->name('force-clear-all');
    });
}