<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Headers Middleware
 * File: app/Http/Middleware/SecurityHeaders.php
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Add Content Security Policy for admin areas
        if ($request->is('admin/*') || $request->is('client/*')) {
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com; " .
                   "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
                   "font-src 'self' https://fonts.gstatic.com; " .
                   "img-src 'self' data: https:; " .
                   "connect-src 'self'";
            
            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}