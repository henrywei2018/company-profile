<?php
namespace App\Events;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatUserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatSession $session;
    public User $user;
    public bool $isTyping;

    public function __construct(ChatSession $session, User $user, bool $isTyping = true)
    {
        $this->session = $session;
        $this->user = $user;
        $this->isTyping = $isTyping;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat-session.' . $this->session->session_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }

    public function broadcastWith(): array
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'type' => $this->user->hasRole('admin') ? 'operator' : 'client'
            ],
            'is_typing' => $this->isTyping,
            'session_id' => $this->session->session_id
        ];
    }
}