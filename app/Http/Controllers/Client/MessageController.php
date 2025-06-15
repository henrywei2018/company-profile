<?php
// app/Http/Controllers/Client/MessageController.php
// Clean version focused on web requests only

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Services\ClientAccessService;
use App\Services\MessageService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
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
            'priority' => 'nullable|string|in:low,normal,high,urgent',
            'project_id' => 'nullable|exists:projects,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'sort' => 'nullable|string|in:created_at,updated_at,subject,is_read',
            'direction' => 'nullable|string|in:asc,desc',
        ]);

        // Get messages using service
        $messages = $this->messageService->getClientMessages($user, $filters, 15);

        // Get message statistics for dashboard
        $statistics = $this->messageService->getMessageStatistics($user);
        
        // Get filter options
        $filterOptions = $this->clientAccessService->getMessageFilters($user);
        
        // Get recent activities
        $recentActivity = $this->messageService->getRecentActivity($user, 5);

        return view('client.messages.index', compact(
            'messages',
            'statistics',
            'filterOptions',
            'recentActivity',
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
        $prefillData = $request->only(['subject', 'project_id', 'type', 'priority']);

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
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,zip,rar',
            'attachments' => 'nullable|array|max:5',
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
     * Display the specified message with thread.
     */
    public function show(Message $message)
    {
        $user = auth()->user();
        
        // Ensure message belongs to authenticated client
        if (!$this->clientAccessService->canAccessMessage($user, $message)) {
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
            $this->messageService->markAsRead($message, $user);
        }

        // Get conversation thread
        $thread = $this->messageService->getMessageThread($message);
        
        // Get related messages from same project
        $relatedMessages = collect();
        if ($message->project_id) {
            $relatedMessages = $this->clientAccessService->getProjectMessages($user, $message->project_id)
                ->where('id', '!=', $message->id)
                ->with(['attachments'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // Check if user can reply to this message
        $canReply = $this->clientAccessService->canReplyToMessage($user, $message);
        
        // Check if user can escalate priority
        $canEscalate = $this->clientAccessService->canEscalateMessage($user, $message);

        return view('client.messages.show', compact(
            'message',
            'thread',
            'relatedMessages',
            'canReply',
            'canEscalate'
        ));
    }

    /**
     * Reply to an admin message.
     */
    public function reply(Request $request, Message $message)
    {
        $user = auth()->user();
        
        // Security checks
        if (!$this->clientAccessService->canAccessMessage($user, $message)) {
            abort(403, 'Unauthorized access to this message.');
        }
        
        if (!$this->clientAccessService->canReplyToMessage($user, $message)) {
            return redirect()->back()
                ->with('error', 'You cannot reply to this message.');
        }
        
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,zip,rar',
            'attachments' => 'nullable|array|max:5',
        ]);

        // Create reply using service
        $reply = $this->messageService->createClientReply(
            $message,
            $validated['message'],
            $request->file('attachments', [])
        );
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);

        return redirect()->route('client.messages.show', $message)
            ->with('success', 'Reply sent successfully!');
    }

    /**
     * Mark message as urgent (escalate priority).
     */
    public function markUrgent(Message $message)
    {
        $user = auth()->user();
        
        if (!$this->clientAccessService->canEscalateMessage($user, $message)) {
            return redirect()->back()
                ->with('error', 'You cannot escalate this message priority.');
        }
        
        $message->update(['priority' => 'urgent']);
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);
        
        return redirect()->back()
            ->with('success', 'Message marked as urgent. We will prioritize your request.');
    }

    /**
     * Toggle message read status.
     */
    public function toggleRead(Message $message)
    {
        $user = auth()->user();
        
        // Ensure message belongs to authenticated client
        if (!$this->clientAccessService->canAccessMessage($user, $message)) {
            abort(403, 'Unauthorized access to this message.');
        }

        if ($message->is_read) {
            $this->messageService->markAsUnread($message);
            $status = 'unread';
        } else {
            $this->messageService->markAsRead($message, $user);
            $status = 'read';
        }
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);

        return redirect()->back()
            ->with('success', "Message marked as {$status}.");
    }

    /**
     * Get project-specific messages.
     */
    public function projectMessages(Request $request, $projectId)
    {
        $user = auth()->user();
        
        // Verify project access
        $project = \App\Models\Project::findOrFail($projectId);
        if (!$this->clientAccessService->canAccessProject($user, $project)) {
            abort(403, 'Unauthorized access to this project.');
        }
        
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'read' => 'nullable|string|in:read,unread',
            'sort' => 'nullable|string|in:created_at,updated_at,subject',
            'direction' => 'nullable|string|in:asc,desc',
        ]);
        
        // Get project messages
        $query = $this->clientAccessService->getProjectMessages($user, $projectId);
        
        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('subject', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('message', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (isset($filters['read'])) {
            $isRead = $filters['read'] === 'read';
            $query->where('is_read', $isRead);
        }
        
        // Sort
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $messages = $query->with(['attachments', 'parent', 'replies'])
            ->paginate(15);

        return view('client.messages.project', compact(
            'messages', 
            'project', 
            'filters'
        ));
    }

    /**
     * Bulk action for multiple messages.
     */
    public function bulkAction(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'action' => 'required|string|in:mark_read,mark_unread,delete',
            'message_ids' => 'required|array',
            'message_ids.*' => 'integer|exists:messages,id',
        ]);
        
        // Get messages that belong to the client
        $messages = $this->clientAccessService->getClientMessages($user)
            ->whereIn('id', $validated['message_ids'])
            ->get();
        
        if ($messages->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No valid messages selected.');
        }
        
        $count = 0;
        foreach ($messages as $message) {
            switch ($validated['action']) {
                case 'mark_read':
                    if (!$message->is_read) {
                        $this->messageService->markAsRead($message, $user);
                        $count++;
                    }
                    break;
                    
                case 'mark_unread':
                    if ($message->is_read) {
                        $this->messageService->markAsUnread($message);
                        $count++;
                    }
                    break;
                    
                case 'delete':
                    // Only allow deletion of client's own messages (not admin replies)
                    if ($message->user_id === $user->id && 
                        !in_array($message->type, ['admin_to_client'])) {
                        $message->delete();
                        $count++;
                    }
                    break;
            }
        }
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);
        
        $actionName = str_replace('_', ' ', $validated['action']);
        return redirect()->back()
            ->with('success', "{$count} messages {$actionName}.");
    }

    /**
     * Download an attachment.
     */
    public function downloadAttachment(Message $message, MessageAttachment $attachment)
    {
        $user = auth()->user();
        
        // Security checks
        if (!$this->clientAccessService->canAccessMessage($user, $message)) {
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
     * Get available message types for clients.
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