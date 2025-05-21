<?php
// Enhanced version of app/Http/Controllers/Admin/MessageController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Notifications\MessageReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages with filtering.
     */
    public function index(Request $request)
    {
        $query = Message::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search($request->search);
            })
            ->when($request->filled('read'), function ($query) use ($request) {
                $query->where('is_read', $request->read === 'read');
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->filled('created_from') && $request->filled('created_to'), function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    $request->created_from . ' 00:00:00', 
                    $request->created_to . ' 23:59:59'
                ]);
            });

        // Apply sorting if requested
        if ($request->filled('sort') && $request->filled('direction')) {
            $query->orderBy($request->sort, $request->direction);
        } else {
            $query->latest(); // Default sort by latest
        }

        $messages = $query->paginate(15)->withQueryString();

        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('admin.messages.index', compact('messages', 'unreadMessages', 'pendingQuotations'));
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

        // Get related messages (thread)
        $relatedMessages = $message->getThreadMessages()->where('id', '!=', $message->id);

        // Get other messages from this client/email (limited to 5)
        $clientMessages = Message::where('email', $message->email)
            ->where('id', '!=', $message->id)
            ->latest()
            ->take(5)
            ->get();
        
        $totalClientMessages = Message::where('email', $message->email)->count();

        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();

        return view('admin.messages.show', compact(
            'message', 
            'relatedMessages', 
            'clientMessages', 
            'totalClientMessages',
            'unreadMessages',
            'pendingQuotations'
        ));
    }

    /**
     * Toggle the read status of the message.
     */
    public function toggleRead(Message $message)
    {
        $message->toggleReadStatus();

        return redirect()->back()
            ->with('success', 'Message status updated!');
    }

    /**
     * Remove the specified message.
     */
    public function destroy(Message $message)
    {
        // Delete all attachments
        foreach ($message->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message deleted successfully!');
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:2048', // 2MB max per file
        ]);

        // Create the reply message
        $reply = Message::create([
            'type' => 'admin_to_client',
            'name' => auth()->user()->name,
            'email' => config('mail.from.address'),
            'subject' => $request->subject,
            'message' => $request->message,
            'parent_id' => $message->id,
            'client_id' => $message->client_id,
            'is_read' => true, // Admin replies are marked as read by default
            'read_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $reply->addAttachment($file);
            }
        }

        // Send notification to the client if it's a registered user
        if ($message->client) {
            $message->client->notify(new MessageReplyNotification($reply));
        } else {
            // For contact form messages, send an email directly
            try {
                Notification::route('mail', $message->email)
                    ->notify(new MessageReplyNotification($reply));
            } catch (\Exception $e) {
                // Log the error but don't stop the process
                \Log::error('Failed to send email notification: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.messages.show', $reply)
            ->with('success', 'Reply sent successfully!');
    }

    /**
     * Mark a batch of messages as read.
     */
    public function markAsRead(Request $request)
    {
        // Only mark messages where it makes sense (client to admin or contact form)
        Message::whereIn('type', ['client_to_admin', 'contact_form'])
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return redirect()->back()
            ->with('success', 'All messages marked as read.');
    }

    /**
     * Delete multiple messages.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required',
        ]);

        $ids = explode(',', $request->ids);
        
        // Delete attachments for these messages first
        $attachments = MessageAttachment::whereIn('message_id', $ids)->get();
        foreach ($attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }
        
        // Delete the messages
        $count = Message::whereIn('id', $ids)->delete();

        return redirect()->back()
            ->with('success', $count . ' messages deleted successfully.');
    }

    /**
     * Download an attachment file.
     */
    public function downloadAttachment(Message $message, $attachmentId)
    {
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