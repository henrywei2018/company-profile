<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class ClientCsrfProtection
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Enhanced CSRF protection for client area
        if ($request->is('client/*') && $request->isMethod('POST')) {
            $this->validateCsrfToken($request);
        }

        return $next($request);
    }

    /**
     * Validate CSRF token with additional checks.
     */
    protected function validateCsrfToken(Request $request): void
    {
        $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');
        
        if (!$token || !hash_equals(session()->token(), $token)) {
            \Log::warning('Invalid CSRF token in client request', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
            ]);
            
            if ($request->expectsJson()) {
                response()->json([
                    'error' => 'Invalid CSRF Token',
                    'message' => 'Your session has expired. Please refresh the page.'
                ], 419)->send();
                exit;
            }
            
            redirect()->back()
                ->with('error', 'Your session has expired. Please try again.')
                ->send();
            exit;
        }
    }
}