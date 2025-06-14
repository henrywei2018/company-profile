<?php

namespace App\Events;

use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSessionPositionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session;
    public $position;
    public $estimatedWait;

    public function __construct(ChatSession $session, int $position, int $estimatedWait)
    {
        $this->session = $session;
        $this->position = $position;
        $this->estimatedWait = $estimatedWait;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->session->getChannelName()),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'position' => $this->position,
            'estimated_wait_minutes' => $this->estimatedWait,
            'session_id' => $this->session->session_id,
        ];
    }

    public function broadcastAs(): string
    {
        return 'position.updated';
    }
}