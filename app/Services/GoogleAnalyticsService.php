<?php

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
     */
    public function getVisitorsAndPageviews(): Collection
    {
        return Analytics::fetchVisitorsAndPageViews(Period::days(7));
    }

    /**
     * Get most visited pages
     */
    public function getMostVisitedPages(int $limit = 10): Collection
    {
        return Analytics::fetchMostVisitedPages(Period::days(7), $limit);
    }

    /**
     * Get top referrers
     */
    public function getTopReferrers(int $limit = 10): Collection
    {
        return Analytics::fetchTopReferrers(Period::days(7), $limit);
    }

    /**
     * Get top browsers
     */
    public function getTopBrowsers(int $limit = 10): Collection
    {
        return Analytics::fetchTopBrowsers(Period::days(7), $limit);
    }

    /**
     * Get user types (new vs returning)
     */
    public function getUserTypes(): Collection
    {
        return Analytics::fetchUserTypes(Period::days(7));
    }

    /**
     * Get total visitors for a specific period
     */
    public function getTotalVisitors(Period $period): int
    {
        $data = Analytics::fetchTotalVisitorsAndPageViews($period);
        return $data['visitors'] ?? 0;
    }

    /**
     * Get total pageviews for a specific period
     */
    public function getTotalPageviews(Period $period): int
    {
        $data = Analytics::fetchTotalVisitorsAndPageViews($period);
        return $data['pageViews'] ?? 0;
    }

    /**
     * Get real-time visitors (if available)
     */
    public function getRealTimeVisitors(): int
    {
        try {
            // Note: Real-time data requires different setup
            return 0; // Placeholder
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get custom analytics query
     */
    public function getCustomData(string $metrics, string $dimensions = '', Period $period = null): Collection
    {
        $period = $period ?: Period::days(7);
        
        return Analytics::get(
            $period,
            $metrics,
            ['dimensions' => $dimensions]
        );
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
}