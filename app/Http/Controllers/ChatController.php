<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatOperator;
use App\Models\ChatMessage;
use App\Models\ChatTemplate;
use App\Facades\Notifications;
use App\Events\ChatMessageSent;
use App\Events\ChatSessionStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChatController extends Controller
{
    
    public function start(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Check for existing active session
            $existingSession = ChatSession::where('user_id', $user->id)
                ->whereIn('status', ['active', 'waiting', 'queued'])
                ->first();

            if ($existingSession) {
                return response()->json([
                    'success' => true,
                    'session_id' => $existingSession->session_id,
                    'status' => $existingSession->status,
                    'message' => 'Using existing session'
                ]);
            }

            // Create visitor info
            $visitorInfo = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            // Create new session
            $session = ChatSession::create([
                'session_id' => 'chat_' . uniqid() . '_' . time(),
                'user_id' => $user->id,
                'visitor_info' => $visitorInfo,
                'status' => 'waiting',
                'priority' => 'normal',
                'source' => 'website',
                'started_at' => now(),
                'last_activity_at' => now(),
            ]);

            // Send welcome message
            ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'system',
                'message' => config('chat.welcome_message', 'Hello! How can we help you today?'),
                'message_type' => 'system'
            ]);

            // Try to notify operators (with error handling)
            try {
                Notifications::send('chat.session_started', $session);
            } catch (\Exception $e) {
                Log::warning('Failed to send session start notification: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'session_id' => $session->session_id,
                'status' => $session->status,
                'message' => 'Chat session started successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to start chat session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to start chat session'
            ], 500);
        }
    }

    public function getSession(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            
            $session = ChatSession::where('user_id', $user->id)
                ->whereIn('status', ['active', 'waiting', 'queued'])
                ->with(['assignedOperator'])
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active session found'
                ]);
            }

            return response()->json([
                'success' => true,
                'session_id' => $session->session_id,
                'status' => $session->status,
                'operator' => $session->assignedOperator ? [
                    'name' => $session->assignedOperator->name,
                    'avatar' => $session->assignedOperator->avatar_url ?? null
                ] : null,
                'started_at' => $session->started_at,
                'queue_position' => $session->getQueuePosition()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get session'
            ], 500);
        }
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string|max:1000'
        ]);

        try {
            $session = ChatSession::where('session_id', $request->session_id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            if ($session->status === 'closed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Session is closed'
                ], 400);
            }

            $message = ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'visitor',
                'sender_id' => auth()->id(),
                'message' => $request->message,
                'message_type' => 'text'
            ]);

            // Update session activity
            $session->update(['last_activity_at' => now()]);

            // Auto-assign operator if not assigned
            if (!$session->assigned_operator_id) {
                $this->autoAssignOperator($session);
            }

            // Send notifications (with error handling)
            try {
                if ($session->assigned_operator_id) {
                    $operator = ChatOperator::with('user')->where('user_id', $session->assigned_operator_id)->first();
                    if ($operator && $operator->user) {
                        Notifications::send('chat.message_received', $session, $operator->user);
                    }
                } else {
                    Notifications::send('chat.message_received', $session);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send message notification: ' . $e->getMessage());
            }

            // Broadcast message (with error handling)
            try {
                broadcast(new ChatMessageSent($message))->toOthers();
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast message: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => $message->toApiArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

   
    public function getMessages(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'since' => 'nullable|integer'
        ]);

        try {
            $session = ChatSession::where('session_id', $request->session_id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            $query = ChatMessage::where('chat_session_id', $session->id)
                ->orderBy('created_at', 'asc');

            if ($request->since) {
                $query->where('created_at', '>', Carbon::createFromTimestamp($request->since));
            }

            $messages = $query->get();

            // Mark messages as read
            ChatMessage::where('chat_session_id', $session->id)
                ->where('sender_type', '!=', 'visitor')
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json([
                'success' => true,
                'messages' => $messages->map(fn($m) => $m->toApiArray())->toArray(),
                'session_status' => $session->status,
                'last_message_time' => $messages->last()?->created_at?->timestamp
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get messages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get messages'
            ], 500);
        }
    }


    public function sendTyping(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'is_typing' => 'required|boolean'
        ]);

        try {
            $session = ChatSession::where('session_id', $request->session_id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            // Broadcast typing indicator (with error handling)
            try {
                broadcast(new class($session->session_id, auth()->user()->name, 'visitor', $request->is_typing) implements \Illuminate\Contracts\Broadcasting\ShouldBroadcast {
                    use \Illuminate\Broadcasting\InteractsWithSockets, \Illuminate\Foundation\Events\Dispatchable, \Illuminate\Queue\SerializesModels;
                    
                    public function __construct(
                        public string $sessionId,
                        public string $userName,
                        public string $userType,
                        public bool $isTyping
                    ) {}
                    
                    public function broadcastOn(): array
                    {
                        return [new \Illuminate\Broadcasting\Channel('chat-session.' . $this->sessionId)];
                    }
                    
                    public function broadcastAs(): string
                    {
                        return 'user.typing';
                    }
                    
                    public function broadcastWith(): array
                    {
                        return [
                            'session_id' => $this->sessionId,
                            'user_name' => $this->userName,
                            'user_type' => $this->userType,
                            'is_typing' => $this->isTyping,
                        ];
                    }
                })->toOthers();
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast typing indicator: ' . $e->getMessage());
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Failed to send typing indicator: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send typing indicator'
            ], 500);
        }
    }

    public function close(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $session = ChatSession::where('session_id', $request->session_id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            $session->update([
                'status' => 'closed',
                'ended_at' => now()
            ]);

            // Broadcast session closed (with error handling)
            try {
                broadcast(new ChatSessionStatusChanged($session))->toOthers();
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast session close: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Chat session closed'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to close session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to close session'
            ], 500);
        }
    }

    public function onlineStatus(): JsonResponse
    {
        try {
            $onlineOperators = ChatOperator::available()->count();
            $queueLength = ChatSession::waiting()->count();

            return response()->json([
                'success' => true,
                'online' => $onlineOperators > 0,
                'operators_online' => $onlineOperators,
                'queue_length' => $queueLength,
                'estimated_wait_time' => $queueLength * 2 // 2 minutes per person in queue
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get online status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'online' => false,
                'operators_online' => 0,
                'queue_length' => 0,
                'estimated_wait_time' => 0
            ]);
        }
    }

    public function adminIndex()
    {
        $this->authorize('viewAny', ChatSession::class);
        
        $stats = [
            'waiting_sessions' => ChatSession::waiting()->count(),
            'active_sessions' => ChatSession::active()->count(),
            'online_operators' => ChatOperator::online()->count(),
            'total_sessions_today' => ChatSession::whereDate('started_at', today())->count(),
        ];
        
        return view('admin.chat.index', compact('stats'));
    }

    public function getAdminSessions(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ChatSession::class);

        $filter = $request->query('filter', 'all');
        $perPage = min($request->query('per_page', 50), 100); // Max 100 per page

        $query = ChatSession::with(['user', 'assignedOperator'])
            ->withCount(['messages as unread_count' => function($q) {
                $q->fromVisitor()->unread();
            }]);

        // Apply filters
        match($filter) {
            'waiting' => $query->waiting(),
            'active' => $query->active(),
            'my_chats' => $query->assignedTo(auth()->id())->active(),
            'unassigned' => $query->unassigned()->whereIn('status', ['waiting', 'active']),
            default => null
        };

        $sessions = $query
            ->byPriority() // Order by priority
            ->orderBy('started_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'sessions' => $sessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'visitor_name' => $session->getVisitorName(),
                    'visitor_email' => $session->getVisitorEmail(),
                    'status' => $session->status,
                    'priority' => $session->priority,
                    'unread_count' => $session->unread_count,
                    'assigned_operator' => $session->assignedOperator ? [
                        'id' => $session->assignedOperator->id,
                        'name' => $session->assignedOperator->name,
                        'avatar' => $session->assignedOperator->avatar_url
                    ] : null,
                    'waiting_time_minutes' => $session->getWaitingTimeInMinutes(),
                    'queue_position' => $session->getQueuePosition(),
                    'status_badge_class' => $session->getStatusBadgeClass(),
                    'priority_badge_class' => $session->getPriorityBadgeClass(),
                    'can_be_assigned' => $session->canBeAssigned(),
                    'can_be_closed' => $session->canBeClosed(),
                    'created_at' => $session->created_at,
                    'updated_at' => $session->updated_at
                ];
            }),
            'pagination' => [
                'current_page' => $sessions->currentPage(),
                'last_page' => $sessions->lastPage(),
                'total' => $sessions->total()
            ]
        ]);
    }

    public function getStatistics(): JsonResponse
    {
        $this->authorize('viewAny', ChatSession::class);

        return response()->json([
            'success' => true,
            'stats' => [
                'waiting_sessions' => ChatSession::waiting()->count(),
                'active_sessions' => ChatSession::active()->count(),
                'online_operators' => ChatOperator::online()->count(),
                'available_operators' => ChatOperator::available()->count(),
                'sessions_today' => ChatSession::whereDate('started_at', today())->count(),
                'avg_wait_time' => $this->getAverageWaitTime(),
                'queue_length' => ChatSession::waiting()->count(),
            ]
        ]);
    }

    public function adminReply(ChatSession $chatSession, Request $request): JsonResponse
    {
        $this->authorize('manage', $chatSession);

        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        try {
            // Auto-assign if not assigned
            if (!$chatSession->assigned_operator_id) {
                $operator = ChatOperator::where('user_id', auth()->id())->first();
                if ($operator && $operator->canAcceptNewChat()) {
                    $chatSession->update([
                        'assigned_operator_id' => auth()->id(),
                        'status' => 'active'
                    ]);
                    $operator->updateChatCount();
                }
            }

            $message = ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'sender_type' => 'operator',
                'sender_id' => auth()->id(),
                'message' => $request->message,
                'message_type' => 'text'
            ]);

            // Update session activity
            $chatSession->update(['last_activity_at' => now()]);

            // Send notifications (with error handling)
            try {
                if ($chatSession->user) {
                    Notifications::send('chat.message_received', $chatSession, $chatSession->user);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send admin reply notification: ' . $e->getMessage());
            }

            // Broadcast message (with error handling)
            try {
                broadcast(new ChatMessageSent($message))->toOthers();
            } catch (\Exception $e) {
                Log::warning('Failed to broadcast admin reply: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => $message->toApiArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send admin reply: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    public function assignToMe(ChatSession $chatSession): JsonResponse
    {
        $this->authorize('assign', ChatSession::class);

        try {
            $operator = ChatOperator::where('user_id', auth()->id())->first();
            
            if (!$operator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Operator profile not found'
                ], 404);
            }

            if (!$operator->canAcceptNewChat()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot accept new chats at this time'
                ], 400);
            }

            $chatSession->update([
                'assigned_operator_id' => auth()->id(),
                'status' => 'active'
            ]);

            $operator->updateChatCount();

            // Send system message
            ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'sender_type' => 'system',
                'message' => auth()->user()->name . ' has joined the chat.',
                'message_type' => 'system'
            ]);

            // Notify client (with error handling)
            try {
                if ($chatSession->user) {
                    Notifications::send('chat.operator_joined', $chatSession, $chatSession->user);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send operator join notification: ' . $e->getMessage());
            }

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
    public function closeSession(ChatSession $chatSession, Request $request): JsonResponse
    {
        $this->authorize('close', $chatSession);

        try {
            $chatSession->update([
                'status' => 'closed',
                'ended_at' => now(),
                'summary' => $request->input('summary')
            ]);

            // Update operator chat count
            if ($chatSession->assigned_operator_id) {
                $operator = ChatOperator::where('user_id', $chatSession->assigned_operator_id)->first();
                if ($operator) {
                    $operator->updateChatCount();
                }
            }

            // Send system message
            ChatMessage::create([
                'chat_session_id' => $chatSession->id,
                'sender_type' => 'system',
                'message' => 'Chat session has been closed by ' . auth()->user()->name,
                'message_type' => 'system'
            ]);

            // Notify client (with error handling)
            try {
                if ($chatSession->user) {
                    Notifications::send('chat.session_closed', $chatSession, $chatSession->user);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send session close notification: ' . $e->getMessage());
            }

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

    public function getChatMessages(ChatSession $chatSession, Request $request): JsonResponse
    {
        $this->authorize('view', $chatSession);

        try {
            $perPage = min($request->query('per_page', 50), 100);
            
            $messages = ChatMessage::where('chat_session_id', $chatSession->id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Mark messages as read by operator
            ChatMessage::where('chat_session_id', $chatSession->id)
                ->fromVisitor()
                ->unread()
                ->update(['is_read' => true, 'read_at' => now()]);

            return response()->json([
                'success' => true,
                'messages' => $messages->map(fn($m) => $m->toApiArray())->reverse()->values(),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'total' => $messages->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get chat messages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get messages'
            ], 500);
        }
    }

    public function setOperatorStatus(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:online,away,offline'
        ]);

        try {
            $operator = ChatOperator::firstOrCreate(
                ['user_id' => auth()->id()],
                ['max_concurrent_chats' => 3]
            );

            match($request->status) {
                'online' => $operator->setOnline(true)->setAvailable(true),
                'away' => $operator->setOnline(true)->setAvailable(false),
                'offline' => $operator->setOnline(false)->setAvailable(false),
            };

            return response()->json([
                'success' => true,
                'status' => $request->status,
                'operator' => $operator->toApiArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to set operator status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function getOperatorStatus(): JsonResponse
    {
        try {
            $operator = ChatOperator::where('user_id', auth()->id())->first();
            
            if (!$operator) {
                return response()->json([
                    'success' => true,
                    'status' => 'offline',
                    'operator' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => $operator->getStatusText(),
                'operator' => $operator->toApiArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get operator status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get status'
            ], 500);
        }
    }

    private function autoAssignOperator(ChatSession $session): void
    {
        try {
            $availableOperator = ChatOperator::available()
                ->withCapacity()
                ->orderBy('last_seen_at', 'desc')
                ->first();

            if ($availableOperator) {
                $session->update([
                    'assigned_operator_id' => $availableOperator->user_id,
                    'status' => 'active'
                ]);

                $availableOperator->updateChatCount();

                try {
                    Notifications::send('chat.session_assigned', $session, $availableOperator->user);
                } catch (\Exception $e) {
                    Log::warning('Failed to notify operator assignment: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to auto-assign operator: ' . $e->getMessage());
        }
    }

    private function getAverageWaitTime(): float
    {
        try {
            $completedSessions = ChatSession::where('status', 'closed')
                ->whereNotNull('started_at')
                ->whereNotNull('ended_at')
                ->whereDate('started_at', today())
                ->get();

            if ($completedSessions->isEmpty()) {
                return 0;
            }

            $totalWaitTime = $completedSessions->sum(function ($session) {
                return $session->started_at->diffInMinutes($session->ended_at);
            });

            return round($totalWaitTime / $completedSessions->count(), 1);
        } catch (\Exception $e) {
            Log::error('Failed to calculate average wait time: ' . $e->getMessage());
            return 0;
        }
    }
}