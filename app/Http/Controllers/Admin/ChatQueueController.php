<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatOperator;
use App\Services\ChatService;
use App\Jobs\ProcessChatQueueJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatQueueController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->middleware(['auth', 'admin']);
        $this->chatService = $chatService;
    }

    /**
     * Display chat queue dashboard
     */
    public function index()
    {
        $waitingSessions = ChatSession::where('status', 'waiting')
            ->with('user')
            ->byPriority('desc')
            ->orderBy('created_at')
            ->get()
            ->map(function ($session, $index) {
                return [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'position' => $index + 1,
                    'visitor_name' => $session->getVisitorName(),
                    'visitor_email' => $session->getVisitorEmail(),
                    'priority' => $session->priority,
                    'waiting_time_minutes' => now()->diffInMinutes($session->created_at),
                    'waiting_time_human' => $session->created_at->diffForHumans(),
                    'created_at' => $session->created_at,
                ];
            });

        $metrics = $this->chatService->getRealTimeMetrics();
        
        $operators = ChatOperator::with('user')
            ->where('is_online', true)
            ->get()
            ->map(function ($operator) {
                $activeSessions = ChatSession::where('assigned_operator_id', $operator->user_id)
                    ->where('status', 'active')
                    ->count();

                return [
                    'user' => $operator->user,
                    'is_available' => $operator->is_available,
                    'active_sessions' => $activeSessions,
                    'max_concurrent_chats' => $operator->max_concurrent_chats ?? 5,
                    'last_seen_at' => $operator->last_seen_at,
                ];
            });

        return view('admin.chat.queue', compact('waitingSessions', 'metrics', 'operators'));
    }

    /**
     * Get real-time queue status via API
     */
    public function status(): JsonResponse
    {
        try {
            $metrics = $this->chatService->getRealTimeMetrics();
            
            $waitingSessions = ChatSession::where('status', 'waiting')
                ->count();

            $estimatedWait = $this->chatService->getEstimatedWaitTime();

            return response()->json([
                'success' => true,
                'data' => [
                    'metrics' => $metrics,
                    'waiting_sessions' => $waitingSessions,
                    'estimated_wait_minutes' => $estimatedWait,
                    'timestamp' => now()->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get queue status'
            ], 500);
        }
    }

    /**
     * Manually process queue
     */
    public function processQueue(): JsonResponse
    {
        try {
            // Dispatch immediate queue processing
            ProcessChatQueueJob::dispatchSync();

            $assigned = $this->chatService->autoAssignWaitingSessions();

            return response()->json([
                'success' => true,
                'message' => "Queue processed successfully. {$assigned} sessions assigned.",
                'sessions_assigned' => $assigned
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process queue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Boost session priority
     */
    public function boostPriority(Request $request, ChatSession $session): JsonResponse
    {
        $request->validate([
            'priority' => 'required|in:normal,high,urgent'
        ]);

        try {
            $oldPriority = $session->priority;
            $session->update(['priority' => $request->priority]);

            // Add system message about priority change
            $session->messages()->create([
                'sender_type' => 'system',
                'message' => "Priority changed from {$oldPriority} to {$request->priority} by " . auth()->user()->name,
                'message_type' => 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Priority updated successfully',
                'old_priority' => $oldPriority,
                'new_priority' => $request->priority
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update priority'
            ], 500);
        }
    }

    /**
     * Bulk assign sessions to operators
     */
    public function bulkAssign(Request $request): JsonResponse
    {
        $request->validate([
            'session_ids' => 'required|array',
            'session_ids.*' => 'exists:chat_sessions,id',
            'operator_id' => 'required|exists:users,id'
        ]);

        try {
            $operator = \App\Models\User::find($request->operator_id);
            $sessions = ChatSession::whereIn('id', $request->session_ids)
                ->where('status', 'waiting')
                ->get();

            $assigned = 0;
            foreach ($sessions as $session) {
                $session->update([
                    'assigned_operator_id' => $operator->id,
                    'status' => 'active'
                ]);

                // Add system message
                $session->messages()->create([
                    'sender_type' => 'system',
                    'message' => "Chat assigned to {$operator->name} by " . auth()->user()->name,
                    'message_type' => 'system',
                ]);

                $assigned++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$assigned} sessions assigned to {$operator->name}",
                'sessions_assigned' => $assigned
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk assign sessions'
            ], 500);
        }
    }
}