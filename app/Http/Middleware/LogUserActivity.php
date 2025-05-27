<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class LogUserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log activity for authenticated users
        if (auth()->check() && $this->shouldLogActivity($request)) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * Determine if we should log this activity.
     */
    protected function shouldLogActivity(Request $request): bool
    {
        // Don't log AJAX requests, API calls, or asset requests
        if ($request->ajax() || $request->is('api/*') || $request->is('*.js', '*.css', '*.png', '*.jpg', '*.svg')) {
            return false;
        }

        // Don't log certain routes
        $excludedRoutes = [
            'logout',
            'api.*',
            'horizon.*',
            'telescope.*',
        ];

        foreach ($excludedRoutes as $pattern) {
            if ($request->routeIs($pattern)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Log the user activity.
     */
    protected function logActivity(Request $request, Response $response): void
    {
        try {
            $activityService = app(\App\Services\UserActivityService::class);
            
            $activityService->logActivity(auth()->user(), 'page_visit', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'route' => $request->route() ? $request->route()->getName() : null,
                'status_code' => $response->getStatusCode(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log user activity: ' . $e->getMessage());
        }
    }
}