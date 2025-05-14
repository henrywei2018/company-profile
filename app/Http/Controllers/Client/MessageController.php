<?php
// File: app/Http/Controllers/Client/MessageController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Project;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    protected $messageService;

    /**
     * Create a new controller instance.
     *
     * @param MessageService $messageService
     */
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Display a listing of the client's messages.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Use service to get filtered messages
        $messages = $this->messageService->getClientMessages(
            $user,
            $request->only(['read', 'search'])
        );

        return view('client.messages.index', compact('messages'));
    }

    /**
     * Show the form for creating a new message.
     */
    public function create()
    {
        // Get client's projects for dropdown
        $projects = Project::where('client_id', auth()->id())->get();
        
        return view('client.messages.create', compact('projects'));
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'project_id' => 'nullable|exists:projects,id',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        $user = auth()->user();

        // Prepare message data
        $messageData = [
            'client_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'project_id' => $validated['project_id'] ?? null,
            'is_read' => false,
            'is_read_by_client' => true, // Client sent it, so they've "read" it
            'type' => 'client_to_admin',
        ];

        // Use service to create message with attachments
        $message = $this->messageService->createMessage(
            $messageData,
            $request->file('attachments') ?? []
        );

        return redirect()->route('client.messages.index')
            ->with('success', 'Message sent successfully!');
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        // Ensure the message belongs to the authenticated client
        $this->authorize('view', $message);

        // Mark as read if not already
        if (!$message->is_read_by_client) {
            $this->messageService->markAsRead($message, 'client');
        }

        // Get all related messages (thread)
        $thread = $this->getMessageThread($message);

        return view('client.messages.show', compact('message', 'thread'));
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        // Ensure the message belongs to the authenticated client
        $this->authorize('reply', $message);

        $validated = $request->validate([
            'reply_message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        $user = auth()->user();

        // Create a new message as reply
        $replyData = [
            'client_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'subject' => 'RE: ' . $message->subject,
            'message' => $validated['reply_message'],
            'parent_id' => $message->id,
            'project_id' => $message->project_id,
            'is_read' => false,
            'is_read_by_client' => true,
            'type' => 'client_to_admin',
        ];

        // Use service to create reply with attachments
        $reply = $this->messageService->createMessage(
            $replyData,
            $request->file('attachments') ?? []
        );

        return redirect()->route('client.messages.show', $message->id)
            ->with('success', 'Reply sent successfully!');
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(Message $message, $attachmentId)
    {
        // Ensure the message belongs to the authenticated client
        $this->authorize('view', $message);
        
        $attachment = $message->attachments()->findOrFail($attachmentId);
        
        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message)
    {
        // Ensure the message belongs to the authenticated client
        $this->authorize('update', $message);

        $this->messageService->markAsRead($message, 'client');

        return redirect()->back()
            ->with('success', 'Message marked as read.');
    }

    /**
     * Mark a message as unread.
     */
    public function markAsUnread(Message $message)
    {
        // Ensure the message belongs to the authenticated client
        $this->authorize('update', $message);

        $this->messageService->markAsUnread($message, 'client');

        return redirect()->back()
            ->with('success', 'Message marked as unread.');
    }
    
    /**
     * Get complete message thread (parent and replies)
     */
    protected function getMessageThread(Message $message)
    {
        // If message has a parent, start with the parent
        if ($message->parent_id) {
            $rootMessage = Message::find($message->parent_id);
        } else {
            $rootMessage = $message;
        }
        
        // Get all replies to the root message
        $thread = Message::where(function($query) use ($rootMessage) {
                $query->where('id', $rootMessage->id)
                      ->orWhere('parent_id', $rootMessage->id);
            })
            ->orderBy('created_at')
            ->get();
            
        return $thread;
    }
}