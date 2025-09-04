<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SurveyController extends Controller
{
    /**
     * Submit survey response
     */
    public function submit(Request $request): JsonResponse
    {
        try {
            // Security: Rate limiting check (additional to route middleware)
            $key = 'survey_submit_' . $request->ip();
            if (Cache::get($key, 0) >= 10) { // Max 10 attempts per hour
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan. Silakan coba lagi nanti.',
                    'retry_after' => 3600
                ], 429);
            }

            // Enhanced validation with security rules
            $validator = Validator::make($request->all(), [
                'satisfaction' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:5',
                    'regex:/^[1-5]$/' // Extra safety: only single digits 1-5
                ],
                'ease_of_use' => [
                    'nullable',
                    'string',
                    'in:very_easy,easy,neutral,difficult,very_difficult',
                    'max:20' // Prevent long strings
                ],
                'comments' => [
                    'nullable',
                    'string',
                    'max:1000',
                    'regex:/^[^<>]*$/', // Block HTML/XML tags
                    function($attribute, $value, $fail) {
                        // Block SQL injection patterns
                        $sqlPatterns = [
                            '/union\s+select/i',
                            '/insert\s+into/i',
                            '/delete\s+from/i',
                            '/update\s+set/i',
                            '/drop\s+table/i',
                            '/create\s+table/i',
                            '/alter\s+table/i',
                            '/exec\s*\(/i',
                            '/script\s*>/i',
                            '/javascript:/i'
                        ];
                        
                        foreach ($sqlPatterns as $pattern) {
                            if ($value && preg_match($pattern, $value)) {
                                $fail('Input contains prohibited content.');
                                break;
                            }
                        }
                    }
                ],
                'page_url' => [
                    'nullable',
                    'url',
                    'max:500',
                    'regex:/^https?:\/\/[^\s<>"{}|\\^`\[\]]*$/' // Stricter URL validation
                ],
                'user_agent' => [
                    'nullable',
                    'string',
                    'max:1000',
                    'regex:/^[^<>]*$/' // Block HTML tags
                ],
                'timestamp' => [
                    'nullable',
                    'date',
                    'before_or_equal:now',
                    'after:' . now()->subDays(1)->toDateString() // Not older than 1 day
                ],
            ], [
                'satisfaction.regex' => 'Rating harus berupa angka 1-5.',
                'comments.regex' => 'Komentar mengandung karakter yang tidak diizinkan.',
                'page_url.regex' => 'URL tidak valid.',
                'user_agent.regex' => 'User agent mengandung karakter yang tidak diizinkan.',
                'timestamp.before_or_equal' => 'Timestamp tidak valid.',
                'timestamp.after' => 'Timestamp terlalu lama.'
            ]);

            if ($validator->fails()) {
                // Increment rate limiting counter for failed attempts
                Cache::increment($key, 1);
                Cache::put($key, Cache::get($key, 0), 3600); // 1 hour expiry
                
                Log::warning('Survey validation failed', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'errors' => $validator->errors(),
                    'input' => $request->except(['comments']) // Don't log comments for privacy
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Data yang dikirim tidak valid.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for duplicate submissions (same IP + session within 24 hours)
            $existingSurvey = Survey::where('ip_address', $request->ip())
                ->where('session_id', session()->getId())
                ->where('submitted_at', '>', now()->subDay())
                ->first();

            if ($existingSurvey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah mengisi survei hari ini. Terima kasih!'
                ], 429);
            }

            // Sanitize and create survey record
            $survey = Survey::create([
                'satisfaction_rating' => (int) $request->satisfaction,
                'ease_of_use' => $request->ease_of_use,
                'comments' => $request->comments ? $this->sanitizeText($request->comments) : null,
                'page_url' => $request->page_url ? filter_var($request->page_url, FILTER_SANITIZE_URL) : null,
                'user_agent' => $request->user_agent ? $this->sanitizeText($request->user_agent) : null,
                'ip_address' => $request->ip(),
                'session_id' => session()->getId(),
                'user_id' => Auth::id(),
                'submitted_at' => $request->timestamp ? Carbon::parse($request->timestamp) : now(),
            ]);

            // Log successful submission for analytics
            Log::info('Survey submitted', [
                'survey_id' => $survey->id,
                'satisfaction_rating' => $survey->satisfaction_rating,
                'ease_of_use' => $survey->ease_of_use,
                'has_comments' => !empty($survey->comments),
                'user_id' => $survey->user_id,
                'ip_address' => $survey->ip_address,
            ]);

            // Reset rate limiting counter on successful submission
            Cache::forget($key);

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas feedback Anda!',
                'survey_id' => $survey->id
            ]);

        } catch (\Exception $e) {
            // Increment rate limiting counter for system errors
            Cache::increment($key, 1);
            Cache::put($key, Cache::get($key, 0), 3600);
            
            Log::error('Survey submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Sanitize text input to prevent XSS and injection attacks
     */
    private function sanitizeText(string $text): string
    {
        // Remove HTML/XML tags
        $text = strip_tags($text);
        
        // Remove special characters that could be used for injection
        $text = preg_replace('/[<>"\']/', '', $text);
        
        // Convert special characters to HTML entities
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove null bytes
        $text = str_replace("\0", '', $text);
        
        // Trim whitespace
        $text = trim($text);
        
        return $text;
    }

    /**
     * Get survey statistics (for admin)
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            // Check if user has admin access
            if (!Auth::check() || !Auth::user()->hasRole(['admin', 'super-admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Get date range from request or default to last 30 days
            $startDate = $request->get('start_date', now()->subDays(30)->startOfDay());
            $endDate = $request->get('end_date', now()->endOfDay());

            // Get basic statistics
            $totalResponses = Survey::inDateRange($startDate, $endDate)->count();
            
            // Satisfaction rating breakdown
            $satisfactionBreakdown = Survey::inDateRange($startDate, $endDate)
                ->selectRaw('satisfaction_rating, count(*) as count')
                ->groupBy('satisfaction_rating')
                ->orderBy('satisfaction_rating')
                ->get()
                ->pluck('count', 'satisfaction_rating')
                ->toArray();

            // Ease of use breakdown
            $easeOfUseBreakdown = Survey::inDateRange($startDate, $endDate)
                ->whereNotNull('ease_of_use')
                ->selectRaw('ease_of_use, count(*) as count')
                ->groupBy('ease_of_use')
                ->get()
                ->pluck('count', 'ease_of_use')
                ->toArray();

            // Average satisfaction rating
            $averageSatisfaction = Survey::inDateRange($startDate, $endDate)
                ->avg('satisfaction_rating');

            // Comments count
            $commentsCount = Survey::inDateRange($startDate, $endDate)
                ->whereNotNull('comments')
                ->where('comments', '!=', '')
                ->count();

            // Top pages with surveys
            $topPages = Survey::inDateRange($startDate, $endDate)
                ->selectRaw('page_url, count(*) as count')
                ->whereNotNull('page_url')
                ->groupBy('page_url')
                ->orderByDesc('count')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_responses' => $totalResponses,
                    'average_satisfaction' => round($averageSatisfaction, 2),
                    'satisfaction_breakdown' => $satisfactionBreakdown,
                    'ease_of_use_breakdown' => $easeOfUseBreakdown,
                    'comments_count' => $commentsCount,
                    'top_pages' => $topPages,
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Survey statistics failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik survei'
            ], 500);
        }
    }

    /**
     * Get recent survey responses (for admin)
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            // Check if user has admin access
            if (!Auth::check() || !Auth::user()->hasRole(['admin', 'super-admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $limit = min($request->get('limit', 50), 100); // Max 100 records
            
            $surveys = Survey::with('user:id,name,email')
                ->orderByDesc('submitted_at')
                ->limit($limit)
                ->get()
                ->map(function ($survey) {
                    return [
                        'id' => $survey->id,
                        'satisfaction_rating' => $survey->satisfaction_rating,
                        'satisfaction_text' => $survey->satisfaction_text,
                        'ease_of_use' => $survey->ease_of_use,
                        'ease_of_use_text' => $survey->ease_of_use_text,
                        'comments' => $survey->comments,
                        'page_url' => $survey->page_url,
                        'user' => $survey->user ? [
                            'name' => $survey->user->name,
                            'email' => $survey->user->email,
                        ] : null,
                        'submitted_at' => $survey->submitted_at->format('Y-m-d H:i:s'),
                        'submitted_at_human' => $survey->submitted_at->diffForHumans(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $surveys
            ]);

        } catch (\Exception $e) {
            Log::error('Recent surveys failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data survei terbaru'
            ], 500);
        }
    }
}