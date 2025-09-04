<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SurveySecurityMiddleware
{
    /**
     * Handle an incoming request for survey security
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Security headers
        $response = $next($request);
        
        // Add security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Validate request origin for CSRF-like protection
        if ($request->isMethod('POST')) {
            $origin = $request->header('Origin') ?? $request->header('Referer');
            $allowedOrigins = [
                config('app.url'),
                $request->getSchemeAndHttpHost()
            ];
            
            if ($origin && !$this->isOriginAllowed($origin, $allowedOrigins)) {
                Log::warning('Survey request from invalid origin', [
                    'origin' => $origin,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Request origin not allowed'
                ], 403);
            }
            
            // Additional suspicious request detection
            if ($this->isSuspiciousRequest($request)) {
                Log::warning('Suspicious survey request detected', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'headers' => $request->headers->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Request blocked for security reasons'
                ], 403);
            }
        }
        
        return $response;
    }
    
    /**
     * Check if origin is allowed
     */
    private function isOriginAllowed(string $origin, array $allowedOrigins): bool
    {
        $parsedOrigin = parse_url($origin, PHP_URL_HOST);
        
        foreach ($allowedOrigins as $allowedOrigin) {
            $parsedAllowed = parse_url($allowedOrigin, PHP_URL_HOST);
            if ($parsedOrigin === $parsedAllowed) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Detect suspicious request patterns
     */
    private function isSuspiciousRequest(Request $request): bool
    {
        $userAgent = $request->userAgent();
        $contentType = $request->header('Content-Type');
        
        // Suspicious user agents
        $suspiciousAgents = [
            'bot', 'crawler', 'spider', 'scraper', 'wget', 'curl',
            'python-requests', 'postman', 'insomnia'
        ];
        
        foreach ($suspiciousAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                return true;
            }
        }
        
        // Missing or invalid content type for POST requests
        if ($request->isMethod('POST') && 
            (!$contentType || !str_contains($contentType, 'application/json'))) {
            return true;
        }
        
        // Excessive header count (possible header injection attack)
        if (count($request->headers->all()) > 50) {
            return true;
        }
        
        // Check for common attack headers
        $attackHeaders = [
            'x-forwarded-host',
            'x-original-url',
            'x-rewrite-url'
        ];
        
        foreach ($attackHeaders as $header) {
            if ($request->hasHeader($header)) {
                return true;
            }
        }
        
        return false;
    }
}