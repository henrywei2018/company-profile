<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class ClientApiRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $retryAfter
            ], 429);
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $maxAttempts - \Illuminate\Support\Facades\RateLimiter::attempts($key)),
        ]);

        return $response;
    }

    /**
     * Resolve request signature for rate limiting.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if (Auth::check()) {
            return 'client_api:' . Auth::id();
        }

        return 'client_api:' . $request->ip();
    }
}