<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientSessionManagement
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && $request->is('client/*')) {
            $this->manageClientSession($request);
        }

        $response = $next($request);

        // Update session activity timestamp
        if (auth()->check() && $request->is('client/*')) {
            session(['client_last_activity' => now()]);
        }

        return $response;
    }

    /**
     * Manage client session.
     */
    protected function manageClientSession(Request $request): void
    {
        $user = auth()->user();
        
        // Check for concurrent sessions (if enabled)
        if (config('auth.prevent_concurrent_sessions', false)) {
            $this->checkConcurrentSessions($user, $request);
        }

        // Update user's online status
        $this->updateOnlineStatus($user);

        // Check session timeout
        $this->checkSessionTimeout($request);
    }

    /**
     * Check for concurrent sessions.
     */
    protected function checkConcurrentSessions($user, Request $request): void
    {
        $currentSessionId = session()->getId();
        $lastSessionId = $user->last_session_id ?? null;

        if ($lastSessionId && $lastSessionId !== $currentSessionId) {
            // Invalidate previous session
            \DB::table('sessions')
                ->where('id', $lastSessionId)
                ->delete();
        }

        // Update user's current session ID
        $user->update(['last_session_id' => $currentSessionId]);
    }

    /**
     * Update user's online status.
     */
    protected function updateOnlineStatus($user): void
    {
        cache()->put("user_online_{$user->id}", true, now()->addMinutes(5));
    }

    /**
     * Check session timeout.
     */
    protected function checkSessionTimeout(Request $request): void
    {
        $lastActivity = session('client_last_activity');
        $timeoutMinutes = config('session.client_timeout', 60);

        if ($lastActivity && now()->diffInMinutes($lastActivity) > $timeoutMinutes) {
            auth()->logout();
            session()->invalidate();
            
            if ($request->expectsJson()) {
                response()->json([
                    'error' => 'Session Expired',
                    'message' => 'Your session has expired. Please log in again.',
                    'redirect_url' => route('login')
                ], 401)->send();
                exit;
            }
            
            redirect()->route('login')
                ->with('info', 'Your session has expired. Please log in again.')
                ->send();
            exit;
        }
    }
}