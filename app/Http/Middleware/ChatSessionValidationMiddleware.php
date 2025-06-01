<?php

namespace App\Http\Middleware;

use App\Models\ChatSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatSessionValidationMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionId = $request->route('chatSession') 
            ?? $request->input('session_id') 
            ?? $request->header('X-Chat-Session-ID');

        if (!$sessionId) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Chat session ID required'], 400);
            }
            abort(400, 'Chat session ID is required');
        }

        // Find the session
        $session = ChatSession::where('session_id', $sessionId)->first();

        if (!$session) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Chat session not found'], 404);
            }
            abort(404, 'Chat session not found');
        }

        // Check if user has access to this session
        if (!$this->hasSessionAccess($request, $session)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied'], 403);
            }
            abort(403, 'Access denied to this chat session');
        }

        // Add session to request for controllers
        $request->merge(['chat_session' => $session]);

        return $next($request);
    }

    /**
     * Check if user has access to the chat session
     */
    protected function hasSessionAccess(Request $request, ChatSession $session): bool
    {
        $user = $request->user();

        if (!$user) {
            return false;
        }

        // Admins can access any session
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Regular users can only access their own sessions
        return $session->user_id === $user->id;
    }
}