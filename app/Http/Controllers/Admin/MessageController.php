<?php

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
        // Debug: Log the incoming parameters
        \Log::info('Message filter parameters:', $request->all());
        
        $query = Message::query()
            ->with(['user', 'attachments', 'repliedBy']) // Eager load relationships
            // Exclude admin-to-client messages from main listing
            ->excludeAdminMessages();
            
        // Debug: Check what types exist in database
        $allTypes = Message::distinct()->pluck('type')->toArray();
        \Log::info('All message types in database:', $allTypes);
        
        // Debug: Count messages by type
        foreach($allTypes as $type) {
            $count = Message::where('type', $type)->count();
            \Log::info("Type '{$type}': {$count} messages");
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            \Log::info('Applying search filter:', ['search' => $request->search]);
            $query->search($request->search);
        }
        
        // Apply status filter  
        if ($request->filled('status')) {
            \Log::info('Applying status filter:', ['status' => $request->status]);
            switch ($request->status) {
                case 'unread':
                    $query->where('is_read', false);
                    break;
                case 'read':
                    $query->where('is_read', true);
                    break;
                case 'unreplied':
                    $query->where('is_replied', false);
                    break;
                case 'replied':
                    $query->where('is_replied', true);
                    break;
                case 'unread_unreplied':
                    $query->where('is_read', false)->where('is_replied', false);
                    break;
            }
        }
        
        // Apply type filter
        if ($request->filled('type')) {
            \Log::info('Applying type filter:', ['type' => $request->type]);
            $query->where('type', $request->type);
        }
        
        // Apply date range filter - FIX: Check for both start and end dates properly
        if ($request->filled('created_from') || $request->filled('created_to')) {
            \Log::info('Applying date filter:', [
                'created_from' => $request->created_from,
                'created_to' => $request->created_to
            ]);
            
            if ($request->filled('created_from') && $request->filled('created_to')) {
                // Both dates provided
                $query->whereBetween('created_at', [
                    $request->created_from . ' 00:00:00', 
                    $request->created_to . ' 23:59:59'
                ]);
            } elseif ($request->filled('created_from')) {
                // Only start date provided
                $query->where('created_at', '>=', $request->created_from . ' 00:00:00');
            } elseif ($request->filled('created_to')) {
                // Only end date provided
                $query->where('created_at', '<=', $request->created_to . ' 23:59:59');
            }
        }

        // Debug: Log the final query before execution
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        \Log::info('Final SQL query:', ['sql' => $sql, 'bindings' => $bindings]);
        
        // Debug: Count results before pagination
        $totalBeforePagination = $query->count();
        \Log::info('Total results before pagination:', ['count' => $totalBeforePagination]);

        // Apply sorting if requested
        if ($request->filled('sort') && $request->filled('direction')) {
            $query->orderBy($request->sort, $request->direction);
        } else {
            // Default sort by priority (unreplied first), then by latest
            $query->orderByRaw('
                CASE 
                    WHEN is_replied = 0 AND is_read = 0 THEN 1
                    WHEN is_replied = 0 AND is_read = 1 THEN 2  
                    WHEN is_replied = 1 AND is_read = 0 THEN 3
                    ELSE 4
                END ASC, created_at DESC
            ');
        }

        $messages = $query->paginate(15)->withQueryString();
        
        // Debug: Log pagination results
        \Log::info('Pagination results:', [
            'total' => $messages->total(),
            'current_page' => $messages->currentPage(),
            'per_page' => $messages->perPage(),
            'from' => $messages->firstItem(),
            'to' => $messages->lastItem()
        ]);

        // Get counts for different statuses
        $statusCounts = [
            'total' => Message::excludeAdminMessages()->count(),
            'unread' => Message::excludeAdminMessages()->unread()->count(),
            'unreplied' => Message::excludeAdminMessages()->unreplied()->count(),
            'unread_unreplied' => Message::excludeAdminMessages()->unread()->unreplied()->count(),
        ];
        
        // Debug: Log status counts
        \Log::info('Status counts:', $statusCounts);

        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = Message::excludeAdminMessages()->unread()->count();
        $pendingQuotations = \App\Models\Quotation::where('status', 'pending')->count();

        return view('admin.messages.index', compact('messages', 'statusCounts', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        // Load relationships
        $message->load(['user', 'attachments', 'parent', 'replies', 'repliedBy']);

        // Mark message as read if not already and it's not an admin message
        if (!$message->is_read && $message->type !== 'admin_to_client') {
            $message->markAsRead();
        }

        // Get related messages (thread) - only if parent_id exists in database
        $relatedMessages = collect();
        try {
            $relatedMessages = $message->getThreadMessages()
                ->where('id', '!=', $message->id)
                ->with(['attachments'])
                ->get();
        } catch (\Exception $e) {
            // If there's an issue with threading, just get empty collection
            $relatedMessages = collect();
        }

        // Get other messages from this client/email (limited to 5)
        // Exclude admin messages from this listing too
        $clientMessages = Message::where('email', $message->email)
            ->where('id', '!=', $message->id)
            ->excludeAdminMessages()
            ->latest()
            ->take(5)
            ->get();
        
        $totalClientMessages = Message::where('email', $message->email)
            ->excludeAdminMessages()
            ->count();

        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = Message::excludeAdminMessages()->unread()->count();
        $pendingQuotations = \App\Models\Quotation::where('status', 'pending')->count();

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
        // Load attachments relationship
        $message->load('attachments');

        // Delete all attachments (the model will handle file deletion)
        foreach ($message->attachments as $attachment) {
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
            'attachments.*' => 'nullable|file|max:2048|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,zip,rar', // 2MB max per file
        ]);

        // Create the reply message
        $reply = Message::create([
            'type' => 'admin_to_client',
            'name' => auth()->user()->name ?? 'Admin',
            'email' => config('mail.from.address'),
            'subject' => $request->subject,
            'message' => $request->message,
            'parent_id' => $message->id,
            'user_id' => $message->user_id,
            'is_read' => true, // Admin replies are marked as read by default
            'is_replied' => false, // Admin messages don't need reply status
            'read_at' => now(),
            'replied_by' => auth()->id(),
        ]);

        // Mark the original message as replied
        if (!$message->is_replied) {
            $message->markAsReplied(auth()->id());
        }

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    $reply->addAttachment($file);
                }
            }
        }

        // Send notification to the client if it's a registered user
        try {
            if ($message->user) {
                $message->user->notify(new MessageReplyNotification($reply));
            } else {
                // For contact form messages, send an email directly
                Notification::route('mail', $message->email)
                    ->notify(new MessageReplyNotification($reply));
            }
        } catch (\Exception $e) {
            // Log the error but don't stop the process
            \Log::error('Failed to send email notification: ' . $e->getMessage());
        }

        return redirect()->route('admin.messages.show', $message)
            ->with('success', 'Reply sent successfully!');
    }

    /**
     * Mark a batch of messages as read.
     */
    public function markAsRead(Request $request)
    {
        // Only mark messages where it makes sense (client to admin or contact form)
        // Exclude admin messages from bulk operations
        Message::excludeAdminMessages()
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
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->ids);
        $ids = array_filter($ids); // Remove empty values
        
        if (empty($ids)) {
            return redirect()->back()
                ->with('error', 'No messages selected for deletion.');
        }

        // Get messages with their attachments, but exclude admin messages from bulk delete
        $messages = Message::whereIn('id', $ids)
            ->excludeAdminMessages()
            ->with('attachments')
            ->get();
        
        // Delete attachments first
        foreach ($messages as $message) {
            foreach ($message->attachments as $attachment) {
                $attachment->delete(); // This will handle file deletion via model event
            }
        }
        
        // Delete the messages
        $count = Message::whereIn('id', $ids)
            ->excludeAdminMessages()
            ->delete();

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

    /**
     * Get dashboard statistics for messages.
     */
    public function getStats()
    {
        return [
            'total_messages' => Message::excludeAdminMessages()->count(),
            'unread_messages' => Message::excludeAdminMessages()->unread()->count(),
            'unreplied_messages' => Message::excludeAdminMessages()->unreplied()->count(),
            'today_messages' => Message::excludeAdminMessages()->whereDate('created_at', today())->count(),
            'urgent_messages' => Message::excludeAdminMessages()->unread()->unreplied()->count(),
        ];
    }
}