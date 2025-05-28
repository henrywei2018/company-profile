<?php
// File: app/Http/Controllers/Client/MessageController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Services\ClientAccessService;
use App\Services\MessageService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    protected ClientAccessService $clientAccessService;
    protected MessageService $messageService;
    protected DashboardService $dashboardService;

    public function __construct(
        ClientAccessService $clientAccessService,
        MessageService $messageService,
        DashboardService $dashboardService
    ) {
        $this->clientAccessService = $clientAccessService;
        $this->messageService = $messageService;
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display a listing of the client's messages.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Validate filters
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'read' => 'nullable|string|in:read,unread',
            'type' => 'nullable|string|in:general,support,project_inquiry,complaint,feedback,client_reply',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'sort' => 'nullable|string|in:created_at,updated_at,subject,is_read',
            'direction' => 'nullable|string|in:asc,desc',
        ]);

        // Get messages using service
        $messages = $this->messageService->getClientMessages($user, $filters, 15);

        // Get message statistics
        $statistics = $this->messageService->getMessageStatistics($user);
        
        // Get filter options
        $messageTypes = $this->getMessageTypes();
        
        // Get recent activities
        $dashboardData = $this->dashboardService->getDashboardData($user);
        $recentActivities = collect($dashboardData['recent_activities'] ?? [])
            ->where('type', 'message')
            ->take(5)
            ->values();

        return view('client.messages.index', compact(
            'messages',
            'statistics',
            'messageTypes',
            'recentActivities',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new message.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        
        // Get user's projects for context selection
        $projects = $this->clientAccessService->getClientProjects($user)
            ->orderBy('title')
            ->get(['id', 'title', 'status']);
            
        // Get available message types
        $messageTypes = $this->getClientMessageTypes();
        
        // Pre-fill data from query parameters
        $prefillData = $request->only(['subject', 'project_id', 'type']);

        return view('client.messages.create', compact(
            'projects',
            'messageTypes',
            'prefillData'
        ));
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'type' => 'nullable|string|in:general,support,project_inquiry,complaint,feedback',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'nullable|string|in:low,normal,high,urgent',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        // Validate project ownership if project_id is provided
        if ($validated['project_id']) {
            $project = \App\Models\Project::find($validated['project_id']);
            if (!$project || !$this->clientAccessService->canAccessProject($user, $project)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['project_id' => 'Invalid project selected.']);
            }
        }

        // Prepare message data
        $messageData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company' => $user->company,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'type' => $validated['type'] ?? 'general',
            'user_id' => $user->id,
            'project_id' => $validated['project_id'] ?? null,
            'priority' => $validated['priority'] ?? 'normal',
            'is_read' => false,
            'is_replied' => false,
        ];

        // Create message using service
        $message = $this->messageService->createMessage(
            $messageData,
            $request->file('attachments', [])
        );
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);

        return redirect()->route('client.messages.show', $message)
            ->with('success', 'Message sent successfully! We will respond within 24 hours.');
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        // Ensure message belongs to authenticated client
        if (!$this->clientAccessService->canAccessMessage(auth()->user(), $message)) {
            abort(403, 'Unauthorized access to this message.');
        }
        
        // Load relationships
        $message->load([
            'attachments',
            'project',
            'parent',
            'replies' => fn($q) => $q->orderBy('created_at'),
            'replies.attachments'
        ]);

        // Mark as read if it's a message TO the client and unread
        if (!$message->is_read && $this->isMessageToClient($message)) {
            $this->messageService->markAsRead($message, 'client');
        }

        // Get conversation thread
        $thread = $this->messageService->getMessageThread($message);
        
        // Get related messages from same project
        $relatedMessages = [];
        if ($message->project_id) {
            $relatedMessages = $this->clientAccessService->getClientMessages(auth()->user())
                ->where('project_id', $message->project_id)
                ->where('id', '!=', $message->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // Get message alerts
        $messageAlerts = $this->getMessageAlerts($message);

        return view('client.messages.show', compact(
            'message',
            'thread',
            'relatedMessages',
            'messageAlerts'
        ));
    }

    /**
     * Get message alerts and notifications.
     */
    protected function getMessageAlerts(Message $message): array
    {
        $alerts = [];
        
        // Check for urgent messages
        if ($message->priority === 'urgent' && !$message->is_replied) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Urgent Message',
                'message' => 'This is marked as urgent and requires immediate attention.',
            ];
        }
        
        // Check for old unreplied messages
        if (!$message->is_replied && 
            $message->created_at->diffInHours(now()) > 48) {
            
            $alerts[] = [
                'type' => 'info',
                'title' => 'Follow-up Available',
                'message' => 'You can send a follow-up message if you need a quicker response.',
                'action' => [
                    'text' => 'Send Follow-up',
                    'url' => route('client.messages.reply', $message),
                ],
            ];
        }
        
        return $alerts;
    }

    /**
     * Show reply form for a message.
     */
    public function showReplyForm(Message $message)
    {
        // Ensure message belongs to authenticated client
        if (!$this->clientAccessService->canAccessMessage(auth()->user(), $message)) {
            abort(403, 'Unauthorized access to this message.');
        }

        return view('client.messages.reply', compact('message'));
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        // Ensure message belongs to authenticated client
        if (!$this->clientAccessService->canAccessMessage(auth()->user(), $message)) {
            abort(403, 'Unauthorized access to this message.');
        }

        $user = auth()->user();
        
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        // Create reply message data
        $replyData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company' => $user->company,
            'subject' => 'Re: ' . $message->subject,
            'message' => $validated['message'],
            'type' => 'client_reply',
            'user_id' => $user->id,
            'project_id' => $message->project_id,
            'parent_id' => $message->getRootMessage()->id,
            'is_read' => false,
            'is_replied' => false,
        ];

        // Create reply using service
        $reply = $this->messageService->createReply(
            $message,
            $replyData,
            $request->file('attachments', [])
        );
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);

        return redirect()->route('client.messages.show', $reply)
            ->with('success', 'Reply sent successfully!');
    }

    /**
     * Mark a message as read/unread.
     */
    public function toggleRead(Message $message)
    {
        // Ensure message belongs to authenticated client
        if (!$this->clientAccessService->canAccessMessage(auth()->user(), $message)) {
            abort(403, 'Unauthorized access to this message.');
        }

        if ($message->is_read) {
            $this->messageService->markAsUnread($message, 'client');
            $status = 'unread';
        } else {
            $this->messageService->markAsRead($message, 'client');
            $status = 'read';
        }
        
        // Clear dashboard cache
        $this->dashboardService->clearCache(auth()->user());

        return redirect()->back()
            ->with('success', "Message marked as {$status}.");
    }

    /**
     * Download an attachment.
     */
    public function downloadAttachment(Message $message, MessageAttachment $attachment)
    {
        // Security checks
        if (!$this->clientAccessService->canAccessMessage(auth()->user(), $message)) {
            abort(403, 'Unauthorized access to this message.');
        }
        
        if ($attachment->message_id !== $message->id) {
            abort(403, 'Invalid attachment for this message.');
        }
        
        // Check if file exists
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }
        
        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }

    /**
     * Get unread message count for API.
     */
    public function getUnreadCount(): JsonResponse
    {
        $user = auth()->user();
        $count = $this->messageService->getUnreadCount($user);
        
        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Bulk mark messages as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();
        
        $messages = $this->clientAccessService->getClientMessages($user)
            ->where('is_read', false)
            ->pluck('id')
            ->toArray();
        
        $count = $this->messageService->bulkMarkAsRead($messages, $user);
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "{$count} messages marked as read",
        ]);
    }

    /**
     * Get message statistics for API.
     */
    public function getStatistics(): JsonResponse
    {
        $user = auth()->user();
        $statistics = $this->messageService->getMessageStatistics($user);
        
        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Get available message types.
     */
    protected function getMessageTypes(): array
    {
        return [
            'general' => 'General Inquiry',
            'support' => 'Technical Support',
            'project_inquiry' => 'Project Related',
            'complaint' => 'Complaint',
            'feedback' => 'Feedback',
            'client_reply' => 'Reply',
        ];
    }

    /**
     * Get message types available for clients to create.
     */
    protected function getClientMessageTypes(): array
    {
        return [
            'general' => 'General Inquiry',
            'support' => 'Technical Support', 
            'project_inquiry' => 'Project Related',
            'complaint' => 'Complaint',
            'feedback' => 'Feedback',
        ];
    }

    /**
     * Check if message is directed to client.
     */
    protected function isMessageToClient(Message $message): bool
    {
        return in_array($message->type, ['admin_to_client', 'support_response']);
    }
}