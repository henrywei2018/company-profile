<?php
// File: app/Http/Middleware/ChatOperatorMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ChatOperator;
use Symfony\Component\HttpFoundation\Response;

class ChatOperatorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user || !$user->hasRole(['admin', 'super-admin'])) {
            abort(403, 'Access denied. Admin role required.');
        }

        // Update operator's last seen timestamp
        ChatOperator::updateOrCreate(
            ['user_id' => $user->id],
            ['last_seen_at' => now()]
        );

        return $next($request);
    }
}