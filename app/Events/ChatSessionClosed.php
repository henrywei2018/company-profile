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

class ChatSessionClosed implements ShouldBroadcast
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
            // Notify user if authenticated
            ...(
                $this->session->user 
                ? [new PrivateChannel("user.{$this->session->user->id}")]
                : []
            )
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
                'ended_at' => $this->session->ended_at?->toISOString(),
                'close_reason' => $this->session->close_reason,
            ]
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'session.closed';
    }
}