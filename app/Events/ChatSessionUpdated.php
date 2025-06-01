<?php
// app/Events/ChatSessionUpdated.php
namespace App\Events;

use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSessionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ChatSession $session
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel($this->session->getChannelName()),
            new Channel($this->session->getAdminChannelName()),
            new Channel('admin-chat-notifications'),
            new Channel('chat-lobby'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'session.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->session_id,
            'visitor_name' => $this->session->getVisitorName(),
            'visitor_email' => $this->session->getVisitorEmail(),
            'status' => $this->session->status,
            'priority' => $this->session->priority,
            'assigned_operator_id' => $this->session->assigned_operator_id,
            'operator_name' => $this->session->operator?->name,
            'last_activity_at' => $this->session->last_activity_at->toISOString(),
            'updated_at' => $this->session->updated_at->toISOString(),
            'url' => route('admin.chat.show', $this->session),
        ];
    }
}