<?php
// app/Events/ChatMessageSent.php
namespace App\Events;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ChatMessage $message,
        public ChatSession $session
    ) {}

    public function broadcastOn(): array
    {
        $channels = [
            // Session channel for participants
            new Channel($this->session->getChannelName()),
        ];

        // Add admin channels if message is from visitor
        if ($this->message->sender_type === 'visitor') {
            $channels[] = new Channel('admin-chat-notifications');
            $channels[] = new Channel($this->session->getAdminChannelName());
            $channels[] = new Channel('chat-lobby'); // For admin dashboard updates
        }

        // Add user channel if message is from operator
        if ($this->message->sender_type === 'operator' && $this->session->user) {
            $channels[] = new Channel("user.{$this->session->user->id}");
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return array_merge(
            $this->message->toWebSocketArray(),
            [
                'session_id' => $this->session->session_id,
                'session_status' => $this->session->status,
                'visitor_name' => $this->session->getVisitorName(),
                'visitor_email' => $this->session->getVisitorEmail(),
                'operator_name' => $this->session->operator?->name,
                'chat_session_url' => route('admin.chat.show', $this->session),
                'timestamp' => now()->toISOString(),
            ]
        );
    }
}