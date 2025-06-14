<?php

namespace App\Events;

use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSessionStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatSession $session;

    public function __construct(ChatSession $session)
    {
        $this->session = $session;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat-session.' . $this->session->session_id),
            new Channel('admin-chat'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'session.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'session' => [
                'id' => $this->session->id,
                'session_id' => $this->session->session_id,
                'status' => $this->session->status,
                'assigned_operator' => $this->session->assignedOperator ? [
                    'id' => $this->session->assignedOperator->user->id,
                    'name' => $this->session->assignedOperator->user->name
                ] : null
            ]
        ];
    }
}