<?php

namespace App\Events;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSessionAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session;
    public $operator;

    public function __construct(ChatSession $session, User $operator)
    {
        $this->session = $session;
        $this->operator = $operator;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->session->getChannelName()),
            new PrivateChannel('admin-chat-notifications'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'session' => [
                'id' => $this->session->id,
                'session_id' => $this->session->session_id,
                'status' => $this->session->status,
            ],
            'operator' => [
                'id' => $this->operator->id,
                'name' => $this->operator->name,
            ],
            'assigned_at' => now()->toISOString(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'session.assigned';
    }
}