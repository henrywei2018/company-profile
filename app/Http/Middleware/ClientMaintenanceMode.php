<?php

class ClientMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if client area is in maintenance mode
        if (config('app.client_maintenance_mode', false)) {
            // Allow admins to bypass maintenance mode
            if (!auth()->check() || !auth()->user()->hasAnyRole(['super-admin', 'admin'])) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Service Unavailable',
                        'message' => 'The client area is currently under maintenance. Please try again later.'
                    ], 503);
                }
                
                return response()->view('errors.maintenance', [
                    'area' => 'client',
                    'message' => 'The client area is currently under maintenance. We apologize for any inconvenience.'
                ], 503);
            }
        }
        // Proceed with the request if not in maintenance mode or if user is an admin
        return $next($request);
    }
}