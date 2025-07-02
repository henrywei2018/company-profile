<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event for broadcasting new messages to relevant users
 */
class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to admin channel for client messages
        if ($this->message->isFromClient()) {
            $channels[] = new Channel('admin-messages');
        }

        // Broadcast to specific client for admin replies
        if ($this->message->isFromAdmin() && $this->message->user_id) {
            $channels[] = new PrivateChannel('user.' . $this->message->user_id . '.messages');
        }

        // Broadcast to email-based notifications for non-registered users
        if ($this->message->isFromAdmin() && !$this->message->user_id && $this->message->email) {
            $channels[] = new Channel('email.' . md5($this->message->email) . '.messages');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.created';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'type' => $this->message->type,
                'subject' => $this->message->subject,
                'name' => $this->message->name,
                'email' => $this->message->email,
                'priority' => $this->message->priority,
                'created_at' => $this->message->created_at,
                'is_read' => $this->message->is_read,
                'is_replied' => $this->message->is_replied,
                'parent_id' => $this->message->parent_id,
                'user_id' => $this->message->user_id,
                'project_id' => $this->message->project_id,
                'attachments_count' => $this->message->attachments()->count(),
            ]
        ];
    }
}

/**
 * Event for broadcasting message status updates (read, replied, etc.)
 */
class MessageStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public string $status;
    public array $changes;

    public function __construct(Message $message, string $status, array $changes = [])
    {
        $this->message = $message;
        $this->status = $status;
        $this->changes = $changes;
    }

    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to admin channel
        $channels[] = new Channel('admin-messages');

        // Broadcast to specific client if they're registered
        if ($this->message->user_id) {
            $channels[] = new PrivateChannel('user.' . $this->message->user_id . '.messages');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message->id,
            'status' => $this->status,
            'changes' => $this->changes,
            'timestamp' => now()->toISOString(),
        ];
    }
}

/**
 * Event for broadcasting real-time typing indicators
 */
class MessageTypingIndicator implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public User $user;
    public bool $isTyping;

    public function __construct(Message $message, User $user, bool $isTyping)
    {
        $this->message = $message;
        $this->user = $user;
        $this->isTyping = $isTyping;
    }

    public function broadcastOn(): array
    {
        $channels = [];

        // Get all participants in the message thread
        $rootMessage = $this->message->parent_id ? $this->message->parent : $this->message;
        
        // Broadcast to admin channel
        $channels[] = new Channel('admin-messages');

        // Broadcast to client if they're registered
        if ($rootMessage->user_id) {
            $channels[] = new PrivateChannel('user.' . $rootMessage->user_id . '.messages');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.typing';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'type' => $this->user->hasRole(['admin', 'super-admin']) ? 'admin' : 'client',
            ],
            'is_typing' => $this->isTyping,
        ];
    }
}

/**
 * Event for broadcasting message thread updates
 */
class MessageThreadUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $rootMessage;
    public array $threadMessages;
    public string $action;

    public function __construct(Message $rootMessage, array $threadMessages, string $action = 'updated')
    {
        $this->rootMessage = $rootMessage;
        $this->threadMessages = $threadMessages;
        $this->action = $action;
    }

    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to admin channel
        $channels[] = new Channel('admin-messages');

        // Broadcast to specific client if they're registered
        if ($this->rootMessage->user_id) {
            $channels[] = new PrivateChannel('user.' . $this->rootMessage->user_id . '.messages');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.thread.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'root_message_id' => $this->rootMessage->id,
            'thread_messages' => $this->threadMessages,
            'action' => $this->action,
            'timestamp' => now()->toISOString(),
        ];
    }
}