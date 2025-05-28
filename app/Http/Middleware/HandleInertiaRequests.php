<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class HandleInertiaRequests
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This would be used if you're using Inertia.js
        // For now, just pass through
        return $next($request);
    }
}