<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatOperatorStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $operator;
    public $totalOnlineOperators;

    public function __construct(User $operator, int $totalOnlineOperators)
    {
        $this->operator = $operator;
        $this->totalOnlineOperators = $totalOnlineOperators;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('public-chat-status'), // Public channel untuk widget
            new PrivateChannel('admin-chat-notifications'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'operator' => [
                'id' => $this->operator->id,
                'name' => $this->operator->name,
                'is_online' => $this->operator->chatOperator?->is_online ?? false,
                'is_available' => $this->operator->chatOperator?->is_available ?? false,
            ],
            'total_online_operators' => $this->totalOnlineOperators,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'operator.status.changed';
    }
}