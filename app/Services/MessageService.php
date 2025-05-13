<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use App\Notifications\NewReplyNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class MessageService
{
    /**
     * Create a new message
     *
     * @param array $data
     * @return Message
     */
    public function createMessage(array $data)
    {
        // Create the message
        $message = Message::create($data);
        
        // Process attachments if any
        if (isset($data['attachments']) && !empty($data['attachments'])) {
            $this->processAttachments($message, $data['attachments']);
        }
        
        // Send notifications
        $this->sendNotifications($message);
        
        return $message;
    }
    
    /**
     * Reply to a message
     *
     * @param Message $parentMessage
     * @param array $data
     * @return Message
     */
    public function replyToMessage(Message $parentMessage, array $data)
    {
        // Set parent ID
        $data['parent_id'] = $parentMessage->id;
        
        // Determine message type
        if ($data['type'] ?? null) {
            // Type is already set
        } elseif (isset($data['client_id']) && $data['client_id']) {
            $data['type'] = 'client_to_admin';
            $data['is_read_by_client'] = true;
            $data['is_read'] = false;
        } else {
            $data['type'] = 'admin_to_client';
            $data['is_read'] = true;
            $data['is_read_by_client'] = false;
        }
        
        // Create the reply
        $reply = Message::create($data);
        
        // Process attachments if any
        if (isset($data['attachments']) && !empty($data['attachments'])) {
            $this->processAttachments($reply, $data['attachments']);
        }
        
        // Send notifications
        $this->sendReplyNotifications($parentMessage, $reply);
        
        return $reply;
    }
    
    /**
     * Process attachments for a message
     *
     * @param Message $message
     * @param array $attachments
     * @return void
     */
    protected function processAttachments(Message $message, array $attachments)
    {
        foreach ($attachments as $file) {
            $path = $file->store('message_attachments/' . $message->id, 'public');
            
            $message->attachments()->create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
            ]);
        }
    }
    
    /**
     * Send notifications for a new message
     *
     * @param Message $message
     * @return void
     */
    protected function sendNotifications(Message $message)
    {
        // Contact form or client message to admin - notify admins
        if (in_array($message->type, ['contact_form', 'client_to_admin'])) {
            $admins = User::role('admin')->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new NewMessageNotification($message));
            }
        } 
        // Admin message to client - notify client
        elseif ($message->type === 'admin_to_client' && $message->client_id) {
            $client = User::find($message->client_id);
            
            if ($client) {
                $client->notify(new NewMessageNotification($message));
            }
        }
    }
    
    /**
     * Send notifications for a reply to a message
     *
     * @param Message $parentMessage
     * @param Message $reply
     * @return void
     */
    protected function sendReplyNotifications(Message $parentMessage, Message $reply)
    {
        // Client reply to admin - notify admins
        if ($reply->type === 'client_to_admin') {
            $admins = User::role('admin')->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new NewReplyNotification($parentMessage, $reply));
            }
        } 
        // Admin reply to client - notify client
        elseif ($reply->type === 'admin_to_client' && $parentMessage->client_id) {
            $client = User::find($parentMessage->client_id);
            
            if ($client) {
                $client->notify(new NewReplyNotification($parentMessage, $reply));
            }
        }
    }
    
    /**
     * Delete a message and its attachments
     *
     * @param Message $message
     * @return bool
     */
    public function deleteMessage(Message $message)
    {
        // Delete attachments if any
        foreach ($message->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }
        
        // Delete replies if any
        foreach ($message->replies as $reply) {
            $this->deleteMessage($reply);
        }
        
        // Delete the message
        return $message->delete();
    }
    
    /**
     * Mark a message as read
     *
     * @param Message $message
     * @param string $type
     * @return Message
     */
    public function markAsRead(Message $message, $type = 'admin')
    {
        if ($type === 'admin') {
            $message->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        } else {
            $message->update([
                'is_read_by_client' => true,
            ]);
        }
        
        return $message;
    }
    
    /**
     * Mark a message as unread
     *
     * @param Message $message
     * @param string $type
     * @return Message
     */
    public function markAsUnread(Message $message, $type = 'admin')
    {
        if ($type === 'admin') {
            $message->update([
                'is_read' => false,
                'read_at' => null,
            ]);
        } else {
            $message->update([
                'is_read_by_client' => false,
            ]);
        }
        
        return $message;
    }
}