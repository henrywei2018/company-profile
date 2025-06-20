<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalyticsTestController extends Controller
{
    /**
     * Test Google Analytics connection and data retrieval
     */
    public function test()
    {
        $results = [];
        $overallStatus = 'success';

        // Test 1: Configuration Check
        $results['config'] = $this->testConfiguration();
        if ($results['config']['status'] === 'error') {
            $overallStatus = 'error';
        }

        // Test 2: Credentials File Check
        $results['credentials'] = $this->testCredentialsFile();
        if ($results['credentials']['status'] === 'error') {
            $overallStatus = 'error';
        }

        // Test 3: Basic Analytics Connection
        $results['connection'] = $this->testAnalyticsConnection();
        if ($results['connection']['status'] === 'error') {
            $overallStatus = 'error';
        }

        // Test 4: Data Retrieval Tests
        if ($overallStatus !== 'error') {
            $results['data_tests'] = $this->testDataRetrieval();
        }

        // Test 5: Service Classes Test
        $results['services'] = $this->testServices();

        return response()->json([
            'overall_status' => $overallStatus,
            'tests' => $results,
            'timestamp' => now(),
            'next_steps' => $this->getNextSteps($results)
        ], $overallStatus === 'error' ? 500 : 200);
    }

    private function testConfiguration(): array
    {
        try {
            $propertyId = config('analytics.property_id');
            $credentialsPath = config('analytics.service_account_credentials_json');

            if (empty($propertyId)) {
                return [
                    'status' => 'error',
                    'message' => 'Analytics property ID not configured',
                    'solution' => 'Set ANALYTICS_PROPERTY_ID in .env file'
                ];
            }

            if (empty($credentialsPath)) {
                return [
                    'status' => 'error',
                    'message' => 'Credentials path not configured',
                    'solution' => 'Check analytics.service_account_credentials_json config'
                ];
            }

            return [
                'status' => 'success',
                'message' => 'Configuration looks good',
                'details' => [
                    'property_id' => $propertyId,
                    'credentials_path' => $credentialsPath
                ]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Configuration test failed: ' . $e->getMessage(),
                'solution' => 'Check config/analytics.php file'
            ];
        }
    }

    private function testCredentialsFile(): array
    {
        try {
            $credentialsPath = config('analytics.service_account_credentials_json');

            if (!file_exists($credentialsPath)) {
                return [
                    'status' => 'error',
                    'message' => 'Credentials file not found',
                    'path' => $credentialsPath,
                    'solution' => 'Move service-account-credentials.json to storage/app/analytics/'
                ];
            }

            $credentials = json_decode(file_get_contents($credentialsPath), true);
            
            if (!$credentials || !isset($credentials['type']) || $credentials['type'] !== 'service_account') {
                return [
                    'status' => 'error',
                    'message' => 'Invalid credentials file format',
                    'solution' => 'Ensure the JSON file is a valid service account key'
                ];
            }

            return [
                'status' => 'success',
                'message' => 'Credentials file found and valid',
                'details' => [
                    'project_id' => $credentials['project_id'] ?? 'unknown',
                    'client_email' => $credentials['client_email'] ?? 'unknown'
                ]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Credentials file test failed: ' . $e->getMessage(),
                'solution' => 'Check file permissions and JSON format'
            ];
        }
    }

    private function testAnalyticsConnection(): array
    {
        try {
            // Simple connection test - get total visitors for yesterday
            $period = Period::days(1);
            $data = Analytics::fetchTotalVisitorsAndPageViews($period);

            return [
                'status' => 'success',
                'message' => 'Analytics connection successful',
                'details' => [
                    'visitors' => $data['visitors'] ?? 0,
                    'pageViews' => $data['pageViews'] ?? 0,
                    'period' => 'Last 1 day'
                ]
            ];

        } catch (\Google\Service\Exception $e) {
            $error = json_decode($e->getMessage(), true);
            return [
                'status' => 'error',
                'message' => 'Google API Error: ' . ($error['error']['message'] ?? $e->getMessage()),
                'error_code' => $e->getCode(),
                'solution' => $this->getGoogleApiErrorSolution($e->getCode())
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Connection failed: ' . $e->getMessage(),
                'solution' => 'Check credentials and property ID'
            ];
        }
    }

    private function testDataRetrieval(): array
    {
        $tests = [];

        // Test visitors and pageviews
        try {
            $data = Analytics::fetchVisitorsAndPageViews(Period::days(7));
            $tests['visitors_pageviews'] = [
                'status' => 'success',
                'count' => $data->count(),
                'sample' => $data->take(2)->toArray()
            ];
        } catch (\Exception $e) {
            $tests['visitors_pageviews'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        // Test most visited pages
        try {
            $data = Analytics::fetchMostVisitedPages(Period::days(7), 5);
            $tests['most_visited_pages'] = [
                'status' => 'success',
                'count' => $data->count(),
                'sample' => $data->take(2)->toArray()
            ];
        } catch (\Exception $e) {
            $tests['most_visited_pages'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        // Test top referrers
        try {
            $data = Analytics::fetchTopReferrers(Period::days(7), 5);
            $tests['top_referrers'] = [
                'status' => 'success',
                'count' => $data->count(),
                'sample' => $data->take(2)->toArray()
            ];
        } catch (\Exception $e) {
            $tests['top_referrers'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        return $tests;
    }

    private function testServices(): array
    {
        try {
            // Test your custom services
            $analyticsService = app(\App\Services\GoogleAnalyticsService::class);
            $dashboardService = app(\App\Services\AnalyticsDashboardService::class);

            $tests = [];

            // Test GoogleAnalyticsService
            try {
                $data = $analyticsService->getDashboardData();
                $tests['google_analytics_service'] = [
                    'status' => 'success',
                    'has_data' => !empty($data),
                    'keys' => array_keys($data)
                ];
            } catch (\Exception $e) {
                $tests['google_analytics_service'] = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }

            // Test AnalyticsDashboardService
            try {
                $data = $dashboardService->getDashboardAnalytics();
                $tests['analytics_dashboard_service'] = [
                    'status' => 'success',
                    'has_data' => !empty($data),
                    'keys' => array_keys($data)
                ];
            } catch (\Exception $e) {
                $tests['analytics_dashboard_service'] = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }

            return $tests;

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Service test failed: ' . $e->getMessage()
            ];
        }
    }

    private function getGoogleApiErrorSolution(int $errorCode): string
    {
        return match($errorCode) {
            403 => 'Permission denied. Ensure service account has Viewer access to GA4 property.',
            404 => 'Property not found. Check if property ID G-VYHSLQXJE5 is correct.',
            401 => 'Authentication failed. Check service account credentials.',
            400 => 'Bad request. Check property ID format and API parameters.',
            default => 'Check Google Cloud Console for API quotas and permissions.'
        };
    }

    private function getNextSteps(array $results): array
    {
        $steps = [];

        // Check if we have any errors
        $hasErrors = false;
        foreach ($results as $test) {
            if (isset($test['status']) && $test['status'] === 'error') {
                $hasErrors = true;
                break;
            }
            if (is_array($test)) {
                foreach ($test as $subTest) {
                    if (isset($subTest['status']) && $subTest['status'] === 'error') {
                        $hasErrors = true;
                        break 2;
                    }
                }
            }
        }

        if (!$hasErrors) {
            $steps[] = 'Your analytics setup is working perfectly!';
            $steps[] = 'Add Google Analytics tracking script to your frontend layouts';
            $steps[] = 'Test the dashboard analytics widgets';
            $steps[] = 'Set up monitoring for API quotas and errors';
        } else {
            $steps[] = 'Fix the errors shown above';
            $steps[] = 'Re-run this test after fixing';
            $steps[] = 'Check Google Cloud Console for any API issues';
        }

        return $steps;
    }
}