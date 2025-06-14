<?php

namespace App\Events;

use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSessionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session;

    public function __construct(ChatSession $session)
    {
        $this->session = $session;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->session->getChannelName()),
            new PrivateChannel('admin-chat-notifications'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'session' => [
                'id' => $this->session->id,
                'session_id' => $this->session->session_id,
                'status' => $this->session->status,
                'assigned_operator_id' => $this->session->assigned_operator_id,
                'operator' => $this->session->operator ? [
                    'id' => $this->session->operator->id,
                    'name' => $this->session->operator->name,
                ] : null,
            ]
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'session.updated';
    }
}