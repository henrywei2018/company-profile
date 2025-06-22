<?php

namespace App\Services;

use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleAnalyticsService
{
    protected array $cacheStrategy = [
        'realtime' => 2,        // 2 minutes for real-time KPIs
        'hourly' => 15,         // 15 minutes for hourly KPIs
        'daily' => 60,          // 1 hour for daily KPIs
        'weekly' => 240,        // 4 hours for weekly KPIs
        'monthly' => 1440,      // 24 hours for monthly KPIs
    ];

    protected array $defaultMetrics = [
        'sessions',
        'totalUsers',
        'screenPageViews',  
        'bounceRate',
        'averageSessionDuration',
        'conversions',
        'eventCount',
        'activeUsers',
        'newUsers',
        'engagementRate'
    ];

    protected array $defaultDimensions = [
        'date',
        'country',
        'city',
        'deviceCategory',
        'operatingSystem',
        'browser',
        'sessionDefaultChannelGroup',  
        'sessionSource',              
        'sessionMedium',             
        'pagePath',
        'pageTitle'
    ];

    /**
     * Get comprehensive KPI dashboard data
     */
    public function getKPIDashboard(int $period = 30): array
    {
        $cacheKey = "analytics.kpi.dashboard.{$period}";
        $cacheTime = $this->getCacheTimeForPeriod($period);
        
        return Cache::remember($cacheKey, $cacheTime, function () use ($period, $cacheTime) {
            try {
                return [
                    'overview' => $this->getOverviewKPIs($period),
                    'traffic' => $this->getTrafficKPIs($period),
                    'engagement' => $this->getEngagementKPIs($period),
                    'conversion' => $this->getConversionKPIs($period),
                    'audience' => $this->getAudienceKPIs($period),
                    'acquisition' => $this->getAcquisitionKPIs($period),
                    'behavior' => $this->getBehaviorKPIs($period),
                    'technical' => $this->getTechnicalKPIs($period),
                    'trends' => $this->getTrendAnalysis($period),
                    'alerts' => $this->getKPIAlerts($period),
                    'meta' => [
                        'period_days' => $period,
                        'generated_at' => now()->toISOString(),
                        'data_freshness' => 'GA4 data (1-4 hours delay)',
                        'cache_expires' => now()->addMinutes($cacheTime)->toISOString(),
                    ]
                ];
            } catch (\Exception $e) {
                Log::error('KPI Dashboard error: ' . $e->getMessage());
                return $this->getEmptyKPIDashboard($period);
            }
        });
    }

    /**
     * Get overview KPIs - most important metrics at a glance
     */
    public function getOverviewKPIs(int $period = 30): array
    {
        $currentPeriod = Period::days($period);
        $previousPeriod = Period::create(
            Carbon::now()->subDays($period * 2),
            Carbon::now()->subDays($period)
        );

        try {
            
            $current = $this->getMetricsForPeriod($currentPeriod, [
                'totalUsers', 'sessions', 'screenPageViews', 'bounceRate', 
                'averageSessionDuration', 'conversions'
            ]);

            $previous = $this->getMetricsForPeriod($previousPeriod, [
                'totalUsers', 'sessions', 'screenPageViews', 'bounceRate', 
                'averageSessionDuration', 'conversions'
            ]);

            return [
                'total_users' => [
                    'current' => $current['totalUsers'] ?? 0,
                    'previous' => $previous['totalUsers'] ?? 0,
                    'change_percent' => $this->calculatePercentageChange(
                        $current['totalUsers'] ?? 0, 
                        $previous['totalUsers'] ?? 0
                    ),
                    'trend' => $this->getTrendDirection($current['totalUsers'] ?? 0, $previous['totalUsers'] ?? 0),
                    'target' => $this->getKPITarget('total_users', $period),
                    'status' => $this->getKPIStatus('total_users', $current['totalUsers'] ?? 0, $period)
                ],
                'sessions' => [
                    'current' => $current['sessions'] ?? 0,
                    'previous' => $previous['sessions'] ?? 0,
                    'change_percent' => $this->calculatePercentageChange(
                        $current['sessions'] ?? 0, 
                        $previous['sessions'] ?? 0
                    ),
                    'trend' => $this->getTrendDirection($current['sessions'] ?? 0, $previous['sessions'] ?? 0),
                    'target' => $this->getKPITarget('sessions', $period),
                    'status' => $this->getKPIStatus('sessions', $current['sessions'] ?? 0, $period)
                ],
                
                'pageviews' => [
                    'current' => $current['screenPageViews'] ?? 0,
                    'previous' => $previous['screenPageViews'] ?? 0,
                    'change_percent' => $this->calculatePercentageChange(
                        $current['screenPageViews'] ?? 0, 
                        $previous['screenPageViews'] ?? 0
                    ),
                    'trend' => $this->getTrendDirection($current['screenPageViews'] ?? 0, $previous['screenPageViews'] ?? 0),
                    'target' => $this->getKPITarget('pageviews', $period),
                    'status' => $this->getKPIStatus('pageviews', $current['screenPageViews'] ?? 0, $period)
                ],
                'bounce_rate' => [
                    'current' => round(($current['bounceRate'] ?? 0) * 100, 2),
                    'previous' => round(($previous['bounceRate'] ?? 0) * 100, 2),
                    'change_percent' => $this->calculatePercentageChange(
                        $current['bounceRate'] ?? 0, 
                        $previous['bounceRate'] ?? 0
                    ),
                    'trend' => $this->getTrendDirection($previous['bounceRate'] ?? 0, $current['bounceRate'] ?? 0), // Inverted for bounce rate
                    'target' => $this->getKPITarget('bounce_rate', $period),
                    'status' => $this->getKPIStatus('bounce_rate', ($current['bounceRate'] ?? 0) * 100, $period)
                ],
                'avg_session_duration' => [
                    'current' => round($current['averageSessionDuration'] ?? 0, 0),
                    'previous' => round($previous['averageSessionDuration'] ?? 0, 0),
                    'change_percent' => $this->calculatePercentageChange(
                        $current['averageSessionDuration'] ?? 0, 
                        $previous['averageSessionDuration'] ?? 0
                    ),
                    'trend' => $this->getTrendDirection($current['averageSessionDuration'] ?? 0, $previous['averageSessionDuration'] ?? 0),
                    'target' => $this->getKPITarget('avg_session_duration', $period),
                    'status' => $this->getKPIStatus('avg_session_duration', $current['averageSessionDuration'] ?? 0, $period)
                ],
                'conversions' => [
                    'current' => $current['conversions'] ?? 0,
                    'previous' => $previous['conversions'] ?? 0,
                    'change_percent' => $this->calculatePercentageChange(
                        $current['conversions'] ?? 0, 
                        $previous['conversions'] ?? 0
                    ),
                    'trend' => $this->getTrendDirection($current['conversions'] ?? 0, $previous['conversions'] ?? 0),
                    'target' => $this->getKPITarget('conversions', $period),
                    'status' => $this->getKPIStatus('conversions', $current['conversions'] ?? 0, $period)
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Overview KPIs error: ' . $e->getMessage());
            return $this->getEmptyOverviewKPIs();
        }
    }

    /**
     * Get traffic-specific KPIs
     */
    public function getTrafficKPIs(int $period = 30): array
    {
        try {
            $periodObj = Period::days($period);
            
            
            $trafficSources = $this->getCustomData(['sessions'], ['sessionSource'], $periodObj);
            $channels = $this->getCustomData(['sessions'], ['sessionDefaultChannelGroup'], $periodObj);
            $mediums = $this->getCustomData(['sessions'], ['sessionMedium'], $periodObj);
            
            // Get daily traffic for trend analysis
            $dailyTraffic = $this->getCustomData(['sessions', 'totalUsers'], ['date'], $periodObj);
            
            return [
                'top_sources' => $trafficSources->sortByDesc('sessions')->take(10)->values()->toArray(),
                'channel_distribution' => $channels->sortByDesc('sessions')->toArray(),
                'medium_breakdown' => $mediums->sortByDesc('sessions')->toArray(),
                'organic_traffic' => [
                    'sessions' => $trafficSources->where('sessionSource', 'google')->sum('sessions'),
                    'percentage' => $this->calculatePercentage(
                        $trafficSources->where('sessionSource', 'google')->sum('sessions'),
                        $trafficSources->sum('sessions')
                    )
                ],
                'direct_traffic' => [
                    'sessions' => $trafficSources->where('sessionSource', '(direct)')->sum('sessions'),
                    'percentage' => $this->calculatePercentage(
                        $trafficSources->where('sessionSource', '(direct)')->sum('sessions'),
                        $trafficSources->sum('sessions')
                    )
                ],
                'referral_traffic' => [
                    'sessions' => $channels->where('sessionDefaultChannelGroup', 'Referral')->sum('sessions'),
                    'percentage' => $this->calculatePercentage(
                        $channels->where('sessionDefaultChannelGroup', 'Referral')->sum('sessions'),
                        $channels->sum('sessions')
                    )
                ],
                'social_traffic' => [
                    'sessions' => $channels->where('sessionDefaultChannelGroup', 'Social')->sum('sessions'),
                    'percentage' => $this->calculatePercentage(
                        $channels->where('sessionDefaultChannelGroup', 'Social')->sum('sessions'),
                        $channels->sum('sessions')
                    )
                ],
                'traffic_trend' => $this->prepareChartData($dailyTraffic, 'sessions'),
                'user_trend' => $this->prepareChartData($dailyTraffic, 'totalUsers'),
            ];
        } catch (\Exception $e) {
            Log::error('Traffic KPIs error: ' . $e->getMessage());
            return $this->getEmptyTrafficKPIs();
        }
    }

    /**
     * Get engagement KPIs
     */
    public function getEngagementKPIs(int $period = 30): array
    {
        try {
            $periodObj = Period::days($period);
            
            // Get engagement metrics
            $engagementData = $this->getCustomData([
                'averageSessionDuration', 'bounceRate', 'pageviewsPerSession', 
                'eventCount', 'engagementRate'
            ], [], $periodObj);
            
            // Get page engagement
            $pageEngagement = $this->getCustomData([
                'averageSessionDuration', 'bounceRate'
            ], ['pagePath'], $periodObj);
            
            // Get event data
            $events = $this->getCustomData(['eventCount'], ['eventName'], $periodObj);
            
            return [
                'average_session_duration' => [
                    'value' => round($engagementData->first()['averageSessionDuration'] ?? 0, 2),
                    'formatted' => $this->formatDuration($engagementData->first()['averageSessionDuration'] ?? 0),
                    'benchmark' => '2-3 minutes',
                    'status' => $this->getEngagementStatus('duration', $engagementData->first()['averageSessionDuration'] ?? 0)
                ],
                'bounce_rate' => [
                    'value' => round(($engagementData->first()['bounceRate'] ?? 0) * 100, 2),
                    'benchmark' => '40-60%',
                    'status' => $this->getEngagementStatus('bounce_rate', ($engagementData->first()['bounceRate'] ?? 0) * 100)
                ],
                'pages_per_session' => [
                    'value' => round($engagementData->first()['pageviewsPerSession'] ?? 0, 2),
                    'benchmark' => '2-4 pages',
                    'status' => $this->getEngagementStatus('pages_per_session', $engagementData->first()['pageviewsPerSession'] ?? 0)
                ],
                'engagement_rate' => [
                    'value' => round(($engagementData->first()['engagementRate'] ?? 0) * 100, 2),
                    'benchmark' => '60-70%',
                    'status' => $this->getEngagementStatus('engagement_rate', ($engagementData->first()['engagementRate'] ?? 0) * 100)
                ],
                'total_events' => $events->sum('eventCount'),
                'top_events' => $events->sortByDesc('eventCount')->take(10)->values()->toArray(),
                'most_engaging_pages' => $pageEngagement
                    ->sortByDesc('averageSessionDuration')
                    ->take(10)
                    ->values()
                    ->toArray(),
                'highest_bounce_pages' => $pageEngagement
                    ->sortByDesc('bounceRate')
                    ->take(10)
                    ->values()
                    ->toArray(),
            ];
        } catch (\Exception $e) {
            Log::error('Engagement KPIs error: ' . $e->getMessage());
            return $this->getEmptyEngagementKPIs();
        }
    }

    /**
     * Get conversion KPIs
     */
    public function getConversionKPIs(int $period = 30): array
    {
        try {
            $periodObj = Period::days($period);
            
            // Get conversion data
            $conversions = $this->getCustomData([
                'conversions', 'sessions', 'totalUsers'
            ], [], $periodObj);
            
            // Get conversion by source
            $conversionBySource = $this->getCustomData([
                'conversions', 'sessions'
            ], ['source'], $periodObj);
            
            // Get conversion by page
            $conversionByPage = $this->getCustomData([
                'conversions', 'sessions'
            ], ['pagePath'], $periodObj);
            
            $totalConversions = $conversions->sum('conversions');
            $totalSessions = $conversions->sum('sessions');
            $totalUsers = $conversions->sum('totalUsers');
            
            return [
                'total_conversions' => $totalConversions,
                'conversion_rate' => [
                    'sessions' => $this->calculatePercentage($totalConversions, $totalSessions),
                    'users' => $this->calculatePercentage($totalConversions, $totalUsers),
                    'benchmark' => '2-5%',
                    'status' => $this->getConversionStatus($this->calculatePercentage($totalConversions, $totalSessions))
                ],
                'conversions_per_user' => round($totalUsers > 0 ? $totalConversions / $totalUsers : 0, 2),
                'top_converting_sources' => $conversionBySource
                    ->map(function ($item) {
                        $item['conversion_rate'] = $this->calculatePercentage($item['conversions'], $item['sessions']);
                        return $item;
                    })
                    ->sortByDesc('conversions')
                    ->take(10)
                    ->values()
                    ->toArray(),
                'top_converting_pages' => $conversionByPage
                    ->map(function ($item) {
                        $item['conversion_rate'] = $this->calculatePercentage($item['conversions'], $item['sessions']);
                        return $item;
                    })
                    ->sortByDesc('conversions')
                    ->take(10)
                    ->values()
                    ->toArray(),
                'conversion_funnel' => $this->getConversionFunnel($period),
            ];
        } catch (\Exception $e) {
            Log::error('Conversion KPIs error: ' . $e->getMessage());
            return $this->getEmptyConversionKPIs();
        }
    }

    /**
     * Get audience KPIs
     */
    public function getAudienceKPIs(int $period = 30): array
    {
        try {
            $periodObj = Period::days($period);
            
            // Get audience data
            $countries = $this->getCustomData(['sessions'], ['country'], $periodObj);
            $cities = $this->getCustomData(['sessions'], ['city'], $periodObj);
            $devices = $this->getCustomData(['sessions'], ['deviceCategory'], $periodObj);
            $browsers = $this->getCustomData(['sessions'], ['browser'], $periodObj);
            $os = $this->getCustomData(['sessions'], ['operatingSystem'], $periodObj);
            
            // Get user types (new vs returning)
            $userTypes = $this->getCustomData(['sessions'], ['newVsReturning'], $periodObj);
            
            return [
                'geographic_distribution' => [
                    'top_countries' => $countries->sortByDesc('sessions')->take(10)->values()->toArray(),
                    'top_cities' => $cities->sortByDesc('sessions')->take(10)->values()->toArray(),
                    'total_countries' => $countries->count(),
                    'total_cities' => $cities->count(),
                ],
                'device_breakdown' => [
                    'devices' => $devices->sortByDesc('sessions')->toArray(),
                    'mobile_percentage' => $this->calculatePercentage(
                        $devices->where('deviceCategory', 'mobile')->sum('sessions'),
                        $devices->sum('sessions')
                    ),
                    'desktop_percentage' => $this->calculatePercentage(
                        $devices->where('deviceCategory', 'desktop')->sum('sessions'),
                        $devices->sum('sessions')
                    ),
                    'tablet_percentage' => $this->calculatePercentage(
                        $devices->where('deviceCategory', 'tablet')->sum('sessions'),
                        $devices->sum('sessions')
                    ),
                ],
                'technology_profile' => [
                    'top_browsers' => $browsers->sortByDesc('sessions')->take(10)->values()->toArray(),
                    'top_operating_systems' => $os->sortByDesc('sessions')->take(10)->values()->toArray(),
                ],
                'user_loyalty' => [
                    'new_users_percentage' => $this->calculatePercentage(
                        $userTypes->where('newVsReturning', 'new')->sum('sessions'),
                        $userTypes->sum('sessions')
                    ),
                    'returning_users_percentage' => $this->calculatePercentage(
                        $userTypes->where('newVsReturning', 'returning')->sum('sessions'),
                        $userTypes->sum('sessions')
                    ),
                    'user_types' => $userTypes->toArray(),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Audience KPIs error: ' . $e->getMessage());
            return $this->getEmptyAudienceKPIs();
        }
    }

    /**
     * Get acquisition KPIs
     */
    public function getAcquisitionKPIs(int $period = 30): array
    {
        try {
            $periodObj = Period::days($period);
            
            // Get acquisition data
            $sources = $this->getCustomData(['sessions', 'totalUsers'], ['source'], $periodObj);
            $mediums = $this->getCustomData(['sessions', 'totalUsers'], ['medium'], $periodObj);
            $campaigns = $this->getCustomData(['sessions', 'totalUsers'], ['campaign'], $periodObj);
            
            return [
                'user_acquisition' => [
                    'total_new_users' => $sources->sum('totalUsers'),
                    'cost_per_acquisition' => 0, // Would need cost data from Google Ads API
                    'top_acquisition_sources' => $sources->sortByDesc('totalUsers')->take(10)->values()->toArray(),
                ],
                'session_acquisition' => [
                    'total_sessions' => $sources->sum('sessions'),
                    'average_sessions_per_user' => round($sources->sum('totalUsers') > 0 ? 
                        $sources->sum('sessions') / $sources->sum('totalUsers') : 0, 2),
                    'top_session_sources' => $sources->sortByDesc('sessions')->take(10)->values()->toArray(),
                ],
                'marketing_channels' => [
                    'organic_search' => $mediums->where('medium', 'organic')->sum('sessions'),
                    'paid_search' => $mediums->where('medium', 'cpc')->sum('sessions'),
                    'social_media' => $mediums->where('medium', 'social')->sum('sessions'),
                    'email' => $mediums->where('medium', 'email')->sum('sessions'),
                    'referral' => $mediums->where('medium', 'referral')->sum('sessions'),
                    'direct' => $mediums->where('medium', '(none)')->sum('sessions'),
                ],
                'campaign_performance' => $campaigns
                    ->filter(fn($item) => $item['campaign'] !== '(not set)')
                    ->sortByDesc('sessions')
                    ->take(10)
                    ->values()
                    ->toArray(),
            ];
        } catch (\Exception $e) {
            Log::error('Acquisition KPIs error: ' . $e->getMessage());
            return $this->getEmptyAcquisitionKPIs();
        }
    }

    /**
     * Get behavior KPIs
     */
    public function getBehaviorKPIs(int $period = 30): array
    {
        try {
            $periodObj = Period::days($period);
            
            // Get behavior data
            $pages = $this->getCustomData([
                'pageviews', 'uniquePageviews', 'averageTimeOnPage', 'exitRate'
            ], ['pagePath', 'pageTitle'], $periodObj);
            
            $landingPages = $this->getCustomData([
                'sessions', 'bounceRate'
            ], ['landingPage'], $periodObj);
            
            return [
                'content_performance' => [
                    'most_viewed_pages' => $pages->sortByDesc('pageviews')->take(10)->values()->toArray(),
                    'highest_time_on_page' => $pages->sortByDesc('averageTimeOnPage')->take(10)->values()->toArray(),
                    'highest_exit_rate' => $pages->sortByDesc('exitRate')->take(10)->values()->toArray(),
                    'total_pages_viewed' => $pages->count(),
                ],
                'landing_page_performance' => [
                    'top_landing_pages' => $landingPages->sortByDesc('sessions')->take(10)->values()->toArray(),
                    'best_converting_landing_pages' => $landingPages
                        ->sortBy('bounceRate')
                        ->take(10)
                        ->values()
                        ->toArray(),
                ],
                'site_search' => $this->getSiteSearchData($period),
                'user_flow' => $this->getUserFlowData($period),
            ];
        } catch (\Exception $e) {
            Log::error('Behavior KPIs error: ' . $e->getMessage());
            return $this->getEmptyBehaviorKPIs();
        }
    }

    /**
     * Get technical KPIs
     */
    public function getTechnicalKPIs(int $period = 30): array
    {
        try {
            $periodObj = Period::days($period);
            
            // Get technical performance data
            $loadTimes = $this->getCustomData(['avgPageLoadTime'], ['pagePath'], $periodObj);
            $errors = $this->getCustomData(['exceptions'], [], $periodObj);
            
            return [
                'site_speed' => [
                    'average_page_load_time' => round($loadTimes->avg('avgPageLoadTime') ?? 0, 2),
                    'slowest_pages' => $loadTimes->sortByDesc('avgPageLoadTime')->take(10)->values()->toArray(),
                    'fastest_pages' => $loadTimes->sortBy('avgPageLoadTime')->take(10)->values()->toArray(),
                    'speed_benchmark' => '3 seconds',
                    'status' => $this->getSpeedStatus($loadTimes->avg('avgPageLoadTime') ?? 0),
                ],
                'technical_issues' => [
                    'total_exceptions' => $errors->sum('exceptions'),
                    'error_rate' => $this->calculatePercentage($errors->sum('exceptions'), $errors->sum('sessions')),
                ],
                'mobile_performance' => $this->getMobilePerformanceData($period),
                'browser_compatibility' => $this->getBrowserCompatibilityData($period),
            ];
        } catch (\Exception $e) {
            Log::error('Technical KPIs error: ' . $e->getMessage());
            return $this->getEmptyTechnicalKPIs();
        }
    }

    /**
     * Get trend analysis across multiple timeframes
     */
    public function getTrendAnalysis(int $period = 30): array
    {
        try {
            $periods = [
                'daily' => Period::days($period),
                'weekly' => Period::days($period * 4), // 4x period for weekly comparison
                'monthly' => Period::days(365), // Full year for monthly trends
            ];
            
            $trends = [];
            
            foreach ($periods as $timeframe => $periodObj) {
                $data = $this->getCustomData(['sessions', 'totalUsers', 'conversions'], ['date'], $periodObj);
                
                $trends[$timeframe] = [
                    'sessions_trend' => $this->calculateTrendDirection($data, 'sessions'),
                    'users_trend' => $this->calculateTrendDirection($data, 'totalUsers'),
                    'conversions_trend' => $this->calculateTrendDirection($data, 'conversions'),
                    'volatility' => $this->calculateVolatility($data, 'sessions'),
                ];
            }
            
            return [
                'trends' => $trends,
                'forecasting' => $this->generateSimpleForecast($period),
                'seasonality' => $this->detectSeasonality($period),
                'anomalies' => $this->detectAnomalies($period),
            ];
        } catch (\Exception $e) {
            Log::error('Trend analysis error: ' . $e->getMessage());
            return $this->getEmptyTrendAnalysis();
        }
    }

    /**
     * Get KPI alerts and recommendations
     */
    public function getKPIAlerts(int $period = 30): array
    {
        try {
            $alerts = [];
            $overview = $this->getOverviewKPIs($period);
            
            // Check for significant drops
            foreach ($overview as $metric => $data) {
                if (isset($data['change_percent']) && $data['change_percent'] < -20) {
                    $alerts[] = [
                        'type' => 'warning',
                        'metric' => $metric,
                        'message' => ucfirst(str_replace('_', ' ', $metric)) . " has decreased by " . abs($data['change_percent']) . "%",
                        'severity' => $data['change_percent'] < -50 ? 'critical' : 'warning',
                        'recommendation' => $this->getRecommendation($metric, 'decrease'),
                    ];
                }
                
                if (isset($data['change_percent']) && $data['change_percent'] > 50) {
                    $alerts[] = [
                        'type' => 'success',
                        'metric' => $metric,
                        'message' => ucfirst(str_replace('_', ' ', $metric)) . " has increased by " . $data['change_percent'] . "%",
                        'severity' => 'info',
                        'recommendation' => $this->getRecommendation($metric, 'increase'),
                    ];
                }
            }
            
            return [
                'alerts' => $alerts,
                'alert_count' => count($alerts),
                'critical_count' => count(array_filter($alerts, fn($a) => $a['severity'] === 'critical')),
                'warning_count' => count(array_filter($alerts, fn($a) => $a['severity'] === 'warning')),
                'recommendations' => $this->getGeneralRecommendations($overview),
            ];
        } catch (\Exception $e) {
            Log::error('KPI alerts error: ' . $e->getMessage());
            return ['alerts' => [], 'alert_count' => 0, 'recommendations' => []];
        }
    }

    // Helper methods for calculations and formatting

    protected function getCustomData(array $metrics, array $dimensions = [], Period $period = null): Collection
    {
        $period = $period ?: Period::days(7);
        
        try {
            return Analytics::get($period, $metrics, $dimensions);
        } catch (\Exception $e) {
            Log::error('Custom data error: ' . json_encode([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'status' => method_exists($e, 'getStatus') ? $e->getStatus() : 'UNKNOWN',
                'details' => method_exists($e, 'getDetails') ? $e->getDetails() : []
            ]));
            return collect();
        }
    }

    protected function getMetricsForPeriod(Period $period, array $metrics): array
    {
        try {
            $data = $this->getCustomData($metrics, [], $period);
            return $data->first() ?? array_fill_keys($metrics, 0);
        } catch (\Exception $e) {
            return array_fill_keys($metrics, 0);
        }
    }

    protected function calculatePercentageChange($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }

    protected function getTrendDirection($current, $previous): string
    {
        if ($current > $previous) return 'up';
        if ($current < $previous) return 'down';
        return 'stable';
    }

    protected function calculatePercentage($value, $total): float
    {
        if ($total == 0) return 0;
        return round(($value / $total) * 100, 2);
    }

    protected function getKPITarget(string $metric, int $period): array
    {
        // Define KPI targets based on industry standards
        $targets = [
            'total_users' => ['target' => $period * 100, 'unit' => 'users'],
            'sessions' => ['target' => $period * 150, 'unit' => 'sessions'],
            'pageviews' => ['target' => $period * 300, 'unit' => 'views'],
            'bounce_rate' => ['target' => 50, 'unit' => '%', 'direction' => 'lower'],
            'avg_session_duration' => ['target' => 180, 'unit' => 'seconds'],
            'conversions' => ['target' => $period * 5, 'unit' => 'conversions'],
        ];

        return $targets[$metric] ?? ['target' => 0, 'unit' => ''];
    }

    protected function getKPIStatus(string $metric, $value, int $period): string
    {
        $target = $this->getKPITarget($metric, $period);
        $targetValue = $target['target'];
        $isLowerBetter = isset($target['direction']) && $target['direction'] === 'lower';

        if ($isLowerBetter) {
            if ($value <= $targetValue * 0.8) return 'excellent';
            if ($value <= $targetValue) return 'good';
            if ($value <= $targetValue * 1.2) return 'warning';
            return 'critical';
        } else {
            if ($value >= $targetValue * 1.2) return 'excellent';
            if ($value >= $targetValue) return 'good';
            if ($value >= $targetValue * 0.8) return 'warning';
            return 'critical';
        }
    }

    protected function getEngagementStatus(string $type, $value): string
    {
        return match($type) {
            'duration' => $value >= 180 ? 'good' : ($value >= 120 ? 'warning' : 'critical'),
            'bounce_rate' => $value <= 40 ? 'excellent' : ($value <= 60 ? 'good' : 'warning'),
            'pages_per_session' => $value >= 3 ? 'excellent' : ($value >= 2 ? 'good' : 'warning'),
            'engagement_rate' => $value >= 70 ? 'excellent' : ($value >= 50 ? 'good' : 'warning'),
            default => 'unknown'
        };
    }

    protected function getConversionStatus($rate): string
    {
        if ($rate >= 5) return 'excellent';
        if ($rate >= 3) return 'good';
        if ($rate >= 1) return 'warning';
        return 'critical';
    }

    protected function getSpeedStatus($loadTime): string
    {
        if ($loadTime <= 2) return 'excellent';
        if ($loadTime <= 3) return 'good';
        if ($loadTime <= 5) return 'warning';
        return 'critical';
    }

    protected function formatDuration($seconds): string
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return sprintf('%dm %ds', $minutes, $remainingSeconds);
    }

    protected function prepareChartData(Collection $data, string $metric): array
    {
        return $data->map(function ($item) use ($metric) {
            return [
                'date' => $item['date'],
                'value' => $item[$metric] ?? 0
            ];
        })->toArray();
    }

    protected function getConversionFunnel(int $period): array
    {
        try {
            $periodObj = Period::days($period);
            
            // Define funnel steps (customize based on your site)
            $funnelSteps = [
                'visitors' => $this->getCustomData(['totalUsers'], [], $periodObj)->sum('totalUsers'),
                'sessions' => $this->getCustomData(['sessions'], [], $periodObj)->sum('sessions'),
                'page_views' => $this->getCustomData(['pageviews'], [], $periodObj)->sum('pageviews'),
                'conversions' => $this->getCustomData(['conversions'], [], $periodObj)->sum('conversions'),
            ];

            $funnel = [];
            $previousStep = null;

            foreach ($funnelSteps as $step => $value) {
                $dropOffRate = $previousStep ? 
                    $this->calculatePercentage($previousStep - $value, $previousStep) : 0;
                
                $funnel[] = [
                    'step' => $step,
                    'value' => $value,
                    'drop_off_rate' => $dropOffRate,
                ];
                
                $previousStep = $value;
            }

            return $funnel;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getSiteSearchData(int $period): array
    {
        try {
            $periodObj = Period::days($period);
            
            // Get site search data if available
            $searchData = $this->getCustomData(['sessions'], ['searchTerm'], $periodObj);
            
            return [
                'total_searches' => $searchData->count(),
                'top_search_terms' => $searchData->sortByDesc('sessions')->take(10)->values()->toArray(),
                'search_to_conversion_rate' => 0, // Would need conversion tracking setup
            ];
        } catch (\Exception $e) {
            return ['total_searches' => 0, 'top_search_terms' => []];
        }
    }

    protected function getUserFlowData(int $period): array
    {
        try {
            $periodObj = Period::days($period);
            
            // Get basic user flow data
            $landingPages = $this->getCustomData(['sessions'], ['landingPage'], $periodObj);
            $exitPages = $this->getCustomData(['sessions'], ['exitPage'], $periodObj);
            
            return [
                'top_entry_points' => $landingPages->sortByDesc('sessions')->take(5)->values()->toArray(),
                'top_exit_points' => $exitPages->sortByDesc('sessions')->take(5)->values()->toArray(),
            ];
        } catch (\Exception $e) {
            return ['top_entry_points' => [], 'top_exit_points' => []];
        }
    }

    protected function getMobilePerformanceData(int $period): array
    {
        try {
            $periodObj = Period::days($period);
            
            $mobileData = $this->getCustomData([
                'sessions', 'bounceRate', 'averageSessionDuration'
            ], ['deviceCategory'], $periodObj);
            
            $mobile = $mobileData->where('deviceCategory', 'mobile')->first();
            $desktop = $mobileData->where('deviceCategory', 'desktop')->first();
            
            return [
                'mobile_vs_desktop_bounce_rate' => [
                    'mobile' => round(($mobile['bounceRate'] ?? 0) * 100, 2),
                    'desktop' => round(($desktop['bounceRate'] ?? 0) * 100, 2),
                ],
                'mobile_vs_desktop_duration' => [
                    'mobile' => round($mobile['averageSessionDuration'] ?? 0, 2),
                    'desktop' => round($desktop['averageSessionDuration'] ?? 0, 2),
                ],
                'mobile_optimization_score' => $this->calculateMobileOptimizationScore($mobile, $desktop),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getBrowserCompatibilityData(int $period): array
    {
        try {
            $periodObj = Period::days($period);
            
            $browserData = $this->getCustomData([
                'sessions', 'bounceRate'
            ], ['browser'], $periodObj);
            
            return [
                'browser_performance' => $browserData->map(function ($item) {
                    return [
                        'browser' => $item['browser'],
                        'sessions' => $item['sessions'],
                        'bounce_rate' => round(($item['bounceRate'] ?? 0) * 100, 2),
                        'performance_score' => $this->calculateBrowserPerformanceScore($item),
                    ];
                })->sortByDesc('sessions')->values()->toArray(),
            ];
        } catch (\Exception $e) {
            return ['browser_performance' => []];
        }
    }

    protected function calculateTrendDirection(Collection $data, string $metric): string
    {
        if ($data->count() < 2) return 'stable';
        
        $values = $data->pluck($metric)->toArray();
        $firstHalf = array_slice($values, 0, floor(count($values) / 2));
        $secondHalf = array_slice($values, floor(count($values) / 2));
        
        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);
        
        if ($secondAvg > $firstAvg * 1.05) return 'increasing';
        if ($secondAvg < $firstAvg * 0.95) return 'decreasing';
        return 'stable';
    }

    protected function calculateVolatility(Collection $data, string $metric): float
    {
        $values = $data->pluck($metric)->toArray();
        if (count($values) < 2) return 0;
        
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / count($values);
        
        return round(sqrt($variance), 2);
    }

    protected function generateSimpleForecast(int $period): array
    {
        try {
            $periodObj = Period::days($period);
            $historicalData = $this->getCustomData(['sessions'], ['date'], $periodObj);
            
            if ($historicalData->count() < 7) {
                return ['forecast' => [], 'confidence' => 'low'];
            }
            
            $values = $historicalData->pluck('sessions')->toArray();
            $trend = $this->calculateLinearTrend($values);
            
            // Simple 7-day forecast
            $forecast = [];
            $lastValue = end($values);
            
            for ($i = 1; $i <= 7; $i++) {
                $predictedValue = max(0, round($lastValue + ($trend * $i)));
                $forecast[] = [
                    'date' => now()->addDays($i)->format('Y-m-d'),
                    'predicted_sessions' => $predictedValue,
                ];
            }
            
            return [
                'forecast' => $forecast,
                'confidence' => count($values) >= 30 ? 'high' : 'medium',
                'trend_direction' => $trend > 0 ? 'increasing' : ($trend < 0 ? 'decreasing' : 'stable'),
            ];
        } catch (\Exception $e) {
            return ['forecast' => [], 'confidence' => 'low'];
        }
    }

    protected function calculateLinearTrend(array $values): float
    {
        $n = count($values);
        if ($n < 2) return 0;
        
        $x = range(1, $n);
        $sumX = array_sum($x);
        $sumY = array_sum($values);
        $sumXY = array_sum(array_map(function($xi, $yi) { return $xi * $yi; }, $x, $values));
        $sumX2 = array_sum(array_map(function($xi) { return $xi * $xi; }, $x));
        
        return ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
    }

    protected function detectSeasonality(int $period): array
    {
        try {
            // Simple seasonality detection based on day of week
            $periodObj = Period::days(min($period, 90)); // Limit to 90 days for performance
            $data = $this->getCustomData(['sessions'], ['date'], $periodObj);
            
            $dayOfWeekData = $data->groupBy(function ($item) {
                return Carbon::parse($item['date'])->dayOfWeek;
            })->map(function ($group) {
                return [
                    'average_sessions' => round($group->avg('sessions'), 2),
                    'day_count' => $group->count(),
                ];
            });
            
            return [
                'weekly_pattern' => $dayOfWeekData->toArray(),
                'strongest_day' => $dayOfWeekData->sortByDesc('average_sessions')->keys()->first(),
                'weakest_day' => $dayOfWeekData->sortBy('average_sessions')->keys()->first(),
            ];
        } catch (\Exception $e) {
            return ['weekly_pattern' => [], 'strongest_day' => null, 'weakest_day' => null];
        }
    }

    protected function detectAnomalies(int $period): array
    {
        try {
            $periodObj = Period::days($period);
            $data = $this->getCustomData(['sessions'], ['date'], $periodObj);
            
            $values = $data->pluck('sessions')->toArray();
            if (count($values) < 7) return ['anomalies' => []];
            
            $mean = array_sum($values) / count($values);
            $stdDev = sqrt(array_sum(array_map(function($x) use ($mean) {
                return pow($x - $mean, 2);
            }, $values)) / count($values));
            
            $anomalies = [];
            foreach ($data as $item) {
                $sessions = $item['sessions'];
                $zScore = abs(($sessions - $mean) / $stdDev);
                
                if ($zScore > 2) { // More than 2 standard deviations
                    $anomalies[] = [
                        'date' => $item['date'],
                        'sessions' => $sessions,
                        'expected_range' => [
                            'min' => round($mean - 2 * $stdDev),
                            'max' => round($mean + 2 * $stdDev),
                        ],
                        'severity' => $zScore > 3 ? 'high' : 'medium',
                    ];
                }
            }
            
            return ['anomalies' => $anomalies];
        } catch (\Exception $e) {
            return ['anomalies' => []];
        }
    }

    protected function getRecommendation(string $metric, string $direction): string
    {
        $recommendations = [
            'total_users' => [
                'decrease' => 'Consider improving SEO, increasing marketing spend, or launching acquisition campaigns.',
                'increase' => 'Great job! Focus on converting these new users and improving retention.',
            ],
            'sessions' => [
                'decrease' => 'Review traffic sources and optimize underperforming channels.',
                'increase' => 'Excellent growth! Ensure your site can handle increased traffic.',
            ],
            'bounce_rate' => [
                'increase' => 'Improve page load speed, content relevance, and user experience.',
                'decrease' => 'Great engagement! Consider A/B testing to optimize further.',
            ],
            'avg_session_duration' => [
                'decrease' => 'Enhance content quality and internal linking to increase engagement.',
                'increase' => 'Users are engaged! Consider adding conversion opportunities.',
            ],
            'conversions' => [
                'decrease' => 'Review conversion funnel, test different CTAs, and optimize checkout process.',
                'increase' => 'Fantastic results! Scale successful strategies and test new opportunities.',
            ],
        ];
        
        return $recommendations[$metric][$direction] ?? 'Monitor this metric closely and investigate potential causes.';
    }

    protected function getGeneralRecommendations(array $overview): array
    {
        $recommendations = [];
        
        // Analyze overall performance and provide recommendations
        if (isset($overview['bounce_rate']['current']) && $overview['bounce_rate']['current'] > 70) {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'user_experience',
                'recommendation' => 'High bounce rate detected. Focus on improving page load speed and content relevance.',
            ];
        }
        
        if (isset($overview['avg_session_duration']['current']) && $overview['avg_session_duration']['current'] < 60) {
            $recommendations[] = [
                'priority' => 'medium',
                'category' => 'engagement',
                'recommendation' => 'Low session duration. Consider improving content quality and internal navigation.',
            ];
        }
        
        if (isset($overview['conversions']['current']) && $overview['conversions']['current'] == 0) {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'conversion',
                'recommendation' => 'No conversions detected. Set up conversion tracking and optimize conversion paths.',
            ];
        }
        
        return $recommendations;
    }

    protected function calculateMobileOptimizationScore($mobile, $desktop): int
    {
        if (!$mobile || !$desktop) return 50;
        
        $bounceRateDiff = ($mobile['bounceRate'] ?? 0) - ($desktop['bounceRate'] ?? 0);
        $durationDiff = ($mobile['averageSessionDuration'] ?? 0) - ($desktop['averageSessionDuration'] ?? 0);
        
        $score = 100;
        
        // Penalize if mobile bounce rate is significantly higher
        if ($bounceRateDiff > 0.1) $score -= 20;
        if ($bounceRateDiff > 0.2) $score -= 20;
        
        // Penalize if mobile session duration is significantly lower
        if ($durationDiff < -30) $score -= 15;
        if ($durationDiff < -60) $score -= 15;
        
        return max(0, min(100, $score));
    }

    protected function calculateBrowserPerformanceScore(array $browserData): int
    {
        $bounceRate = $browserData['bounceRate'] ?? 0;
        $sessions = $browserData['sessions'] ?? 0;
        
        $score = 100;
        
        // Penalize high bounce rates
        if ($bounceRate > 0.6) $score -= 20;
        if ($bounceRate > 0.8) $score -= 30;
        
        // Consider session volume (low volume might indicate compatibility issues)
        if ($sessions < 10) $score -= 10;
        
        return max(0, min(100, $score));
    }

    protected function getCacheTimeForPeriod(int $period): int
    {
        if ($period <= 7) return $this->cacheStrategy['daily'];
        if ($period <= 30) return $this->cacheStrategy['weekly'];
        return $this->cacheStrategy['monthly'];
    }

    // Empty data methods for error handling

    protected function getEmptyKPIDashboard(int $period): array
    {
        return [
            'overview' => $this->getEmptyOverviewKPIs(),
            'traffic' => $this->getEmptyTrafficKPIs(),
            'engagement' => $this->getEmptyEngagementKPIs(),
            'conversion' => $this->getEmptyConversionKPIs(),
            'audience' => $this->getEmptyAudienceKPIs(),
            'acquisition' => $this->getEmptyAcquisitionKPIs(),
            'behavior' => $this->getEmptyBehaviorKPIs(),
            'technical' => $this->getEmptyTechnicalKPIs(),
            'trends' => $this->getEmptyTrendAnalysis(),
            'alerts' => ['alerts' => [], 'alert_count' => 0, 'recommendations' => []],
            'meta' => [
                'period_days' => $period,
                'generated_at' => now()->toISOString(),
                'error' => true,
                'message' => 'Analytics data temporarily unavailable',
            ]
        ];
    }

    protected function getEmptyOverviewKPIs(): array
    {
        $emptyKPI = [
            'current' => 0,
            'previous' => 0,
            'change_percent' => 0,
            'trend' => 'stable',
            'target' => ['target' => 0, 'unit' => ''],
            'status' => 'unknown'
        ];

        return [
            'total_users' => $emptyKPI,
            'sessions' => $emptyKPI,
            'pageviews' => $emptyKPI,
            'bounce_rate' => $emptyKPI,
            'avg_session_duration' => $emptyKPI,
            'conversions' => $emptyKPI,
        ];
    }

    protected function getEmptyTrafficKPIs(): array
    {
        return [
            'top_sources' => [],
            'channel_distribution' => [],
            'medium_breakdown' => [],
            'organic_traffic' => ['sessions' => 0, 'percentage' => 0],
            'direct_traffic' => ['sessions' => 0, 'percentage' => 0],
            'referral_traffic' => ['sessions' => 0, 'percentage' => 0],
            'social_traffic' => ['sessions' => 0, 'percentage' => 0],
            'traffic_trend' => [],
            'user_trend' => [],
        ];
    }

    protected function getEmptyEngagementKPIs(): array
    {
        return [
            'average_session_duration' => ['value' => 0, 'formatted' => '0m 0s', 'status' => 'unknown'],
            'bounce_rate' => ['value' => 0, 'status' => 'unknown'],
            'pages_per_session' => ['value' => 0, 'status' => 'unknown'],
            'engagement_rate' => ['value' => 0, 'status' => 'unknown'],
            'total_events' => 0,
            'top_events' => [],
            'most_engaging_pages' => [],
            'highest_bounce_pages' => [],
        ];
    }

    protected function getEmptyConversionKPIs(): array
    {
        return [
            'total_conversions' => 0,
            'conversion_rate' => ['sessions' => 0, 'users' => 0, 'status' => 'unknown'],
            'conversions_per_user' => 0,
            'top_converting_sources' => [],
            'top_converting_pages' => [],
            'conversion_funnel' => [],
        ];
    }

    protected function getEmptyAudienceKPIs(): array
    {
        return [
            'geographic_distribution' => [
                'top_countries' => [],
                'top_cities' => [],
                'total_countries' => 0,
                'total_cities' => 0,
            ],
            'device_breakdown' => [
                'devices' => [],
                'mobile_percentage' => 0,
                'desktop_percentage' => 0,
                'tablet_percentage' => 0,
            ],
            'technology_profile' => [
                'top_browsers' => [],
                'top_operating_systems' => [],
            ],
            'user_loyalty' => [
                'new_users_percentage' => 0,
                'returning_users_percentage' => 0,
                'user_types' => [],
            ],
        ];
    }

    protected function getEmptyAcquisitionKPIs(): array
    {
        return [
            'user_acquisition' => [
                'total_new_users' => 0,
                'cost_per_acquisition' => 0,
                'top_acquisition_sources' => [],
            ],
            'session_acquisition' => [
                'total_sessions' => 0,
                'average_sessions_per_user' => 0,
                'top_session_sources' => [],
            ],
            'marketing_channels' => [
                'organic_search' => 0,
                'paid_search' => 0,
                'social_media' => 0,
                'email' => 0,
                'referral' => 0,
                'direct' => 0,
            ],
            'campaign_performance' => [],
        ];
    }

    protected function getEmptyBehaviorKPIs(): array
    {
        return [
            'content_performance' => [
                'most_viewed_pages' => [],
                'highest_time_on_page' => [],
                'highest_exit_rate' => [],
                'total_pages_viewed' => 0,
            ],
            'landing_page_performance' => [
                'top_landing_pages' => [],
                'best_converting_landing_pages' => [],
            ],
            'site_search' => ['total_searches' => 0, 'top_search_terms' => []],
            'user_flow' => ['top_entry_points' => [], 'top_exit_points' => []],
        ];
    }

    protected function getEmptyTechnicalKPIs(): array
    {
        return [
            'site_speed' => [
                'average_page_load_time' => 0,
                'slowest_pages' => [],
                'fastest_pages' => [],
                'status' => 'unknown',
            ],
            'technical_issues' => [
                'total_exceptions' => 0,
                'error_rate' => 0,
            ],
            'mobile_performance' => [],
            'browser_compatibility' => ['browser_performance' => []],
        ];
    }

    protected function getEmptyTrendAnalysis(): array
    {
        return [
            'trends' => [],
            'forecasting' => ['forecast' => [], 'confidence' => 'low'],
            'seasonality' => ['weekly_pattern' => []],
            'anomalies' => ['anomalies' => []],
        ];
    }

    /**
     * Get real-time KPI summary
     */
    public function getRealTimeKPISummary(): array
    {
        $cacheKey = 'analytics.realtime.kpi.summary';
        $cacheTime = $this->cacheStrategy['realtime'];
        
        return Cache::remember($cacheKey, $cacheTime, function () {
            try {
                $today = Period::days(1);
                $yesterday = Period::create(Carbon::yesterday(), Carbon::today());
                
                $todayData = $this->getMetricsForPeriod($today, ['totalUsers', 'sessions', 'conversions']);
                $yesterdayData = $this->getMetricsForPeriod($yesterday, ['totalUsers', 'sessions', 'conversions']);
                
                return [
                    'status' => 'operational',
                    'today_vs_yesterday' => [
                        'users' => [
                            'today' => $todayData['totalUsers'],
                            'yesterday' => $yesterdayData['totalUsers'],
                            'change' => $this->calculatePercentageChange($todayData['totalUsers'], $yesterdayData['totalUsers']),
                        ],
                        'sessions' => [
                            'today' => $todayData['sessions'],
                            'yesterday' => $yesterdayData['sessions'],
                            'change' => $this->calculatePercentageChange($todayData['sessions'], $yesterdayData['sessions']),
                        ],
                        'conversions' => [
                            'today' => $todayData['conversions'],
                            'yesterday' => $yesterdayData['conversions'],
                            'change' => $this->calculatePercentageChange($todayData['conversions'], $yesterdayData['conversions']),
                        ],
                    ],
                    'real_time_alerts' => $this->getRealTimeAlerts($todayData, $yesterdayData),
                    'last_updated' => now()->toISOString(),
                ];
            } catch (\Exception $e) {
                Log::error('Real-time KPI summary error: ' . $e->getMessage());
                return [
                    'status' => 'error',
                    'today_vs_yesterday' => [],
                    'real_time_alerts' => [],
                    'error' => $e->getMessage(),
                ];
            }
        });
    }

    protected function getRealTimeAlerts(array $todayData, array $yesterdayData): array
    {
        $alerts = [];
        
        // Check for significant drops in real-time
        foreach (['totalUsers', 'sessions', 'conversions'] as $metric) {
            $change = $this->calculatePercentageChange($todayData[$metric], $yesterdayData[$metric]);
            
            if ($change < -30) {
                $alerts[] = [
                    'type' => 'critical',
                    'metric' => $metric,
                    'message' => ucfirst($metric) . " is down {$change}% compared to yesterday",
                    'action_required' => true,
                ];
            } elseif ($change < -15) {
                $alerts[] = [
                    'type' => 'warning',
                    'metric' => $metric,
                    'message' => ucfirst($metric) . " is down {$change}% compared to yesterday",
                    'action_required' => false,
                ];
            }
        }
        
        return $alerts;
    }

    /**
     * Clear all KPI caches
     */
    public function clearKPICache(): void
    {
        $patterns = [
            'analytics.kpi.*',
            'analytics.realtime.kpi.*',
        ];
        
        foreach ($patterns as $pattern) {
            Cache::flush(); // In production, use more specific cache clearing
        }
    }

    /**
     * Test analytics connection and return status
     */
    public function testAnalyticsConnection(): array
    {
        try {
            $testData = Analytics::fetchTotalVisitorsAndPageViews(Period::days(1));
            
            return [
                'status' => 'connected',
                'message' => 'Analytics API is working correctly',
                'test_data' => $testData,
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Analytics API connection failed: ' . $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }
}