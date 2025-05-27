<?php
// Update your existing app/Http/Controllers/ChatController.php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\User;
use App\Services\ChatService;
use App\Models\ChatOperator;
use App\Models\ChatTemplate;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Start a new chat session for authenticated client
     */
    public function start(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Check if user already has an active session
        $existingSession = ChatSession::where('user_id', $user->id)
            ->whereIn('status', ['active', 'waiting'])
            ->first();

        if ($existingSession) {
            return response()->json([
                'success' => true,
                'session_id' => $existingSession->session_id,
                'messages' => $this->formatMessages($existingSession->messages)
            ]);
        }

        try {
            // Create new session for authenticated user
            $session = $this->chatService->startSession(
                null, // No visitor info needed - using authenticated user
                $user
            );

            // Store session ID in browser session
            session(['chat_session_id' => $session->session_id]);

            return response()->json([
                'success' => true,
                'session_id' => $session->session_id,
                'messages' => $this->formatMessages($session->messages)
            ]);

        } catch (\Exception $e) {
            \Log::error('Chat session start failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to start chat session'
            ], 500);
        }
    }

    /**
     * Send a message in chat
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
            $session = $this->chatService->getSession($request->session_id);

            // Verify session belongs to authenticated user
            if (!$session || $session->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session not found or access denied'
                ], 404);
            }

            if ($session->status === 'closed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session is closed'
                ], 400);
            }

            $message = $this->chatService->sendMessage(
                $session,
                $request->message,
                'visitor',
                auth()->user()
            );

            // Get updated messages (last 20 messages)
            $messages = $this->chatService->getMessages($session, 20);

            return response()->json([
                'success' => true,
                'message_id' => $message->id,
                'messages' => $this->formatMessages($messages)
            ]);

        } catch (\Exception $e) {
            \Log::error('Chat message failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    /**
     * Get messages for a chat session
     */
    public function getMessages(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|exists:chat_sessions,session_id',
            'last_message_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $session = $this->chatService->getSession($request->session_id);

            // Verify session belongs to authenticated user
            if (!$session || $session->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session not found or access denied'
                ], 404);
            }

            $query = $session->messages()->orderBy('created_at');

            // Get only new messages if last_message_id is provided
            if ($request->last_message_id) {
                $query->where('id', '>', $request->last_message_id);
            }

            $messages = $query->get();

            return response()->json([
                'success' => true,
                'messages' => $this->formatMessages($messages),
                'session_status' => $session->status
            ]);

        } catch (\Exception $e) {
            \Log::error('Get chat messages failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get messages'
            ], 500);
        }
    }

    /**
     * Update client information
     */
    public function updateClientInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|exists:chat_sessions,session_id',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $session = $this->chatService->getSession($request->session_id);

            // Verify session belongs to authenticated user
            if (!$session || $session->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session not found or access denied'
                ], 404);
            }

            // Update user information
            $user = auth()->user();
            $user->update([
                'phone' => $request->phone ?: $user->phone,
                'company' => $request->company ?: $user->company,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Information updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Update client info failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update information'
            ], 500);
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
            $session = $this->chatService->getSession($request->session_id);

            // Verify session belongs to authenticated user
            if (!$session || $session->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session not found or access denied'
                ], 404);
            }

            $this->chatService->closeSession($session, 'Closed by client');

            // Remove from browser session
            session()->forget('chat_session_id');

            return response()->json([
                'success' => true,
                'message' => 'Chat session closed'
            ]);

        } catch (\Exception $e) {
            \Log::error('Close chat session failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to close chat session'
            ], 500);
        }
    }

    /**
     * Get existing session from browser session
     */
    public function getSession(Request $request): JsonResponse
    {
        $user = auth()->user();

        // First check for active session in database
        $session = ChatSession::where('user_id', $user->id)
            ->whereIn('status', ['active', 'waiting'])
            ->first();

        if (!$session) {
            // Check browser session as fallback
            $sessionId = session('chat_session_id');
            if ($sessionId) {
                $session = $this->chatService->getSession($sessionId);
                if (!$session || $session->user_id !== $user->id || $session->status === 'closed') {
                    session()->forget('chat_session_id');
                    return response()->json([
                        'success' => false,
                        'message' => 'No active chat session'
                    ], 404);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No active chat session'
                ], 404);
            }
        }

        try {
            $messages = $this->chatService->getMessages($session);

            return response()->json([
                'success' => true,
                'session_id' => $session->session_id,
                'status' => $session->status,
                'messages' => $this->formatMessages($messages),
                'client_info' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'company' => $user->company,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Get chat session failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get chat session'
            ], 500);
        }
    }

    /**
     * Get chat history for authenticated user
     */
    public function history(Request $request): JsonResponse
    {
        $user = auth()->user();

        $sessions = ChatSession::where('user_id', $user->id)
            ->with([
                'messages' => function ($query) {
                    $query->orderBy('created_at');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'sessions' => $sessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'status' => $session->status,
                    'started_at' => $session->started_at->toISOString(),
                    'ended_at' => $session->ended_at?->toISOString(),
                    'messages' => $this->formatMessages($session->messages),
                    'summary' => $session->summary,
                ];
            })
        ]);
    }
    public function index()
    {
        // Check if user is admin
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $statistics = $this->chatService->getStatistics();

        $activeSessions = ChatSession::with(['user', 'latestMessage'])
            ->where('status', 'active')
            ->orderBy('last_activity_at', 'desc')
            ->get();

        $waitingSessions = ChatSession::with(['user', 'latestMessage'])
            ->where('status', 'waiting')
            ->orderBy('priority')
            ->orderBy('created_at')
            ->get();

        $recentClosedSessions = ChatSession::with(['user', 'operator'])
            ->where('status', 'closed')
            ->orderBy('ended_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.chat.index', compact(
            'statistics',
            'activeSessions',
            'waitingSessions',
            'recentClosedSessions'
        ));
    }

    /**
     * Show chat settings page
     */
    public function settings()
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        return view('admin.chat.settings');
    }

    /**
     * Update chat settings
     */
    public function updateSettings(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $validated = $request->validate([
            'chat_enabled' => 'boolean',
            'chat_position' => 'in:bottom-right,bottom-left,top-right,top-left',
            'chat_theme' => 'in:primary,dark,light',
            'chat_greeting' => 'string|max:500',
            'offline_message' => 'string|max:1000',
            'auto_response_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'notification_email' => 'email|nullable',
        ]);

        // Save settings - you can implement your preferred settings storage method here
        // Example: foreach ($validated as $key => $value) { settings([$key => $value], true); }

        return redirect()->route('admin.chat.settings')
            ->with('success', 'Chat settings updated successfully!');
    }

    /**
     * Show individual chat session for admin
     */
    public function show(ChatSession $chatSession)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $chatSession->load(['user', 'operator', 'messages.sender']);

        return view('admin.chat.show', compact('chatSession'));
    }

    /**
     * Reply to chat session (admin)
     */
    public function reply(Request $request, ChatSession $chatSession)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Create operator message
        $chatSession->messages()->create([
            'sender_type' => 'operator',
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'message_type' => 'text',
        ]);

        // Update session activity
        $chatSession->update([
            'last_activity_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Message sent successfully!');
    }

    /**
     * Close chat session (admin)
     */
    public function closeSession(ChatSession $chatSession)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $this->chatService->closeSession($chatSession, 'Closed by admin: ' . auth()->user()->name);

        return redirect()->route('admin.chat.index')
            ->with('success', 'Chat session closed successfully!');
    }

    /**
     * Assign chat to current admin user
     */
    public function assignToMe(ChatSession $chatSession)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $chatSession->update([
            'assigned_operator_id' => auth()->id(),
            'status' => 'active',
        ]);

        // Add system message
        $chatSession->messages()->create([
            'sender_type' => 'system',
            'message' => 'Chat assigned to ' . auth()->user()->name,
            'message_type' => 'system',
        ]);

        return redirect()->back()->with('success', 'Chat assigned to you successfully!');
    }

    /**
     * Update chat priority
     */
    public function updatePriority(Request $request, ChatSession $chatSession)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $request->validate([
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        $chatSession->update(['priority' => $request->priority]);

        return redirect()->back()->with('success', 'Priority updated successfully!');
    }

    /**
     * Update session notes
     */
    public function updateNotes(Request $request, ChatSession $chatSession)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $request->validate([
            'summary' => 'nullable|string|max:1000',
        ]);

        $chatSession->update(['summary' => $request->summary]);

        return redirect()->back()->with('success', 'Notes updated successfully!');
    }

    /**
     * Handle typing indicator
     */
    public function typing(Request $request, ChatSession $chatSession)
    {
        $request->validate([
            'typing' => 'required|boolean',
        ]);

        // In a real implementation, you'd broadcast this via WebSocket
        return response()->json(['success' => true]);
    }

    /**
     * Get chat statistics for admin
     */
    public function statistics(): JsonResponse
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        return response()->json($this->chatService->getStatistics());
    }

    /**
     * Get messages for admin (different from client getMessages)
     */
    public function getChatMessages(ChatSession $chatSession): JsonResponse
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $messages = $chatSession->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'new_messages' => false,
        ]);
    }

    /**
     * Go online as operator
     */
    public function goOnline(): JsonResponse
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $operator = ChatOperator::firstOrCreate(
            ['user_id' => auth()->id()],
            ['is_online' => true, 'is_available' => true]
        );

        $operator->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        return response()->json(['success' => true, 'status' => 'online']);
    }

    /**
     * Go offline as operator
     */
    public function goOffline(): JsonResponse
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $operator = ChatOperator::where('user_id', auth()->id())->first();

        if ($operator) {
            $operator->update([
                'is_online' => false,
                'last_seen_at' => now(),
            ]);
        }

        return response()->json(['success' => true, 'status' => 'offline']);
    }

    /**
     * Show chat templates
     */
    public function templates()
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $templates = ChatTemplate::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('admin.chat.templates', compact('templates'));
    }

    /**
     * Store chat template
     */
    public function storeTemplate(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:greeting,auto_response,quick_reply,offline',
            'trigger' => 'nullable|string|max:100',
        ]);

        ChatTemplate::create($request->all());

        return redirect()->back()->with('success', 'Template created successfully!');
    }

    public function reports(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        // Get all operators for filter dropdown
        $operators = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['super-admin', 'admin', 'manager', 'editor']);
        })->get();

        $reportData = null;
        $sessions = null;

        // Generate report if filters are applied
        if ($request->hasAny(['date_range', 'status', 'priority', 'operator_id', 'report_type'])) {
            $reportData = $this->generateReportData($request);

            if ($request->get('report_type') === 'detailed') {
                $sessions = $this->getDetailedSessions($request);
            }
        }

        return view('admin.chat.reports.index', compact('operators', 'reportData', 'sessions'));
    }

    /**
     * Generate report data based on filters
     */
    private function generateReportData(Request $request): array
    {
        $query = ChatSession::query();

        // Apply date filters
        $dateRange = $request->get('date_range', 'today');
        $this->applyDateFilter($query, $dateRange, $request);

        // Apply other filters
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        if ($request->filled('operator_id')) {
            $query->where('assigned_operator_id', $request->get('operator_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhere('visitor_info->name', 'like', "%{$search}%")
                    ->orWhere('visitor_info->email', 'like', "%{$search}%")
                    ->orWhereHas('messages', function ($mq) use ($search) {
                        $mq->where('message', 'like', "%{$search}%");
                    });
            });
        }

        $sessions = $query->with(['user', 'operator', 'messages'])->get();

        // Calculate statistics
        $totalSessions = $sessions->count();
        $totalMessages = $sessions->sum(function ($session) {
            return $session->messages->count();
        });

        $completedSessions = $sessions->where('status', 'closed');
        $avgResponseTime = $completedSessions->isEmpty() ? 0 : $completedSessions->avg(function ($session) {
            return $session->getDuration() ?? 0;
        });

        // Calculate satisfaction rate (placeholder - you can implement actual satisfaction tracking)
        $satisfactionRate = 85.5; // Mock data

        // Generate chart data
        $chartData = $this->generateChartData($sessions, $dateRange);

        return [
            'total_sessions' => $totalSessions,
            'total_messages' => $totalMessages,
            'avg_response_time' => round($avgResponseTime, 1),
            'satisfaction_rate' => $satisfactionRate,
            'chart_labels' => $chartData['labels'],
            'chart_sessions' => $chartData['sessions'],
            'chart_response_times' => $chartData['response_times'],
            'status_breakdown' => [
                'active' => $sessions->where('status', 'active')->count(),
                'waiting' => $sessions->where('status', 'waiting')->count(),
                'closed' => $sessions->where('status', 'closed')->count(),
            ],
            'priority_breakdown' => [
                'low' => $sessions->where('priority', 'low')->count(),
                'normal' => $sessions->where('priority', 'normal')->count(),
                'high' => $sessions->where('priority', 'high')->count(),
                'urgent' => $sessions->where('priority', 'urgent')->count(),
            ]
        ];
    }

    /**
     * Get detailed sessions for table view
     */
    private function getDetailedSessions(Request $request)
    {
        $query = ChatSession::with(['user', 'operator', 'messages']);

        // Apply same filters as report data
        $dateRange = $request->get('date_range', 'today');
        $this->applyDateFilter($query, $dateRange, $request);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        if ($request->filled('operator_id')) {
            $query->where('assigned_operator_id', $request->get('operator_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhere('visitor_info->name', 'like', "%{$search}%")
                    ->orWhere('visitor_info->email', 'like', "%{$search}%")
                    ->orWhereHas('messages', function ($mq) use ($search) {
                        $mq->where('message', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * Apply date filter to query
     */
    private function applyDateFilter($query, string $dateRange, Request $request): void
    {
        $now = now();

        switch ($dateRange) {
            case 'today':
                $query->whereDate('created_at', $now->toDateString());
                break;
            case 'yesterday':
                $query->whereDate('created_at', $now->subDay()->toDateString());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [
                    $now->startOfWeek()->toDateString(),
                    $now->endOfWeek()->toDateString()
                ]);
                break;
            case 'last_week':
                $startOfLastWeek = $now->subWeek()->startOfWeek();
                $endOfLastWeek = $now->subWeek()->endOfWeek();
                $query->whereBetween('created_at', [
                    $startOfLastWeek->toDateString(),
                    $endOfLastWeek->toDateString()
                ]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year);
                break;
            case 'last_month':
                $lastMonth = $now->subMonth();
                $query->whereMonth('created_at', $lastMonth->month)
                    ->whereYear('created_at', $lastMonth->year);
                break;
            case 'last_30_days':
                $query->where('created_at', '>=', $now->subDays(30));
                break;
            case 'last_90_days':
                $query->where('created_at', '>=', $now->subDays(90));
                break;
            case 'custom':
                if ($request->filled('date_from')) {
                    $query->whereDate('created_at', '>=', $request->get('date_from'));
                }
                if ($request->filled('date_to')) {
                    $query->whereDate('created_at', '<=', $request->get('date_to'));
                }
                break;
        }
    }

    /**
     * Generate chart data for reports
     */
    private function generateChartData($sessions, string $dateRange): array
    {
        $labels = [];
        $sessionCounts = [];
        $responseTimes = [];

        // Group sessions by date/period based on date range
        if (in_array($dateRange, ['today', 'yesterday'])) {
            // Group by hour
            $groupedSessions = $sessions->groupBy(function ($session) {
                return $session->created_at->format('H:00');
            });

            for ($hour = 0; $hour < 24; $hour++) {
                $hourLabel = sprintf('%02d:00', $hour);
                $labels[] = $hourLabel;
                $hourSessions = $groupedSessions->get($hourLabel, collect());
                $sessionCounts[] = $hourSessions->count();
                $responseTimes[] = $hourSessions->where('status', 'closed')->avg(function ($session) {
                    return $session->getDuration() ?? 0;
                }) ?? 0;
            }
        } else {
            // Group by day
            $groupedSessions = $sessions->groupBy(function ($session) {
                return $session->created_at->format('M j');
            });

            $period = $this->getDatePeriod($dateRange);
            foreach ($period as $date) {
                $dateLabel = $date->format('M j');
                $labels[] = $dateLabel;
                $dateSessions = $groupedSessions->get($dateLabel, collect());
                $sessionCounts[] = $dateSessions->count();
                $responseTimes[] = $dateSessions->where('status', 'closed')->avg(function ($session) {
                    return $session->getDuration() ?? 0;
                }) ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'sessions' => $sessionCounts,
            'response_times' => array_map(function ($time) {
                return round($time, 1);
            }, $responseTimes)
        ];
    }

    /**
     * Get date period for chart generation
     */
    private function getDatePeriod(string $dateRange): array
    {
        $now = now();
        $dates = [];

        switch ($dateRange) {
            case 'this_week':
            case 'last_week':
                $start = $dateRange === 'this_week' ? $now->startOfWeek() : $now->subWeek()->startOfWeek();
                for ($i = 0; $i < 7; $i++) {
                    $dates[] = $start->copy()->addDays($i);
                }
                break;
            case 'this_month':
            case 'last_month':
                $start = $dateRange === 'this_month' ? $now->startOfMonth() : $now->subMonth()->startOfMonth();
                $end = $dateRange === 'this_month' ? $now->endOfMonth() : $now->subMonth()->endOfMonth();
                $current = $start->copy();
                while ($current->lte($end)) {
                    $dates[] = $current->copy();
                    $current->addDay();
                }
                break;
            case 'last_30_days':
                for ($i = 29; $i >= 0; $i--) {
                    $dates[] = $now->copy()->subDays($i);
                }
                break;
            case 'last_90_days':
                for ($i = 89; $i >= 0; $i -= 7) { // Weekly intervals for 90 days
                    $dates[] = $now->copy()->subDays($i);
                }
                break;
        }

        return $dates;
    }

    /**
     * Export report data
     */
    public function exportReport(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $reportData = $this->generateReportData($request);
        $sessions = $this->getDetailedSessions($request);

        $filename = 'chat_report_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($sessions, $reportData) {
            $file = fopen('php://output', 'w');

            // Add report summary
            fputcsv($file, ['CHAT REPORT SUMMARY']);
            fputcsv($file, ['Generated at', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['']);
            fputcsv($file, ['Total Sessions', $reportData['total_sessions']]);
            fputcsv($file, ['Total Messages', $reportData['total_messages']]);
            fputcsv($file, ['Average Response Time (min)', $reportData['avg_response_time']]);
            fputcsv($file, ['Satisfaction Rate (%)', $reportData['satisfaction_rate']]);
            fputcsv($file, ['']);

            // Add detailed sessions
            fputcsv($file, ['DETAILED SESSIONS']);
            fputcsv($file, [
                'Session ID',
                'Client Name',
                'Client Email',
                'Started At',
                'Ended At',
                'Duration (min)',
                'Messages Count',
                'Operator',
                'Status',
                'Priority'
            ]);

            foreach ($sessions as $session) {
                fputcsv($file, [
                    $session->session_id,
                    $session->getVisitorName(),
                    $session->getVisitorEmail(),
                    $session->started_at->format('Y-m-d H:i:s'),
                    $session->ended_at ? $session->ended_at->format('Y-m-d H:i:s') : '',
                    $session->getDuration() ?? '',
                    $session->messages->count(),
                    $session->operator ? $session->operator->name : 'Bot',
                    ucfirst($session->status),
                    ucfirst($session->priority)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Remove the old dailyReport method and replace with:
    /**
     * Redirect old daily report route to new reports
     */
    public function dailyReport()
    {
        return redirect()->route('admin.chat.reports.index', ['date_range' => 'today']);
    }

    /**
     * Check online status (public method)
     */
    public function onlineStatus(): JsonResponse
    {
        $onlineOperators = ChatOperator::where('is_online', true)
            ->where('is_available', true)
            ->count();

        return response()->json([
            'is_online' => $onlineOperators > 0,
            'operators_count' => $onlineOperators,
        ]);
    }
    /**
     * Get average response time (helper method)
     */
    private function getAverageResponseTime(): float
    {
        $sessions = ChatSession::where('status', 'closed')
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

    public function getOperatorStatus(): JsonResponse
    {
        if (!auth()->user()->hasAdminAccess()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $operator = ChatOperator::where('user_id', auth()->id())->first();

            return response()->json([
                'success' => true,
                'is_online' => $operator ? $operator->is_online : false,
                'is_available' => $operator ? $operator->is_available : false,
                'last_seen_at' => $operator ? $operator->last_seen_at : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'is_online' => false,
                'error' => 'Failed to get operator status'
            ]);
        }
    }

    /**
     * Format messages for API response (make it public instead of protected)
     */
    public function formatMessages($messages): array
    {
        return $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'sender_type' => $message->sender_type,
                'sender_name' => $message->getSenderName(),
                'message' => $message->message,
                'message_type' => $message->message_type,
                'metadata' => $message->metadata,
                'created_at' => $message->created_at->toISOString(),
                'formatted_time' => $message->created_at->format('H:i'),
                'is_from_visitor' => $message->isFromVisitor(),
                'is_from_operator' => $message->isFromOperator(),
                'is_from_bot' => $message->isFromBot(),
            ];
        })->toArray();
    }

}