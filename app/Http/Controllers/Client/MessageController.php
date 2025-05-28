<?php
// File: app/Http/Controllers/Client/MessageController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Services\ClientAccessService;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    protected ClientAccessService $clientAccessService;
    protected MessageService $messageService;

    public function __construct(
        ClientAccessService $clientAccessService,
        MessageService $messageService
    ) {
        $this->clientAccessService = $clientAccessService;
        $this->messageService = $messageService;
    }

    /**
     * Display a listing of the client's messages.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get messages using proper schema alignment (messages table uses user_id)
        $filters = [
            'search' => $request->input('search'),
            'read' => $request->input('read'),
            'type' => $request->input('type'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        // Use the message service to get properly filtered messages
        $messages = $this->messageService->getClientMessages($user, $filters, 15);

        // Get message statistics
        $statistics = $this->getMessageStatistics($user);
        
        // Get filter options
        $messageTypes = $this->getMessageTypes();

        return view('client.messages.index', compact(
            'messages',
            'statistics',
            'messageTypes'
        ));
    }

    /**
     * Show the form for creating a new message.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get user's projects for context selection
        $projects = \App\Models\Project::where('client_id', $user->id)
            ->orderBy('title')
            ->get(['id', 'title', 'status']);
            
        // Get available message types
        $messageTypes = $this->getClientMessageTypes();

        return view('client.messages.create', compact(
            'projects',
            'messageTypes'
        ));
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
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
            if (!$project || $project->client_id !== $user->id) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['project_id' => 'Invalid project selected.']);
            }
        }

        // Prepare message data (aligned with messages table schema)
        $messageData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company' => $user->company,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'type' => $validated['type'] ?? 'general',
            'user_id' => $user->id, // Messages table uses user_id, not client_id
            'project_id' => $validated['project_id'] ?? null,
            'is_read' => false,
            'is_replied' => false,
        ];

        // Create message using service
        $message = $this->messageService->createMessage(
            $messageData,
            $request->file('attachments', [])
        );

        return redirect()->route('client.messages.show', $message)
            ->with('success', 'Message sent successfully! We will respond within 24 hours.');
    }
    

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        // Ensure message belongs to authenticated client (using user_id per schema)
        if ($message->user_id !== Auth::id()) {
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
        $thread = $this->getMessageThread($message);
        
        // Get related messages from same project
        $relatedMessages = [];
        if ($message->project_id) {
            $relatedMessages = Message::where('project_id', $message->project_id)
                ->where('user_id', Auth::id())
                ->where('id', '!=', $message->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('client.messages.show', compact(
            'message',
            'thread',
            'relatedMessages'
        ));
    }

    /**
     * Show reply form for a message.
     */
    public function showReplyForm(Message $message)
    {
        // Ensure message belongs to authenticated client
        if ($message->user_id !== Auth::id()) {
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
        if ($message->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this message.');
        }

        $user = Auth::user();
        
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        // Create reply message (aligned with schema)
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
        $reply = $this->messageService->createMessage(
            $replyData,
            $request->file('attachments', [])
        );

        // Mark original message as replied
        $message->update(['is_replied' => true, 'replied_at' => now()]);

        return redirect()->route('client.messages.show', $reply)
            ->with('success', 'Reply sent successfully!');
    }
    

    /**
     * Mark a message as read/unread.
     */
    public function toggleRead(Message $message)
    {
        // Ensure message belongs to authenticated client
        if ($message->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this message.');
        }

        if ($message->is_read) {
            $this->messageService->markAsUnread($message, 'client');
            $status = 'unread';
        } else {
            $this->messageService->markAsRead($message, 'client');
            $status = 'read';
        }

        return redirect()->back()
            ->with('success', "Message marked as {$status}.");
    }

    /**
     * Download an attachment.
     */
    public function downloadAttachment(Message $message, MessageAttachment $attachment)
    {
        // Security checks
        if ($message->user_id !== Auth::id()) {
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
     * Get message statistics for client.
     */
    protected function getMessageStatistics($user): array
    {
        // Using user_id as per messages table schema
        $query = Message::where('user_id', $user->id);
        
        return [
            'total' => (clone $query)->count(),
            'unread' => (clone $query)->where('is_read', false)->count(),
            'replied' => (clone $query)->where('is_replied', true)->count(),
            'this_month' => (clone $query)->whereMonth('created_at', now()->month)->count(),
            'by_type' => (clone $query)->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'recent_activity' => (clone $query)->where('created_at', '>=', now()->subDays(7))->count(),
        ];
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
     * Get full message thread/conversation.
     */
    protected function getMessageThread(Message $message): \Illuminate\Database\Eloquent\Collection
    {
        $rootMessage = $message->getRootMessage();
        
        return Message::where(function($query) use ($rootMessage) {
            $query->where('id', $rootMessage->id)
                  ->orWhere('parent_id', $rootMessage->id);
        })
        ->where('user_id', Auth::id())
        ->with(['attachments'])
        ->orderBy('created_at')
        ->get();
    }

    /**
     * Check if message is directed to client.
     */
    protected function isMessageToClient(Message $message): bool
    {
        return in_array($message->type, ['admin_to_client', 'support_response']);
    }
}