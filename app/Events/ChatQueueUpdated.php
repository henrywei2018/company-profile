<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatQueueUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $queueData;

    public function __construct(array $queueData)
    {
        $this->queueData = $queueData;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('public-chat-status'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'queue' => $this->queueData
        ];
    }

    public function broadcastAs(): string
    {
        return 'queue.updated';
    }
}