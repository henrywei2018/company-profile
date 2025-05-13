<?php
// File: app/Http/Controllers/Admin/MessageController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages.
     */
    public function index(Request $request)
    {
        $messages = Message::when($request->filled('read'), function ($query) use ($request) {
            return $query->where('is_read', $request->read === 'read');
        })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%")
                        ->orWhere('subject', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(15);

        return view('admin.messages.index', compact('messages'));
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        // Mark message as read if not already
        if (!$message->is_read) {
            $message->markAsRead();
        }

        return view('admin.messages.show', compact('message'));
    }

    /**
     * Mark the message as read/unread.
     */
    public function toggleRead(Message $message)
    {
        $message->update([
            'is_read' => !$message->is_read,
            'read_at' => $message->is_read ? null : now(),
        ]);

        return redirect()->back()
            ->with('success', 'Message status updated!');
    }

    /**
     * Remove the specified message.
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message deleted successfully!');
    }

    /**
     * Mark multiple messages as read.
     */
    public function markAsRead(Message $message)
    {
        // Only mark as read if it's from client to admin or a contact form
        if (in_array($message->type, ['client_to_admin', 'contact_form'])) {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Message marked as read.');
    }

    /**
     * Delete multiple messages.
     */
    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        Message::whereIn('id', $ids)->delete();

        return redirect()->back()
            ->with('success', count($ids) . ' messages deleted successfully.');
    }
}
