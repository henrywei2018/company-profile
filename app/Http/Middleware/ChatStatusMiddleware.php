<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatStatusMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if chat system is enabled
        if (!settings('chat_enabled', true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Chat system is currently disabled',
                    'offline_message' => settings('chat_offline_message', 'Chat is temporarily unavailable')
                ], 503);
            }
            
            return response()->view('errors.chat-disabled', [
                'message' => settings('chat_offline_message', 'Chat is temporarily unavailable')
            ], 503);
        }

        // Check maintenance mode for chat
        if (settings('chat_maintenance_mode', false)) {
            // Allow admins to access during maintenance
            if (!auth()->check() || !auth()->user()->hasAdminAccess()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Chat system is under maintenance',
                        'maintenance_message' => settings('chat_maintenance_message', 'Chat is under maintenance')
                    ], 503);
                }
                
                return response()->view('errors.chat-maintenance', [
                    'message' => settings('chat_maintenance_message', 'Chat is under maintenance')
                ], 503);
            }
        }

        return $next($request);
    }
}