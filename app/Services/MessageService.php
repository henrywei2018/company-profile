<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MessageService
{
    /**
     * Get paginated messages
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedMessages(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Message::query();
        
        // Apply filters
        if (isset($filters['read'])) {
            $query->where('is_read', $filters['read'] === 'read');
        }
        
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }
        
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        // Get latest messages
        return $query->latest()->paginate($perPage);
    }
    
    /**
     * Get client messages
     *
     * @param User $client
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getClientMessages(User $client, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Message::where('client_id', $client->id);
        
        // Apply filters
        if (isset($filters['read'])) {
            $query->where('is_read_by_client', $filters['read'] === 'read');
        }
        
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }
        
        // Get latest messages
        return $query->latest()->paginate($perPage);
    }
    
    /**
     * Create a new message
     *
     * @param array $data
     * @param array $files
     * @return Message
     */
    public function createMessage(array $data, array $files = []): Message
    {
        // Create the message
        $message = Message::create($data);
        
        // Process attachments if any
        if (!empty($files)) {
            $this->processAttachments($message, $files);
        }
        
        return $message;
    }
    
    /**
     * Mark message as read
     *
     * @param Message $message
     * @param string $type
     * @return Message
     */
    public function markAsRead(Message $message, string $type = 'admin'): Message
    {
        if ($type === 'admin') {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        } else {
            $message->update([
                'is_read_by_client' => true,
                'read_by_client_at' => now(),
            ]);
        }
        
        return $message;
    }
    
    /**
     * Mark message as unread
     *
     * @param Message $message
     * @param string $type
     * @return Message
     */
    public function markAsUnread(Message $message, string $type = 'admin'): Message
    {
        if ($type === 'admin') {
            $message->update([
                'is_read' => false,
                'read_at' => null,
            ]);
        } else {
            $message->update([
                'is_read_by_client' => false,
                'read_by_client_at' => null,
            ]);
        }
        
        return $message;
    }
    
    /**
     * Delete message
     *
     * @param Message $message
     * @return bool
     */
    public function deleteMessage(Message $message): bool
    {
        // Delete attachments if any
        if ($message->attachments()->count() > 0) {
            foreach ($message->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }
        }
        
        return $message->delete();
    }
    
    /**
     * Process attachments
     *
     * @param Message $message
     * @param array $files
     * @return void
     */
    private function processAttachments(Message $message, array $files): void
    {
        foreach ($files as $file) {
            $path = $file->store('message_attachments/' . $message->id, 'public');
            
            $message->attachments()->create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
            ]);
        }
    }
}