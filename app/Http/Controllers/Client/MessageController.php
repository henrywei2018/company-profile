<?php
// File: app/Http/Controllers/Client/MessageController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the client's messages.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $messages = Message::where('client_id', $user->id)
            ->when($request->filled('read'), function ($query) use ($request) {
                return $query->where('is_read_by_client', $request->read === 'read');
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('subject', 'like', "%{$request->search}%")
                        ->orWhere('message', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('client.messages.index', compact('messages'));
    }

    /**
     * Show the form for creating a new message.
     */
    public function create()
    {
        return view('client.messages.create');
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Create message
        $message = Message::create([
            'client_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'is_read' => false,
            'is_read_by_client' => true, // Client sent it, so they've "read" it
            'type' => 'client_to_admin',
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message_attachments/' . $message->id, 'public');

                $message->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }

        // Send notification to admin (to be implemented)
        // Notification::route('mail', config('mail.admin'))
        //    ->notify(new ClientMessageNotification($message));

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
            $message->update(['is_read_by_client' => true]);
        }

        return view('client.messages.show', compact('message'));
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        // Ensure the message belongs to the authenticated client
        $this->authorize('reply', $message);

        $user = auth()->user();

        $validated = $request->validate([
            'reply_message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Create a new message as reply
        $reply = Message::create([
            'client_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'subject' => 'RE: ' . $message->subject,
            'message' => $validated['reply_message'],
            'parent_id' => $message->id,
            'is_read' => false,
            'is_read_by_client' => true,
            'type' => 'client_to_admin',
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message_attachments/' . $reply->id, 'public');

                $reply->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }

        // Send notification to admin (to be implemented)
        // Notification::route('mail', config('mail.admin'))
        //    ->notify(new ClientMessageNotification($reply));

        return redirect()->route('client.messages.show', $message->id)
            ->with('success', 'Reply sent successfully!');
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message)
    {
        // Only mark as read if it's from admin to client
        if ($message->type === 'admin_to_client') {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

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

        $message->update(['is_read_by_client' => false]);

        return redirect()->back()
            ->with('success', 'Message marked as unread.');
    }
}
