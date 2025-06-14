<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $session;

    public function __construct(ChatMessage $message, ChatSession $session)
    {
        $this->message = $message;
        $this->session = $session;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // 1. Session channel (for chat participants)
        $channels[] = new PrivateChannel($this->session->getChannelName());

        // 2. Admin notifications channel (if message from visitor)
        if ($this->message->sender_type === 'visitor') {
            $channels[] = new PrivateChannel('admin-chat-notifications');
        }

        // 3. User channel (if message from operator to authenticated user)
        if ($this->message->sender_type === 'operator' && $this->session->user) {
            $channels[] = new PrivateChannel("user.{$this->session->user->id}");
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => $this->message->toWebSocketArray(),
            'session' => [
                'id' => $this->session->id,
                'session_id' => $this->session->session_id,
                'status' => $this->session->status,
                'visitor_name' => $this->session->getVisitorName(),
            ]
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}