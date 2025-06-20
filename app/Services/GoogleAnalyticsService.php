<?php

// Fix for GoogleAnalyticsService.php - Updated for Spatie Analytics v5.6.0

namespace App\Services;

use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class GoogleAnalyticsService
{
    /**
     * Get dashboard analytics data
     */
    public function getDashboardData(): array
    {
        return Cache::remember('analytics.dashboard', 60, function () {
            try {
                return [
                    'visitors_and_pageviews' => $this->getVisitorsAndPageviews(),
                    'most_visited_pages' => $this->getMostVisitedPages(),
                    'top_referrers' => $this->getTopReferrers(),
                    'top_browsers' => $this->getTopBrowsers(),
                    'user_types' => $this->getUserTypes(),
                    'real_time_visitors' => $this->getRealTimeVisitors(),
                ];
            } catch (\Exception $e) {
                \Log::error('Analytics error: ' . $e->getMessage());
                return $this->getEmptyData();
            }
        });
    }

    /**
     * Get visitors and pageviews for the last 7 days
     * Fixed for Spatie Analytics v5.6.0
     */
    public function getVisitorsAndPageviews(): Collection
    {
        try {
            // Use the correct method for v5.6.0
            return Analytics::fetchVisitorsAndPageViews(Period::days(7));
        } catch (\Exception $e) {
            \Log::error('Error fetching visitors and pageviews: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get most visited pages
     * Fixed for Spatie Analytics v5.6.0
     */
    public function getMostVisitedPages(int $limit = 10): Collection
    {
        try {
            return Analytics::fetchMostVisitedPages(Period::days(7), $limit);
        } catch (\Exception $e) {
            \Log::error('Error fetching most visited pages: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get top referrers
     * Fixed for Spatie Analytics v5.6.0
     */
    public function getTopReferrers(int $limit = 10): Collection
    {
        try {
            return Analytics::fetchTopReferrers(Period::days(7), $limit);
        } catch (\Exception $e) {
            \Log::error('Error fetching top referrers: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get top browsers
     * Fixed for Spatie Analytics v5.6.0
     */
    public function getTopBrowsers(int $limit = 10): Collection
    {
        try {
            return Analytics::fetchTopBrowsers(Period::days(7), $limit);
        } catch (\Exception $e) {
            \Log::error('Error fetching top browsers: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get user types (new vs returning)
     * Fixed for Spatie Analytics v5.6.0
     */
    public function getUserTypes(): Collection
    {
        try {
            return Analytics::fetchUserTypes(Period::days(7));
        } catch (\Exception $e) {
            \Log::error('Error fetching user types: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get total visitors for a specific period
     * Fixed for Spatie Analytics v5.6.0
     */
    public function getTotalVisitors(Period $period): int
    {
        try {
            $data = Analytics::fetchTotalVisitorsAndPageViews($period);
            return $data['visitors'] ?? 0;
        } catch (\Exception $e) {
            \Log::error('Error fetching total visitors: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total pageviews for a specific period
     * Fixed for Spatie Analytics v5.6.0
     */
    public function getTotalPageviews(Period $period): int
    {
        try {
            $data = Analytics::fetchTotalVisitorsAndPageViews($period);
            return $data['pageViews'] ?? 0;
        } catch (\Exception $e) {
            \Log::error('Error fetching total pageviews: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get real-time visitors (if available)
     */
    public function getRealTimeVisitors(): int
    {
        try {
            // Real-time data requires different setup in GA4
            // For now, return 0 as placeholder
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get custom analytics query
     * Fixed for Spatie Analytics v5.6.0 - Updated method signature
     */
    public function getCustomData(array $metrics, array $dimensions = [], Period $period = null): Collection
    {
        $period = $period ?: Period::days(7);
        
        try {
            // Updated for v5.6.0 - metrics should be array, not string
            return Analytics::get(
                $period,
                $metrics, // Now expects array
                $dimensions // Now expects array
            );
        } catch (\Exception $e) {
            \Log::error('Error fetching custom analytics data: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get sessions data with proper error handling
     */
    public function getSessionsData(Period $period): Collection
    {
        try {
            // For GA4, sessions metric
            return $this->getCustomData(['sessions'], [], $period);
        } catch (\Exception $e) {
            \Log::error('Error fetching sessions data: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get bounce rate data with proper error handling
     */
    public function getBounceRateData(Period $period): Collection
    {
        try {
            // For GA4, bounceRate metric
            return $this->getCustomData(['bounceRate'], [], $period);
        } catch (\Exception $e) {
            \Log::error('Error fetching bounce rate: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get countries data
     */
    public function getCountriesData(Period $period): Collection
    {
        try {
            return $this->getCustomData(['sessions'], ['country'], $period);
        } catch (\Exception $e) {
            \Log::error('Error fetching countries data: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get device categories data
     */
    public function getDevicesData(Period $period): Collection
    {
        try {
            return $this->getCustomData(['sessions'], ['deviceCategory'], $period);
        } catch (\Exception $e) {
            \Log::error('Error fetching devices data: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get empty data structure for error cases
     */
    public function getEmptyData(): array
    {
        return [
            'visitors_and_pageviews' => collect(),
            'most_visited_pages' => collect(),
            'top_referrers' => collect(),
            'top_browsers' => collect(),
            'user_types' => collect(),
            'real_time_visitors' => 0,
        ];
    }

    /**
     * Test connection with simple metrics
     */
    public function testConnection(): bool
    {
        try {
            Analytics::fetchTotalVisitorsAndPageViews(Period::days(1));
            return true;
        } catch (\Exception $e) {
            \Log::error('Analytics connection test failed: ' . $e->getMessage());
            return false;
        }
    }
}