<?php
// app/Http/Controllers/ChatController.php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\ChatOperator;
use App\Models\User;
use App\Services\ChatService;
use App\Events\ChatTypingIndicator;
use App\Events\ChatOperatorStatusChanged;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    // ===== CLIENT METHODS =====

    /**
     * Start a new chat session for authenticated user
     */
    public function start(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            // Check if user already has an active session
            $existingSession = ChatSession::where('user_id', $user->id)
                ->whereIn('status', ['active', 'waiting'])
                ->first();

            if ($existingSession) {
                $messages = $existingSession->messages()->orderBy('created_at')->get();
                
                return response()->json([
                    'success' => true,
                    'session_id' => $existingSession->session_id,
                    'status' => $existingSession->status,
                    'messages' => $this->formatMessages($messages),
                    'channel' => $existingSession->getChannelName(),
                ]);
            }

            // Create new session
            $session = ChatSession::create([
                'user_id' => $user->id,
                'status' => 'waiting',
                'priority' => 'normal',
                'source' => 'website',
            ]);

            // Send welcome message
            $welcomeMessage = ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'bot',
                'message' => 'Hello! How can we help you today? An operator will be with you shortly.',
                'message_type' => 'text',
            ]);

            // Auto-assign if operators are available
            $this->chatService->autoAssignSession($session);

            $messages = $session->messages()->orderBy('created_at')->get();

            return response()->json([
                'success' => true,
                'session_id' => $session->session_id,
                'status' => $session->status,
                'messages' => $this->formatMessages($messages),
                'channel' => $session->getChannelName(),
            ]);

        } catch (\Exception $e) {
            Log::error('Chat session start failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'error' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start chat session'
            ], 500);
        }
    }

    /**
     * Send message in chat session
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|exists:chat_sessions,session_id',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $session = ChatSession::where('session_id', $request->session_id)->first();
            $user = auth()->user();

            // Verify session ownership
            if ($session->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to chat session'
                ], 403);
            }

            if ($session->status === 'closed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session is closed'
                ], 400);
            }

            // Create message (will auto-broadcast via model event)
            $message = ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'visitor',
                'sender_id' => $user->id,
                'message' => $request->message,
                'message_type' => 'text',
            ]);

            // Set session to active if it was waiting
            if ($session->status === 'waiting') {
                $session->update(['status' => 'active']);
            }

            return response()->json([
                'success' => true,
                'message' => $message->toWebSocketArray(),
            ]);

        } catch (\Exception $e) {
            Log::error('Send chat message failed: ' . $e->getMessage(), [
                'session_id' => $request->session_id,
                'user_id' => auth()->id(),
                'error' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    /**
     * Get session info and messages
     */
    public function getSession(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            $session = ChatSession::where('user_id', $user->id)
                ->whereIn('status', ['active', 'waiting'])
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active chat session'
                ], 404);
            }

            $messages = $session->messages()->orderBy('created_at')->get();

            return response()->json([
                'success' => true,
                'session_id' => $session->session_id,
                'status' => $session->status,
                'messages' => $this->formatMessages($messages),
                'channel' => $session->getChannelName(),
                'operator' => $session->operator ? [
                    'id' => $session->operator->id,
                    'name' => $session->operator->name,
                ] : null,
            ]);

        } catch (\Exception $e) {
            Log::error('Get chat session failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'error' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get chat session'
            ], 500);
        }
    }

    /**
     * Send typing indicator
     */
    public function sendTyping(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|exists:chat_sessions,session_id',
            'is_typing' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $session = ChatSession::where('session_id', $request->session_id)->first();
            $user = auth()->user();

            if ($session->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Broadcast typing indicator (will not broadcast to self)
            broadcast(new ChatTypingIndicator(
                $session,
                $user,
                $request->is_typing
            ))->toOthers();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Send typing indicator failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Close chat session
     */
    public function close(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|exists:chat_sessions,session_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $session = ChatSession::where('session_id', $request->session_id)->first();
            $user = auth()->user();

            if ($session->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Close session (will auto-broadcast via model event)
            $session->update([
                'status' => 'closed',
                'ended_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat session closed'
            ]);

        } catch (\Exception $e) {
            Log::error('Close chat session failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get online status of operators
     */
    public function onlineStatus(): JsonResponse
    {
        try {
            $onlineOperators = ChatOperator::where('is_online', true)
                ->where('is_available', true)
                ->count();

            return response()->json([
                'is_online' => $onlineOperators > 0,
                'operators_count' => $onlineOperators,
            ]);

        } catch (\Exception $e) {
            Log::error('Get online status failed: ' . $e->getMessage());
            return response()->json([
                'is_online' => false,
                'operators_count' => 0,
            ]);
        }
    }

    // ===== ADMIN METHODS =====

    /**
     * Admin chat dashboard
     */
    public function index()
    {
        $this->authorize('admin.chat.view');

        $statistics = [
            'total_sessions' => ChatSession::count(),
            'active_sessions' => ChatSession::where('status', 'active')->count(),
            'waiting_sessions' => ChatSession::where('status', 'waiting')->count(),
            'closed_sessions_today' => ChatSession::whereDate('ended_at', today())->where('status', 'closed')->count(),
            'online_operators' => ChatOperator::where('is_online', true)->count(),
            'available_operators' => ChatOperator::where('is_online', true)->where('is_available', true)->count(),
        ];

        $activeSessions = ChatSession::with(['user', 'latestMessage'])
            ->where('status', 'active')
            ->orderBy('last_activity_at', 'desc')
            ->get();

        $waitingSessions = ChatSession::with(['user', 'latestMessage'])
            ->where('status', 'waiting')
            ->orderBy('priority')
            ->orderBy('created_at')
            ->get();

        return view('admin.chat.index', compact(
            'statistics',
            'activeSessions',
            'waitingSessions'
        ));
    }

    /**
     * Show specific chat session for admin
     */
    public function show(ChatSession $chatSession)
    {
        $this->authorize('admin.chat.view');

        $chatSession->load(['user', 'operator', 'messages.sender']);

        return view('admin.chat.show', compact('chatSession'));
    }

    /**
     * Admin send message
     */
    public function reply(Request $request, ChatSession $chatSession)
    {
        $this->authorize('admin.chat.reply');

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            // Auto-assign current admin if not assigned
            if (!$chatSession->assigned_operator_id) {
                $chatSession->assignOperator(auth()->user());
            }

            // Create message (will auto-broadcast via model event)
            $message = ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'sender_type' => 'operator',
                'sender_id' => auth()->id(),
                'message' => $request->message,
                'message_type' => 'text',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message->toWebSocketArray(),
                ]);
            }

            return redirect()->back()->with('success', 'Message sent successfully!');

        } catch (\Exception $e) {
            Log::error('Admin chat reply failed: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false], 500);
            }

            return redirect()->back()->with('error', 'Failed to send message.');
        }
    }

    /**
     * Set operator online status
     */
    public function setOperatorStatus(Request $request): JsonResponse
    {
        $this->authorize('admin.chat.operate');

        $request->validate([
            'is_online' => 'required|boolean',
            'is_available' => 'boolean',
        ]);

        try {
            $operator = ChatOperator::updateOrCreate(
                ['user_id' => auth()->id()],
                [
                    'is_online' => $request->is_online,
                    'is_available' => $request->is_available ?? true,
                    'last_seen_at' => now(),
                ]
            );

            // Broadcast status change
            broadcast(new ChatOperatorStatusChanged($operator, $request->is_online))->toOthers();

            return response()->json([
                'success' => true,
                'status' => $request->is_online ? 'online' : 'offline',
                'is_available' => $operator->is_available,
            ]);

        } catch (\Exception $e) {
            Log::error('Set operator status failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Assign chat session to current admin
     */
    public function assignToMe(ChatSession $chatSession): JsonResponse
    {
        $this->authorize('admin.chat.assign');

        try {
            $chatSession->assignOperator(auth()->user());

            // Add system message
            ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'sender_type' => 'system',
                'message' => auth()->user()->name . ' joined the chat',
                'message_type' => 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat assigned successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Assign chat session failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get chat statistics for admin dashboard
     */
    public function getStatistics(): JsonResponse
    {
        $this->authorize('admin.chat.view');

        try {
            $statistics = [
                'total_sessions' => ChatSession::count(),
                'active_sessions' => ChatSession::where('status', 'active')->count(),
                'waiting_sessions' => ChatSession::where('status', 'waiting')->count(),
                'closed_sessions_today' => ChatSession::whereDate('ended_at', today())->where('status', 'closed')->count(),
                'total_messages' => ChatMessage::count(),
                'messages_today' => ChatMessage::whereDate('created_at', today())->count(),
                'online_operators' => ChatOperator::where('is_online', true)->count(),
                'available_operators' => ChatOperator::where('is_online', true)->where('is_available', true)->count(),
                'avg_response_time' => $this->calculateAverageResponseTime(),
            ];

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);

        } catch (\Exception $e) {
            Log::error('Get chat statistics failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    // ===== HELPER METHODS =====

    /**
     * Format messages for API response
     */
    protected function formatMessages($messages): array
    {
        return $messages->map(function ($message) {
            return $message->toWebSocketArray();
        })->toArray();
    }

    /**
     * Calculate average response time
     */
    protected function calculateAverageResponseTime(): float
    {
        $sessions = ChatSession::where('status', 'closed')
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $totalDuration = 0;
        $count = 0;

        foreach ($sessions as $session) {
            if ($session->started_at && $session->ended_at) {
                $duration = $session->started_at->diffInMinutes($session->ended_at);
                $totalDuration += $duration;
                $count++;
            }
        }

        return $count > 0 ? round($totalDuration / $count, 1) : 0;
    }

    /**
     * Handle operator typing (admin)
     */
    public function operatorTyping(Request $request, ChatSession $chatSession): JsonResponse
    {
        $this->authorize('admin.chat.operate');

        $request->validate([
            'is_typing' => 'required|boolean',
        ]);

        try {
            broadcast(new ChatTypingIndicator(
                $chatSession,
                auth()->user(),
                $request->is_typing
            ))->toOthers();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Operator typing indicator failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Close session (admin)
     */
    public function closeSession(ChatSession $chatSession)
    {
        $this->authorize('admin.chat.close');

        try {
            $chatSession->update([
                'status' => 'closed',
                'ended_at' => now(),
                'summary' => 'Closed by admin: ' . auth()->user()->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat session closed successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Close chat session failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get operator status
     */
    public function getOperatorStatus(): JsonResponse
    {
        try {
            $operator = ChatOperator::where('user_id', auth()->id())->first();

            return response()->json([
                'success' => true,
                'is_online' => $operator ? $operator->is_online : false,
                'is_available' => $operator ? $operator->is_available : false,
                'last_seen_at' => $operator ? $operator->last_seen_at : null,
            ]);

        } catch (\Exception $e) {
            Log::error('Get operator status failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'is_online' => false,
            ]);
        }
    }
}