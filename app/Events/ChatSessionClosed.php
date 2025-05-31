<?php

namespace App\Events;

use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSessionClosed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ChatSession $session
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel($this->session->getChannelName()),
            new Channel('admin-chat-notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'session.closed';
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->session_id,
            'visitor_name' => $this->session->getVisitorName(),
            'ended_at' => $this->session->ended_at->toISOString(),
            'duration' => $this->session->getDuration(),
            'summary' => $this->session->summary,
        ];
    }
}