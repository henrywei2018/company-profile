<?php
// Create: app/Http/Controllers/Client/ChatController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display client chat dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get client's chat sessions
        $activeSessions = ChatSession::where('user_id', $user->id)
            ->whereIn('status', ['active', 'waiting'])
            ->with(['operator', 'latestMessage'])
            ->orderBy('last_activity_at', 'desc')
            ->get();

        $recentSessions = ChatSession::where('user_id', $user->id)
            ->where('status', 'closed')
            ->with(['operator', 'latestMessage'])
            ->orderBy('ended_at', 'desc')
            ->limit(10)
            ->get();

        $statistics = [
            'total_sessions' => ChatSession::where('user_id', $user->id)->count(),
            'active_sessions' => $activeSessions->count(),
            'total_messages' => $user->messages()->count(),
            'avg_response_time' => $this->getAverageResponseTime($user),
        ];

        return view('client.chat.index', compact(
            'activeSessions',
            'recentSessions', 
            'statistics'
        ));
    }

    /**
     * Display chat history
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        
        $sessions = ChatSession::where('user_id', $user->id)
            ->with(['operator', 'messages'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('summary', 'like', "%{$search}%")
                      ->orWhereHas('messages', function ($mq) use ($search) {
                          $mq->where('message', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('client.chat.history', compact('sessions'));
    }

    /**
     * Show specific chat session
     */
    public function show(ChatSession $chatSession)
    {
        // Ensure the chat session belongs to the authenticated user
        if ($chatSession->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this chat session.');
        }

        $chatSession->load(['operator', 'messages.sender']);

        // Mark messages as read by client
        $chatSession->messages()
            ->where('sender_type', '!=', 'visitor')
            ->where('is_read_by_client', false)
            ->update([
                'is_read_by_client' => true,
                'read_by_client_at' => now(),
            ]);

        return view('client.chat.show', compact('chatSession'));
    }

    /**
     * Calculate average response time for user's sessions
     */
    private function getAverageResponseTime(User $user): float
    {
        $sessions = ChatSession::where('user_id', $user->id)
            ->where('status', 'closed')
            ->whereNotNull('ended_at')
            ->take(50)
            ->get();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $totalMinutes = $sessions->sum(function ($session) {
            return $session->started_at->diffInMinutes($session->ended_at);
        });

        return round($totalMinutes / $sessions->count(), 2);
    }
}