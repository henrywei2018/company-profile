<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ChatOperator;
use Symfony\Component\HttpFoundation\Response;

class UpdateOperatorActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Update operator activity after request
        if (auth()->check() && auth()->user()->hasRole(['admin', 'super-admin'])) {
            try {
                ChatOperator::updateOrCreate(
                    ['user_id' => auth()->id()],
                    [
                        'last_seen_at' => now(),
                        'is_online' => true
                    ]
                );
            } catch (\Exception $e) {
                // Don't fail request if operator update fails
                \Log::warning('Failed to update operator activity', [
                    'user_id' => auth()->id(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $response;
    }
}