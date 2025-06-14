<?php

namespace App\Events;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatTypingIndicator implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session;
    public $user;
    public $isTyping;

    public function __construct(ChatSession $session, User $user, bool $isTyping)
    {
        $this->session = $session;
        $this->user = $user;
        $this->isTyping = $isTyping;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->session->getChannelName()),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'type' => $this->user->hasRole(['admin', 'super-admin']) ? 'operator' : 'visitor',
            ],
            'is_typing' => $this->isTyping,
            'session_id' => $this->session->session_id,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'typing.indicator';
    }
}