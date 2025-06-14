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

class ChatSessionStarted implements ShouldBroadcast
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
            new PrivateChannel('admin-chat-notifications'),
            new PrivateChannel($this->session->getChannelName()),
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
                'visitor_name' => $this->session->getVisitorName(),
                'visitor_email' => $this->session->getVisitorEmail(),
                'status' => $this->session->status,
                'priority' => $this->session->priority,
                'started_at' => $this->session->started_at->toISOString(),
            ]
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'session.started';
    }
}