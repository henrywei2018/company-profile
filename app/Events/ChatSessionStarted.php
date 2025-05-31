<?php

namespace App\Events;

use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSessionStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ChatSession $session
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-chat-notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'session.started';
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->session_id,
            'visitor_name' => $this->session->visitor_name ?: 'Anonymous',
            'visitor_email' => $this->session->visitor_email,
            'status' => $this->session->status,
            'started_at' => $this->session->started_at->toISOString(),
            'url' => route('admin.chat.show', $this->session),
        ];
    }
}