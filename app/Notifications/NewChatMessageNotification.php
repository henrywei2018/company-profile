<?php

namespace App\Notifications;

use App\Models\ChatSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChatMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected ChatSession $session;
    protected string $message;

    public function __construct(ChatSession $session, string $message)
    {
        $this->session = $session;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Chat Message - ' . $this->session->getVisitorName())
            ->greeting('New Chat Message!')
            ->line('You have received a new chat message from ' . $this->session->getVisitorName())
            ->line('**Message:** ' . $this->message)
            ->line('**Email:** ' . ($this->session->getVisitorEmail() ?: 'Not provided'))
            ->line('**Session ID:** ' . $this->session->session_id)
            ->action('View Chat', route('admin.chat.show', $this->session))
            ->line('Please respond as soon as possible to provide excellent customer service.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_chat_message',
            'session_id' => $this->session->session_id,
            'visitor_name' => $this->session->getVisitorName(),
            'visitor_email' => $this->session->getVisitorEmail(),
            'message' => \Illuminate\Support\Str::limit($this->message, 100),
            'created_at' => now()->toISOString(),
        ];
    }
}