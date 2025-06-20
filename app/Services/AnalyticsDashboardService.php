<?php

namespace App\Services;

use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnalyticsDashboardService
{
    protected GoogleAnalyticsService $analyticsService;
    protected int $cacheMinutes = 60;

    public function __construct(GoogleAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get complete dashboard analytics data
     */
    public function getDashboardAnalytics(): array
    {
        return Cache::remember('dashboard.analytics.complete', $this->cacheMinutes, function () {
            try {
                $analyticsData = $this->analyticsService->getDashboardData();
                $stats = $this->calculateStatistics();

                return [
                    'analytics' => $analyticsData,
                    'stats' => $stats,
                    'summary' => $this->getSummaryData(),
                    'trends' => $this->getTrendData(),
                ];

            } catch (\Exception $e) {
                Log::error('Error fetching dashboard analytics: ' . $e->getMessage());
                return $this->getEmptyDashboardData();
            }
        });
    }

    /**
     * Get analytics data by type
     */
    public function getAnalyticsDataByType(string $type, int $period = 7): Collection
    {
        $cacheKey = "analytics.{$type}.{$period}";
        
        return Cache::remember($cacheKey, $this->cacheMinutes, function () use ($type, $period) {
            try {
                return match($type) {
                    'visitors' => $this->analyticsService->getVisitorsAndPageviews(),
                    'pages' => $this->analyticsService->getMostVisitedPages(),
                    'referrers' => $this->analyticsService->getTopReferrers(),
                    'browsers' => $this->analyticsService->getTopBrowsers(),
                    'countries' => $this->getTopCountries($period),
                    'devices' => $this->getDeviceStats($period),
                    'user_types' => $this->analyticsService->getUserTypes(),
                    default => collect()
                };
            } catch (\Exception $e) {
                Log::error("Error fetching {$type} analytics: " . $e->getMessage());
                return collect();
            }
        });
    }

    /**
     * Get chart data for visualization
     */
    public function getChartData(int $days = 7): array
    {
        $cacheKey = "analytics.chart.{$days}";
        
        return Cache::remember($cacheKey, $this->cacheMinutes, function () use ($days) {
            try {
                $data = $this->analyticsService->getVisitorsAndPageviews();
                
                return [
                    'labels' => $data->pluck('date')->toArray(),
                    'visitors' => $data->pluck('visitors')->toArray(),
                    'pageviews' => $data->pluck('pageViews')->toArray(),
                    'sessions' => $this->getSessionsData($days),
                ];

            } catch (\Exception $e) {
                Log::error('Error fetching chart data: ' . $e->getMessage());
                return $this->getEmptyChartData();
            }
        });
    }

    /**
     * Get realtime statistics
     */
    public function getRealtimeStats(): array
    {
        return Cache::remember('analytics.realtime', 5, function () {
            try {
                return [
                    'active_users' => $this->analyticsService->getRealTimeVisitors(),
                    'today_visitors' => $this->getTodayVisitors(),
                    'today_pageviews' => $this->getTodayPageviews(),
                    'bounce_rate' => $this->getBounceRate(),
                    'avg_session_duration' => $this->getAvgSessionDuration(),
                ];

            } catch (\Exception $e) {
                Log::error('Error fetching realtime stats: ' . $e->getMessage());
                return $this->getEmptyRealtimeStats();
            }
        });
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(int $period = 30): array
    {
        $cacheKey = "analytics.performance.{$period}";
        
        return Cache::remember($cacheKey, $this->cacheMinutes * 2, function () use ($period) {
            try {
                $periodObj = Period::days($period);
                $previousPeriod = Period::create(
                    Carbon::now()->subDays($period * 2),
                    Carbon::now()->subDays($period)
                );

                $currentData = $this->getMetricsForPeriod($periodObj);
                $previousData = $this->getMetricsForPeriod($previousPeriod);

                return [
                    'current' => $currentData,
                    'previous' => $previousData,
                    'growth' => $this->calculateGrowthRates($currentData, $previousData),
                    'trends' => $this->analyzeTrends($periodObj),
                ];

            } catch (\Exception $e) {
                Log::error('Error fetching performance metrics: ' . $e->getMessage());
                return $this->getEmptyPerformanceMetrics();
            }
        });
    }

    /**
     * Calculate statistics for dashboard
     */
    protected function calculateStatistics(): array
    {
        try {
            return [
                'visitors' => [
                    'today' => $this->analyticsService->getTotalVisitors(Period::days(1)),
                    'week' => $this->analyticsService->getTotalVisitors(Period::days(7)),
                    'month' => $this->analyticsService->getTotalVisitors(Period::days(30)),
                ],
                'pageviews' => [
                    'today' => $this->analyticsService->getTotalPageviews(Period::days(1)),
                    'week' => $this->analyticsService->getTotalPageviews(Period::days(7)),
                    'month' => $this->analyticsService->getTotalPageviews(Period::days(30)),
                ],
                'sessions' => [
                    'today' => $this->getTotalSessions(Period::days(1)),
                    'week' => $this->getTotalSessions(Period::days(7)),
                    'month' => $this->getTotalSessions(Period::days(30)),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating statistics: ' . $e->getMessage());
            return $this->getEmptyStatistics();
        }
    }

    /**
     * Get summary data for dashboard
     */
    protected function getSummaryData(): array
    {
        try {
            $weekData = $this->analyticsService->getVisitorsAndPageviews();
            
            return [
                'total_visitors' => $weekData->sum('visitors'),
                'total_pageviews' => $weekData->sum('pageViews'),
                'avg_daily_visitors' => round($weekData->avg('visitors')),
                'avg_daily_pageviews' => round($weekData->avg('pageViews')),
                'peak_day' => $weekData->sortByDesc('visitors')->first()['date'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting summary data: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get trend data for analysis
     */
    protected function getTrendData(): array
    {
        try {
            $currentWeek = $this->analyticsService->getVisitorsAndPageviews();
            $previousWeek = Analytics::fetchVisitorsAndPageViews(
                Period::create(Carbon::now()->subDays(14), Carbon::now()->subDays(7))
            );

            $currentTotal = $currentWeek->sum('visitors');
            $previousTotal = $previousWeek->sum('visitors');
            
            $growth = $previousTotal > 0 
                ? round((($currentTotal - $previousTotal) / $previousTotal) * 100, 2)
                : 0;

            return [
                'visitor_growth' => $growth,
                'trend_direction' => $growth > 0 ? 'up' : ($growth < 0 ? 'down' : 'stable'),
                'is_growing' => $growth > 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating trends: ' . $e->getMessage());
            return ['visitor_growth' => 0, 'trend_direction' => 'stable', 'is_growing' => false];
        }
    }

    /**
     * Get top countries data
     */
    protected function getTopCountries(int $period = 7): Collection
    {
        try {
            return $this->analyticsService->getCustomData(
                'ga:sessions',
                'ga:country',
                Period::days($period)
            );
        } catch (\Exception $e) {
            Log::error('Error fetching countries data: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get device statistics
     */
    protected function getDeviceStats(int $period = 7): Collection
    {
        try {
            return $this->analyticsService->getCustomData(
                'ga:sessions',
                'ga:deviceCategory',
                Period::days($period)
            );
        } catch (\Exception $e) {
            Log::error('Error fetching device stats: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get sessions data for chart
     */
    protected function getSessionsData(int $days): array
    {
        try {
            $data = $this->analyticsService->getCustomData(
                'ga:sessions',
                'ga:date',
                Period::days($days)
            );
            
            return $data->pluck('sessions')->toArray();
        } catch (\Exception $e) {
            Log::error('Error fetching sessions data: ' . $e->getMessage());
            return array_fill(0, $days, 0);
        }
    }

    /**
     * Get today's visitors
     */
    protected function getTodayVisitors(): int
    {
        try {
            return $this->analyticsService->getTotalVisitors(Period::days(1));
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get today's pageviews
     */
    protected function getTodayPageviews(): int
    {
        try {
            return $this->analyticsService->getTotalPageviews(Period::days(1));
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get bounce rate
     */
    protected function getBounceRate(): float
    {
        try {
            $data = $this->analyticsService->getCustomData(
                'ga:bounceRate',
                '',
                Period::days(1)
            );
            
            return round($data->first()['bounceRate'] ?? 0, 2);
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Get average session duration
     */
    protected function getAvgSessionDuration(): int
    {
        try {
            $data = $this->analyticsService->getCustomData(
                'ga:avgSessionDuration',
                '',
                Period::days(1)
            );
            
            return round($data->first()['avgSessionDuration'] ?? 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get total sessions for period
     */
    protected function getTotalSessions(Period $period): int
    {
        try {
            $data = $this->analyticsService->getCustomData('ga:sessions', '', $period);
            return $data->sum('sessions') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get metrics for a specific period
     */
    protected function getMetricsForPeriod(Period $period): array
    {
        try {
            return [
                'visitors' => $this->analyticsService->getTotalVisitors($period),
                'pageviews' => $this->analyticsService->getTotalPageviews($period),
                'sessions' => $this->getTotalSessions($period),
                'bounce_rate' => $this->getBounceRateForPeriod($period),
            ];
        } catch (\Exception $e) {
            return ['visitors' => 0, 'pageviews' => 0, 'sessions' => 0, 'bounce_rate' => 0];
        }
    }

    /**
     * Get bounce rate for specific period
     */
    protected function getBounceRateForPeriod(Period $period): float
    {
        try {
            $data = $this->analyticsService->getCustomData('ga:bounceRate', '', $period);
            return round($data->first()['bounceRate'] ?? 0, 2);
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Calculate growth rates between periods
     */
    protected function calculateGrowthRates(array $current, array $previous): array
    {
        $growth = [];
        
        foreach ($current as $key => $currentValue) {
            $previousValue = $previous[$key] ?? 0;
            
            if ($previousValue > 0) {
                $growth[$key] = round((($currentValue - $previousValue) / $previousValue) * 100, 2);
            } else {
                $growth[$key] = $currentValue > 0 ? 100 : 0;
            }
        }
        
        return $growth;
    }

    /**
     * Analyze trends for the period
     */
    protected function analyzeTrends(Period $period): array
    {
        try {
            $data = $this->analyticsService->getVisitorsAndPageviews();
            
            // Simple trend analysis - compare first half vs second half
            $halfPoint = floor($data->count() / 2);
            $firstHalf = $data->take($halfPoint);
            $secondHalf = $data->skip($halfPoint);
            
            $firstAvg = $firstHalf->avg('visitors') ?? 0;
            $secondAvg = $secondHalf->avg('visitors') ?? 0;
            
            return [
                'trend' => $secondAvg > $firstAvg ? 'increasing' : ($secondAvg < $firstAvg ? 'decreasing' : 'stable'),
                'strength' => abs($secondAvg - $firstAvg) / max($firstAvg, 1),
            ];
        } catch (\Exception $e) {
            return ['trend' => 'stable', 'strength' => 0];
        }
    }

    /**
     * Clear all analytics cache
     */
    public function clearCache(): void
    {
        $cacheKeys = [
            'dashboard.analytics.complete',
            'analytics.chart.*',
            'analytics.realtime',
            'analytics.performance.*',
            'analytics.visitors.*',
            'analytics.pages.*',
            'analytics.referrers.*',
            'analytics.browsers.*',
            'analytics.countries.*',
            'analytics.devices.*',
            'analytics.user_types.*',
        ];

        foreach ($cacheKeys as $pattern) {
            if (str_contains($pattern, '*')) {
                // For Laravel cache tags or manual cache key management
                Cache::flush(); // Simple approach - clear all cache
                break;
            } else {
                Cache::forget($pattern);
            }
        }
    }

    /**
     * Get empty dashboard data structure
     */
    protected function getEmptyDashboardData(): array
    {
        return [
            'analytics' => $this->analyticsService->getEmptyData(),
            'stats' => $this->getEmptyStatistics(),
            'summary' => [],
            'trends' => ['visitor_growth' => 0, 'trend_direction' => 'stable', 'is_growing' => false],
        ];
    }

    /**
     * Get empty statistics structure
     */
    protected function getEmptyStatistics(): array
    {
        return [
            'visitors' => ['today' => 0, 'week' => 0, 'month' => 0],
            'pageviews' => ['today' => 0, 'week' => 0, 'month' => 0],
            'sessions' => ['today' => 0, 'week' => 0, 'month' => 0],
        ];
    }

    /**
     * Get empty chart data structure
     */
    protected function getEmptyChartData(): array
    {
        return [
            'labels' => [],
            'visitors' => [],
            'pageviews' => [],
            'sessions' => [],
        ];
    }

    /**
     * Get empty realtime stats structure
     */
    protected function getEmptyRealtimeStats(): array
    {
        return [
            'active_users' => 0,
            'today_visitors' => 0,
            'today_pageviews' => 0,
            'bounce_rate' => 0.0,
            'avg_session_duration' => 0,
        ];
    }

    /**
     * Get empty performance metrics structure
     */
    protected function getEmptyPerformanceMetrics(): array
    {
        return [
            'current' => ['visitors' => 0, 'pageviews' => 0, 'sessions' => 0, 'bounce_rate' => 0],
            'previous' => ['visitors' => 0, 'pageviews' => 0, 'sessions' => 0, 'bounce_rate' => 0],
            'growth' => ['visitors' => 0, 'pageviews' => 0, 'sessions' => 0, 'bounce_rate' => 0],
            'trends' => ['trend' => 'stable', 'strength' => 0],
        ];
    }
}