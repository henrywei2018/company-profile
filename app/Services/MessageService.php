<?php
// File: app/Services/MessageService.php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class MessageService
{
    /**
     * Get messages for a client with filtering.
     */
    public function getClientMessages(User $user, array $filters = [], int $perPage = 15)
    {
        $query = Message::query();
        
        // Apply client access control - messages table uses user_id
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('user_id', $user->id);
        }
        
        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('subject', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('message', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('name', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        if (isset($filters['read']) && $filters['read'] !== '') {
            $isRead = $filters['read'] === 'read' || $filters['read'] === '1';
            $query->where('is_read', $isRead);
        }
        
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        return $query->with(['attachments', 'project', 'parent'])
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }
    
    /**
     * Create a new message with attachments.
     */
    public function createMessage(array $messageData, array $attachments = []): Message
    {
        // Create the message
        $message = Message::create($messageData);
        
        // Handle attachments if provided
        if (!empty($attachments)) {
            $this->handleAttachments($message, $attachments);
        }
        
        // Load relationships for return
        $message->load(['attachments', 'project']);
        
        return $message;
    }
    
    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message, string $readBy = 'client'): void
    {
        if (!$message->is_read) {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
            
            // If marking as read by client, you might want to track this separately
            if ($readBy === 'client') {
                // Could add client-specific read tracking if needed
            }
        }
    }
    
    /**
     * Mark a message as unread.
     */
    public function markAsUnread(Message $message, string $readBy = 'client'): void
    {
        if ($message->is_read) {
            $message->update([
                'is_read' => false,
                'read_at' => null,
            ]);
        }
    }
    
    /**
     * Handle file attachments for a message.
     */
    protected function handleAttachments(Message $message, array $attachments): void
    {
        foreach ($attachments as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $this->storeAttachment($message, $file);
            }
        }
    }
    
    /**
     * Store a single attachment.
     */
    protected function storeAttachment(Message $message, UploadedFile $file): void
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Store file
        $path = $file->storeAs('message_attachments/' . $message->id, $filename, 'public');
        
        // Create attachment record
        $message->attachments()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }
    
    /**
     * Get message thread (conversation).
     */
    public function getMessageThread(Message $message): \Illuminate\Database\Eloquent\Collection
    {
        $rootMessage = $message->getRootMessage();
        
        return Message::where(function($query) use ($rootMessage) {
            $query->where('id', $rootMessage->id)
                  ->orWhere('parent_id', $rootMessage->id);
        })
        ->with(['attachments'])
        ->orderBy('created_at')
        ->get();
    }
    
    /**
     * Create a reply to an existing message.
     */
    public function createReply(Message $originalMessage, array $replyData, array $attachments = []): Message
    {
        // Ensure the reply is linked to the root message
        $rootMessage = $originalMessage->getRootMessage();
        
        $replyData['parent_id'] = $rootMessage->id;
        $replyData['project_id'] = $originalMessage->project_id; // Inherit project if exists
        
        // Create the reply
        $reply = $this->createMessage($replyData, $attachments);
        
        // Mark original message as replied if it's from admin to client
        if ($originalMessage->type === 'admin_to_client' || $originalMessage->type === 'support_response') {
            $originalMessage->update([
                'is_replied' => true,
                'replied_at' => now(),
            ]);
        }
        
        return $reply;
    }
    
    /**
     * Get unread message count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        $query = Message::query();
        
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('user_id', $user->id);
        }
        
        return $query->where('is_read', false)->count();
    }
    
    /**
     * Bulk mark messages as read.
     */
    public function bulkMarkAsRead(array $messageIds, User $user): int
    {
        $query = Message::whereIn('id', $messageIds);
        
        // Apply access control
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('user_id', $user->id);
        }
        
        return $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
    
    /**
     * Delete a message and its attachments.
     */
    public function deleteMessage(Message $message): bool
    {
        // Delete attachments from storage
        foreach ($message->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }
        
        // Delete the message (attachments will be deleted via model relationship)
        return $message->delete();
    }
    
    /**
     * Get message statistics for a user.
     */
    public function getMessageStatistics(User $user): array
    {
        $query = Message::query();
        
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('user_id', $user->id);
        }
        
        return [
            'total' => (clone $query)->count(),
            'unread' => (clone $query)->where('is_read', false)->count(),
            'replied' => (clone $query)->where('is_replied', true)->count(),
            'this_week' => (clone $query)->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => (clone $query)->whereMonth('created_at', now()->month)->count(),
            'by_type' => (clone $query)
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }
}