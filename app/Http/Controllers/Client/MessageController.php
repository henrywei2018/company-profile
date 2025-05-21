<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Display a listing of the client's messages.
     */
    public function index(Request $request)
    {
        $query = Message::where(function ($query) {
            $query->where('client_id', Auth::id())
                  ->orWhere('email', Auth::user()->email);
        })
        ->when($request->filled('search'), function ($query) use ($request) {
            $query->search($request->search);
        })
        ->when($request->filled('read'), function ($query) use ($request) {
            $query->where('is_read', $request->read === 'read');
        })
        ->when($request->filled('type'), function ($query) use ($request) {
            $query->where('type', $request->type);
        });

        // Apply sorting if requested
        if ($request->filled('sort') && $request->filled('direction')) {
            $query->orderBy($request->sort, $request->direction);
        } else {
            $query->latest(); // Default sort by latest
        }

        $messages = $query->paginate(10)->withQueryString();
        
        // Get unread messages count for notifications
        $unreadMessages = Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('client.messages.index', compact('messages', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for creating a new message.
     */
    public function create()
    {
        // Get unread messages count for notifications
        $unreadMessages = Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('client.messages.create', compact('unreadMessages', 'pendingQuotations'));
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:2048', // 2MB max per file
        ]);

        // Create the message
        $message = Message::create([
            'type' => 'client_to_admin',
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'phone' => Auth::user()->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'client_id' => Auth::id(),
            'is_read' => false,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $message->addAttachment($file);
            }
        }

        return redirect()->route('client.messages.index')
            ->with('success', 'Message sent successfully!');
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        // Security check - make sure the message belongs to the authenticated client
        if ($message->client_id !== Auth::id() && $message->email !== Auth::user()->email) {
            abort(403, 'Unauthorized access to message');
        }

        // Mark message as read if not already read and it's from admin to client
        if (!$message->is_read && $message->type === 'admin_to_client') {
            $message->markAsRead();
        }

        // Get related messages (thread)
        $relatedMessages = $message->getThreadMessages()->where('id', '!=', $message->id);

        // Get unread messages count for notifications
        $unreadMessages = Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('client.messages.show', compact('message', 'relatedMessages', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        // Security check - make sure the message belongs to the authenticated client
        if ($message->client_id !== Auth::id() && $message->email !== Auth::user()->email) {
            abort(403, 'Unauthorized access to message');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:2048', // 2MB max per file
        ]);

        // Create the reply message
        $reply = Message::create([
            'type' => 'client_to_admin',
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'phone' => Auth::user()->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'parent_id' => $message->id,
            'client_id' => Auth::id(),
            'is_read' => false,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $reply->addAttachment($file);
            }
        }

        return redirect()->route('client.messages.show', $reply)
            ->with('success', 'Reply sent successfully!');
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message)
    {
        // Security check
        if ($message->client_id !== Auth::id() && $message->email !== Auth::user()->email) {
            abort(403, 'Unauthorized access to message');
        }

        $message->markAsRead();

        return redirect()->back()
            ->with('success', 'Message marked as read.');
    }

    /**
     * Mark a message as unread.
     */
    public function markAsUnread(Message $message)
    {
        // Security check
        if ($message->client_id !== Auth::id() && $message->email !== Auth::user()->email) {
            abort(403, 'Unauthorized access to message');
        }

        $message->update([
            'is_read' => false,
            'read_at' => null,
        ]);

        return redirect()->back()
            ->with('success', 'Message marked as unread.');
    }

    /**
     * Download an attachment file.
     */
    public function downloadAttachment(Message $message, $attachmentId)
    {
        // Security check
        if ($message->client_id !== Auth::id() && $message->email !== Auth::user()->email) {
            abort(403, 'Unauthorized access to message');
        }

        $attachment = MessageAttachment::findOrFail($attachmentId);
        
        // Security check - make sure the attachment belongs to the message
        if ($attachment->message_id !== $message->id) {
            abort(403, 'Unauthorized access to attachment');
        }
        
        // Check if the file exists
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'Attachment file not found');
        }
        
        return Storage::disk('public')->download(
            $attachment->file_path, 
            $attachment->file_name
        );
    }
}