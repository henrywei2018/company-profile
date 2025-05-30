<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\User;
use App\Services\ChatService;
use App\Models\ChatOperator;
use App\Models\ChatTemplate;
use App\Models\ChatMessage;
use App\Facades\Notifications;
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
            $session = $this->chatService->startSession($user);

            // Store session ID in browser session
            session(['chat_session_id' => $session->session_id]);

            // Send notification to available operators using centralized system
            Notifications::send('chat.session_started', $session);

            return response()->json([
                'success' => true,
                'session_id' => $session->session_id,
                'messages' => $this->formatMessages($session->messages)
            ]);

        } catch (\Exception $e) {
            Log::error('Chat session start failed: ' . $e->getMessage());

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
            $session = ChatSession::where('session_id', $request->session_id)->first();

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
                'visitor'
            );

            // Notify operators about new message using centralized system
            Notifications::send('chat.message_received', $session, null, ['database']);

            // Get updated messages (last 20 messages)
            $messages = $session->messages()->orderBy('created_at')->take(20)->get();

            return response()->json([
                'success' => true,
                'message_id' => $message->id,
                'messages' => $this->formatMessages($messages)
            ]);

        } catch (\Exception $e) {
            Log::error('Chat message failed: ' . $e->getMessage());

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
            $session = ChatSession::where('session_id', $request->session_id)->first();

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
            Log::error('Get chat messages failed: ' . $e->getMessage());

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
            $session = ChatSession::where('session_id', $request->session_id)->first();

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
            Log::error('Update client info failed: ' . $e->getMessage());

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
            $session = ChatSession::where('session_id', $request->session_id)->first();

            // Verify session belongs to authenticated user
            if (!$session || $session->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat session not found or access denied'
                ], 404);
            }

            // Close the session
            $session->update([
                'status' => 'closed',
                'ended_at' => now(),
                'close_reason' => 'Closed by client'
            ]);

            // Notify about session closure using centralized system
            Notifications::send('chat.session_closed', $session);

            // Remove from browser session
            session()->forget('chat_session_id');

            return response()->json([
                'success' => true,
                'message' => 'Chat session closed'
            ]);

        } catch (\Exception $e) {
            Log::error('Close chat session failed: ' . $e->getMessage());

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
                $session = ChatSession::where('session_id', $sessionId)->first();
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
            $messages = $session->messages()->orderBy('created_at')->get();

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
            Log::error('Get chat session failed: ' . $e->getMessage());

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

    // ===== ADMIN METHODS =====

    /**
     * Admin chat dashboard
     */
    public function index()
    {
        // Check if user is admin
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        // Get statistics manually since ChatService might not have getStatistics
        $statistics = [
            'total_sessions' => ChatSession::count(),
            'active_sessions' => ChatSession::where('status', 'active')->count(),
            'waiting_sessions' => ChatSession::where('status', 'waiting')->count(),
            'closed_sessions_today' => ChatSession::whereDate('ended_at', today())->where('status', 'closed')->count(),
            'avg_response_time' => 0, // Calculate if needed
            'satisfaction_rate' => 0, // Calculate if needed
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

        // Save settings using the settings helper
        foreach ($validated as $key => $value) {
            update_setting($key, $value);
        }

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

        try {
            // Create operator message
            $message = $chatSession->messages()->create([
                'sender_type' => 'operator',
                'sender_id' => auth()->id(),
                'message' => $request->message,
                'message_type' => 'text',
            ]);

            // Update session activity
            $chatSession->update([
                'last_activity_at' => now(),
                'status' => 'active',
                'assigned_operator_id' => auth()->id(),
            ]);

            // Notify client about new message using centralized system
            if ($chatSession->user) {
                Notifications::send('chat.operator_reply', $chatSession, $chatSession->user);
            }

            return redirect()->back()->with('success', 'Message sent successfully!');

        } catch (\Exception $e) {
            Log::error('Admin chat reply failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send message.');
        }
    }

    /**
     * Close chat session (admin)
     */
    public function closeSession(ChatSession $chatSession)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        // Close the session
        $chatSession->update([
            'status' => 'closed',
            'ended_at' => now(),
            'close_reason' => 'Closed by admin: ' . auth()->user()->name
        ]);

        // Notify about session closure using centralized system
        Notifications::send('chat.session_closed', $chatSession);

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

        $statistics = [
            'total_sessions' => ChatSession::count(),
            'active_sessions' => ChatSession::where('status', 'active')->count(),
            'waiting_sessions' => ChatSession::where('status', 'waiting')->count(),
            'closed_sessions_today' => ChatSession::whereDate('ended_at', today())->where('status', 'closed')->count(),
            'total_messages' => ChatMessage::count(),
            'online_operators' => ChatOperator::where('is_online', true)->count(),
            'avg_response_time' => 0, // Calculate if needed
            'satisfaction_rate' => 0, // Calculate if needed
        ];

        return response()->json($statistics);
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

    /**
     * Generate PDF reports for chat sessions
     */
    public function reports(Request $request)
    {
        Log::info('🟢 Entering reports() function in ChatController');

        if (!auth()->check()) {
            Log::warning('🚨 User not logged in!');
        } elseif (!auth()->user()->hasAdminAccess()) {
            Log::warning('🚫 User does not have admin access.', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
            ]);
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
     * Export report data as PDF
     */
    public function exportReport(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $reportData = $this->generateReportData($request);
        $sessions = $this->getDetailedSessions($request);

        $data = [
            'reportData' => $reportData,
            'sessions' => $sessions,
            'filters' => $request->all(),
            'generatedBy' => auth()->user()->name,
            'generatedAt' => now(),
        ];

        $pdf = Pdf::loadView('admin.chat.reports.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'margin-top' => 20,
                'margin-bottom' => 20,
                'margin-left' => 15,
                'margin-right' => 15,
            ]);

        $filename = 'chat_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
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
     * Get operator status for current user
     */
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
     * Format messages for API response
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

    /**
     * Send notification when chat session needs attention
     */
    protected function notifyIfSessionNeedsAttention(ChatSession $session): void
    {
        // Check if session has been waiting too long
        if ($session->status === 'waiting') {
            $waitingMinutes = now()->diffInMinutes($session->created_at);
            if ($waitingMinutes > 10) { // Alert if waiting more than 10 minutes
                Notifications::send('chat.session_waiting', $session);
            }
        }

        // Check if session is inactive
        if ($session->last_activity_at) {
            $inactiveMinutes = now()->diffInMinutes($session->last_activity_at);
            if ($inactiveMinutes > 30) { // Alert if inactive more than 30 minutes
                Notifications::send('chat.session_inactive', $session);
            }
        }
    }

    /**
     * Update operator availability
     */
    public function updateAvailability(Request $request): JsonResponse
    {
        if (!auth()->user()->hasAdminAccess()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'is_available' => 'required|boolean',
        ]);

        try {
            $operator = ChatOperator::firstOrCreate(
                ['user_id' => auth()->id()],
                ['is_online' => true, 'is_available' => $request->is_available]
            );

            $operator->update([
                'is_available' => $request->is_available,
                'last_seen_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'is_available' => $operator->is_available,
                'message' => $request->is_available ? 'You are now available for chat' : 'You are now unavailable for chat'
            ]);

        } catch (\Exception $e) {
            Log::error('Update operator availability failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update availability'
            ], 500);
        }
    }

    /**
     * Bulk update chat sessions
     */
    public function bulkUpdate(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $request->validate([
            'session_ids' => 'required|array',
            'session_ids.*' => 'exists:chat_sessions,id',
            'action' => 'required|in:close,assign,priority',
            'value' => 'nullable|string',
        ]);

        $sessionIds = $request->session_ids;
        $action = $request->action;
        $value = $request->value;
        $updated = 0;

        try {
            foreach ($sessionIds as $sessionId) {
                $session = ChatSession::find($sessionId);
                if (!$session) continue;

                switch ($action) {
                    case 'close':
                        if ($session->status !== 'closed') {
                            $session->update([
                                'status' => 'closed',
                                'ended_at' => now(),
                                'close_reason' => 'Bulk closed by admin'
                            ]);
                            Notifications::send('chat.session_closed', $session);
                            $updated++;
                        }
                        break;

                    case 'assign':
                        if ($value && $value !== $session->assigned_operator_id) {
                            $session->update(['assigned_operator_id' => $value]);
                            $updated++;
                        }
                        break;

                    case 'priority':
                        if ($value && $value !== $session->priority) {
                            $session->update(['priority' => $value]);
                            $updated++;
                        }
                        break;
                }
            }

            return redirect()->back()->with('success', "Updated {$updated} chat session(s) successfully!");

        } catch (\Exception $e) {
            Log::error('Bulk update chat sessions failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update chat sessions.');
        }
    }

    /**
     * Archive old chat sessions
     */
    public function archiveOldSessions()
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        try {
            // Archive sessions older than 30 days
            $cutoffDate = now()->subDays(30);
            $archivedCount = ChatSession::where('status', 'closed')
                ->where('ended_at', '<', $cutoffDate)
                ->update(['status' => 'archived']);

            // Send notification about archival
            if ($archivedCount > 0) {
                Notifications::send('system.chat_sessions_archived', [
                    'count' => $archivedCount,
                    'cutoff_date' => $cutoffDate->format('Y-m-d'),
                ]);
            }

            return redirect()->back()->with('success', "Archived {$archivedCount} old chat session(s)!");

        } catch (\Exception $e) {
            Log::error('Archive old chat sessions failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to archive old sessions.');
        }
    }
}