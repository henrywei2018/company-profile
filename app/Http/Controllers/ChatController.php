<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\User;
use App\Services\ChatService;
use App\Models\ChatOperator;
use App\Models\ChatTemplate;
use App\Models\ChatMessage;
use App\Facades\Notifications;
use App\Events\ChatMessageSent;
use App\Events\ChatSessionStatusChanged;
use App\Events\ChatOperatorStatusChanged;
use App\Events\ChatUserTyping;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    // =======================
    // CLIENT METHODS
    // =======================

    /**
     * Get current user's active session
     */
    public function getCurrentSession(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $session = ChatSession::where('user_id', $user->id)
            ->whereIn('status', ['active', 'waiting'])
            ->with(['messages' => function($query) {
                $query->orderBy('created_at', 'asc')->limit(50);
            }, 'assignedOperator.user'])
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'No active session']);
        }

        return response()->json([
            'success' => true,
            'session_id' => $session->session_id,
            'status' => $session->status,
            'messages' => $this->formatMessages($session->messages),
            'operator' => $session->assignedOperator ? [
                'name' => $session->assignedOperator->user->name,
                'avatar' => $session->assignedOperator->user->avatar_url
            ] : null
        ]);
    }

    /**
     * Send message from client
     */
    public function sendMessage(ChatSession $chatSession, Request $request): JsonResponse
    {
        $this->authorize('participate', $chatSession);

        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            $message = ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'sender_type' => 'client',
                'sender_id' => auth()->id(),
                'message' => $request->message,
                'message_type' => 'text'
            ]);

            // Update session activity
            $chatSession->touch();

            // Send notification to assigned operator
            if ($chatSession->assigned_operator_id) {
                Notifications::send('chat.message_received', $chatSession, $chatSession->assignedOperator->user);
            } else {
                // Notify all available operators
                Notifications::send('chat.message_received', $chatSession);
            }

            // Broadcast to real-time listeners
            broadcast(new ChatMessageSent($message))->toOthers();

            return response()->json([
                'success' => true,
                'message' => $this->formatMessage($message)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send client message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    /**
     * Send typing indicator from client
     */
    public function clientTyping(ChatSession $chatSession, Request $request): JsonResponse
    {
        $this->authorize('participate', $chatSession);

        $isTyping = $request->boolean('is_typing', true);

        broadcast(new ChatUserTyping($chatSession, auth()->user(), $isTyping))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Rate completed session
     */
    public function rateSession(ChatSession $chatSession, Request $request): JsonResponse
    {
        $this->authorize('participate', $chatSession);

        if ($chatSession->status !== 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Can only rate closed sessions'
            ], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:500'
        ]);

        try {
            $chatSession->update([
                'rating' => $request->rating,
                'feedback' => $request->feedback
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback!'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save session rating: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save rating'
            ], 500);
        }
    }

    // =======================
    // ADMIN METHODS
    // =======================

    /**
     * Admin dashboard view
     */
    public function adminIndex()
    {
        $stats = $this->chatService->getRealTimeMetrics();
        
        return view('admin.chat.index', compact('stats'));
    }

    /**
     * Get sessions for admin dashboard
     */
    public function getAdminSessions(Request $request): JsonResponse
    {
        $filter = $request->query('filter', 'all');
        $perPage = $request->query('per_page', 50);

        $query = ChatSession::with(['user', 'assignedOperator.user'])
            ->withCount(['messages as unread_count' => function($q) {
                $q->where('sender_type', 'client')->where('is_read', false);
            }]);

        // Apply filters
        switch ($filter) {
            case 'waiting':
                $query->where('status', 'waiting');
                break;
            case 'active':
                $query->where('status', 'active');
                break;
            case 'my_chats':
                $query->where('assigned_operator_id', auth()->id())
                      ->where('status', 'active');
                break;
            case 'unassigned':
                $query->whereNull('assigned_operator_id')
                      ->whereIn('status', ['waiting', 'active']);
                break;
        }

        $sessions = $query->orderByRaw("
            CASE status 
                WHEN 'waiting' THEN 1 
                WHEN 'active' THEN 2 
                ELSE 3 
            END
        ")
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

        $formattedSessions = $sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'session_id' => $session->session_id,
                'visitor_name' => $session->getVisitorName(),
                'visitor_email' => $session->getVisitorEmail(),
                'status' => $session->status,
                'priority' => $session->priority,
                'unread_count' => $session->unread_count,
                'assigned_operator' => $session->assignedOperator ? [
                    'id' => $session->assignedOperator->user->id,
                    'name' => $session->assignedOperator->user->name,
                    'avatar' => $session->assignedOperator->user->avatar_url
                ] : null,
                'waiting_time_minutes' => $session->status === 'waiting' 
                    ? now()->diffInMinutes($session->created_at) 
                    : null,
                'waiting_time_human' => $session->created_at->diffForHumans(),
                'created_at' => $session->created_at,
                'updated_at' => $session->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'sessions' => $formattedSessions,
            'pagination' => [
                'current_page' => $sessions->currentPage(),
                'last_page' => $sessions->lastPage(),
                'total' => $sessions->total()
            ]
        ]);
    }

    /**
     * Get real-time statistics
     */
    public function getStatistics(): JsonResponse
    {
        $stats = $this->chatService->getRealTimeMetrics();
        
        // Additional stats for admin
        $todayStats = [
            'sessions_today' => ChatSession::whereDate('created_at', today())->count(),
            'avg_response_time' => $this->chatService->getAverageResponseTime(),
            'satisfaction_rate' => $this->chatService->getSatisfactionRate(),
            'busiest_hour' => $this->chatService->getBusiestHour()
        ];

        return response()->json([
            'success' => true,
            'stats' => array_merge($stats, $todayStats)
        ]);
    }

    /**
     * Reply to chat as admin/operator
     */
    public function adminReply(ChatSession $chatSession, Request $request): JsonResponse
    {
        $this->authorize('manage', $chatSession);

        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        try {
            // Auto-assign if not assigned and operator is available
            if (!$chatSession->assigned_operator_id) {
                $operator = ChatOperator::where('user_id', auth()->id())->first();
                if ($operator && $operator->is_available) {
                    $chatSession->update([
                        'assigned_operator_id' => auth()->id(),
                        'status' => 'active'
                    ]);
                }
            }

            $message = ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'sender_type' => 'operator',
                'sender_id' => auth()->id(),
                'message' => $request->message,
                'message_type' => $request->input('message_type', 'text')
            ]);

            // Update session activity
            $chatSession->touch();

            // Send notification to client
            Notifications::send('chat.message_received', $chatSession, $chatSession->user);

            // Broadcast to real-time listeners
            broadcast(new ChatMessageSent($message))->toOthers();

            return response()->json([
                'success' => true,
                'message' => $this->formatMessage($message)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send admin reply: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    /**
     * Assign session to current operator
     */
    public function assignToMe(ChatSession $chatSession): JsonResponse
    {
        $this->authorize('manage', $chatSession);

        try {
            $operator = ChatOperator::where('user_id', auth()->id())->first();
            
            if (!$operator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Operator profile not found'
                ], 404);
            }

            if (!$operator->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not available for new chats'
                ], 400);
            }

            // Check concurrent chat limit
            $activeSessions = ChatSession::where('assigned_operator_id', auth()->id())
                ->where('status', 'active')
                ->count();

            if ($activeSessions >= $operator->max_concurrent_chats) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached your maximum concurrent chat limit'
                ], 400);
            }

            $chatSession->update([
                'assigned_operator_id' => auth()->id(),
                'status' => 'active',
                'assigned_at' => now()
            ]);

            // Send system message
            ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'sender_type' => 'system',
                'message' => auth()->user()->name . ' has joined the chat.',
                'message_type' => 'system'
            ]);

            // Notify client
            Notifications::send('chat.operator_joined', $chatSession, $chatSession->user);

            // Broadcast status change
            broadcast(new ChatSessionStatusChanged($chatSession))->toOthers();

            return response()->json([
                'success' => true,
                'operator' => [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name,
                    'avatar' => auth()->user()->avatar_url
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to assign session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign session'
            ], 500);
        }
    }

    /**
     * Close chat session
     */
    public function closeSession(ChatSession $chatSession, Request $request): JsonResponse
    {
        $this->authorize('manage', $chatSession);

        try {
            $chatSession->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => auth()->id(),
                'close_reason' => $request->input('reason', 'resolved')
            ]);

            // Send system message
            ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'sender_type' => 'system',
                'message' => 'Chat session has been closed by ' . auth()->user()->name,
                'message_type' => 'system'
            ]);

            // Notify client
            Notifications::send('chat.session_closed', $chatSession, $chatSession->user);

            // Broadcast status change
            broadcast(new ChatSessionStatusChanged($chatSession))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Session closed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to close session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to close session'
            ], 500);
        }
    }

    /**
     * Transfer session to another operator
     */
    public function transferSession(ChatSession $chatSession, Request $request): JsonResponse
    {
        $this->authorize('manage', $chatSession);

        $request->validate([
            'operator_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            $newOperator = User::findOrFail($request->operator_id);
            $operatorProfile = ChatOperator::where('user_id', $newOperator->id)->first();

            if (!$operatorProfile || !$operatorProfile->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected operator is not available'
                ], 400);
            }

            $oldOperatorName = auth()->user()->name;
            
            $chatSession->update([
                'assigned_operator_id' => $newOperator->id,
                'transferred_at' => now(),
                'transfer_reason' => $request->reason
            ]);

            // Send system message
            ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'sender_type' => 'system',
                'message' => "Chat transferred from {$oldOperatorName} to {$newOperator->name}",
                'message_type' => 'system'
            ]);

            // Notify new operator
            Notifications::send('chat.session_transferred', $chatSession, $newOperator);

            // Notify client
            Notifications::send('chat.operator_changed', $chatSession, $chatSession->user);

            return response()->json([
                'success' => true,
                'message' => 'Session transferred successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to transfer session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to transfer session'
            ], 500);
        }
    }

    /**
     * Update session priority
     */
    public function updatePriority(ChatSession $chatSession, Request $request): JsonResponse
    {
        $this->authorize('manage', $chatSession);

        $request->validate([
            'priority' => 'required|in:low,normal,high,urgent'
        ]);

        try {
            $chatSession->update(['priority' => $request->priority]);

            return response()->json([
                'success' => true,
                'message' => 'Priority updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update priority: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update priority'
            ], 500);
        }
    }

    /**
     * Set operator status (online/offline/away)
     */
    public function setOperatorStatus(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:online,offline,away'
        ]);

        try {
            $operator = ChatOperator::firstOrCreate(
                ['user_id' => auth()->id()],
                [
                    'is_online' => false,
                    'is_available' => false,
                    'max_concurrent_chats' => 5
                ]
            );

            $statusMap = [
                'online' => ['is_online' => true, 'is_available' => true],
                'away' => ['is_online' => true, 'is_available' => false],
                'offline' => ['is_online' => false, 'is_available' => false]
            ];

            $operator->update($statusMap[$request->status]);

            // Broadcast status change
            broadcast(new ChatOperatorStatusChanged($operator))->toOthers();

            return response()->json([
                'success' => true,
                'status' => $request->status
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update operator status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    /**
     * Get current operator status
     */
    public function getOperatorStatus(): JsonResponse
    {
        $operator = ChatOperator::where('user_id', auth()->id())->first();
        
        if (!$operator) {
            return response()->json([
                'success' => true,
                'status' => 'offline'
            ]);
        }

        $status = 'offline';
        if ($operator->is_online) {
            $status = $operator->is_available ? 'online' : 'away';
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'active_sessions' => ChatSession::where('assigned_operator_id', auth()->id())
                ->where('status', 'active')
                ->count()
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markMessagesAsRead(ChatSession $chatSession): JsonResponse
    {
        try {
            ChatMessage::where('chat_session_id', $chatSession->id)
                ->where('sender_type', 'client')
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Failed to mark messages as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark messages as read'
            ], 500);
        }
    }

    /**
     * Get chat templates
     */
    public function getTemplates(): JsonResponse
    {
        $templates = ChatTemplate::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return response()->json([
            'success' => true,
            'templates' => $templates
        ]);
    }

    /**
     * Use predefined template
     */
    public function useTemplate(ChatSession $chatSession, Request $request): JsonResponse
    {
        $this->authorize('manage', $chatSession);

        $request->validate([
            'template_id' => 'required|exists:chat_templates,id'
        ]);

        try {
            $template = ChatTemplate::findOrFail($request->template_id);
            
            $message = ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'sender_type' => 'operator',
                'sender_id' => auth()->id(),
                'message' => $template->content,
                'message_type' => 'text',
                'template_id' => $template->id
            ]);

            // Update template usage count
            $template->increment('usage_count');

            // Notify client
            Notifications::send('chat.message_received', $chatSession, $chatSession->user);

            // Broadcast message
            broadcast(new ChatMessageSent($message))->toOthers();

            return response()->json([
                'success' => true,
                'message' => $this->formatMessage($message)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to use template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send template message'
            ], 500);
        }
    }

    // =======================
    // UTILITY METHODS
    // =======================

    /**
     * Format single message for API response
     */
    private function formatMessage(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'message' => $message->message,
            'sender_type' => $message->sender_type,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->getSenderName(),
            'message_type' => $message->message_type,
            'file_path' => $message->file_path,
            'file_type' => $message->file_type,
            'is_read' => $message->is_read,
            'created_at' => $message->created_at,
            'updated_at' => $message->updated_at
        ];
    }

    /**
     * Format multiple messages for API response
     */
    private function formatMessages($messages): array
    {
        return $messages->map(function ($message) {
            return $this->formatMessage($message);
        })->toArray();
    }
}