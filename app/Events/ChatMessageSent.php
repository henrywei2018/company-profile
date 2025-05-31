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
        return [
            // Channel untuk session ini (client + admin yang handle session ini)
            new Channel("chat-session.{$this->session->session_id}"),
            // Channel untuk semua admin (notification baru)
            new Channel('admin-chat-notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'sender_type' => $this->message->sender_type,
            'sender_name' => $this->message->sender_name ?: 'Anonymous',
            'session_id' => $this->session->session_id,
            'session_status' => $this->session->status,
            'timestamp' => $this->message->created_at->toISOString(),
            'formatted_time' => $this->message->created_at->format('H:i'),
            'visitor_name' => $this->session->visitor_name,
            'visitor_email' => $this->session->visitor_email,
        ];
    }
}