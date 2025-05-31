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

    public function __construct(
        public ChatOperator $operator,
        public bool $isOnline
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-chat-notifications'),
            new Channel('public-chat-status'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'operator.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'operator_id' => $this->operator->id,
            'operator_name' => $this->operator->user->name,
            'is_online' => $this->isOnline,
            'total_online_operators' => \App\Models\ChatOperator::where('is_online', true)->count(),
            'timestamp' => now()->toISOString(),
        ];
    }
}