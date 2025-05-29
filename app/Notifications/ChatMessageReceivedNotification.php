<?php
// File: app/Notifications/ChatMessageReceivedNotification.php

namespace App\Notifications;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChatMessageReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected ChatSession $chatSession;
    protected ChatMessage $message;

    public function __construct(ChatSession $chatSession, ChatMessage $message)
    {
        $this->chatSession = $chatSession;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Chat Message - ' . $this->chatSession->getVisitorName())
            ->greeting('New Chat Message!')
            ->line('You have received a new chat message from ' . $this->chatSession->getVisitorName())
            ->line('**Message:**')
            ->line('"' . \Illuminate\Support\Str::limit($this->message->message, 200) . '"')
            ->line('**Session Details:**')
            ->line('• **Visitor:** ' . $this->chatSession->getVisitorName())
            ->line('• **Email:** ' . ($this->chatSession->getVisitorEmail() ?: 'Not provided'))
            ->line('• **Session ID:** ' . $this->chatSession->session_id)
            ->line('• **Started:** ' . $this->chatSession->started_at->format('M d, Y H:i'))
            ->action('Join Chat', route('admin.chat.show', $this->chatSession))
            ->line('Please respond promptly to provide excellent customer service.')
            ->salutation('Best regards,<br>' . config('app.name') . ' System');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'chat_message_received',
            'chat_session_id' => $this->chatSession->id,
            'session_id' => $this->chatSession->session_id,
            'message_id' => $this->message->id,
            'visitor_name' => $this->chatSession->getVisitorName(),
            'visitor_email' => $this->chatSession->getVisitorEmail(),
            'message' => \Illuminate\Support\Str::limit($this->message->message, 100),
            'created_at' => $this->message->created_at->toISOString(),
            'title' => 'New Chat Message',
            'message_preview' => 'New message from ' . $this->chatSession->getVisitorName(),
            'priority' => 'medium',
        ];
    }
}