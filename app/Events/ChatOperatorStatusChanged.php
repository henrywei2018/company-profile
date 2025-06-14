<?php

namespace App\Events;

use App\Models\ChatOperator;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatOperatorStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatOperator $operator;

    public function __construct(ChatOperator $operator)
    {
        $this->operator = $operator;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-chat'),
            new Channel('chat-operators'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'operator.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'operator' => [
                'id' => $this->operator->user->id,
                'name' => $this->operator->user->name,
                'is_online' => $this->operator->is_online,
                'is_available' => $this->operator->is_available
            ]
        ];
    }
}