<?php

namespace App\Notifications;

use App\Models\ChatSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChatSessionClosedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected ChatSession $chatSession;
    protected ?string $summary;

    public function __construct(ChatSession $chatSession, ?string $summary = null)
    {
        $this->chatSession = $chatSession;
        $this->summary = $summary;
    }

    public function via($notifiable): array
    {
        return ['database']; // Usually only database for closed sessions
    }
    protected function getSessionDuration(): string
    {
        if (!$this->chatSession->started_at || !$this->chatSession->ended_at) {
            return 'Unknown duration';
        }
        
        $minutes = $this->chatSession->started_at->diffInMinutes($this->chatSession->ended_at);
        return $minutes . ' minutes';
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Chat Session Closed - ' . $this->chatSession->getVisitorName())
            ->greeting('Chat Session Summary')
            ->line('A chat session has been closed.')
            ->line('**Session Details:**')
            ->line('• **Visitor:** ' . $this->chatSession->getVisitorName())
            ->line('• **Email:** ' . ($this->chatSession->getVisitorEmail() ?: 'Not provided'))
            ->line('• **Duration:** ' . $this->getSessionDuration())
            ->line('• **Messages:** ' . $this->chatSession->messages()->count())
            ->line('• **Closed:** ' . now()->format('M d, Y H:i'));

        if ($this->summary) {
            $mail->line('**Session Summary:**')
                 ->line($this->summary);
        }

        return $mail
            ->action('View Chat History', route('admin.chat.show', $this->chatSession))
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
            'notification_message' => "Chat session with {$this->chatSession->getVisitorName()} has been closed",
            'created_at' => $this->message->created_at->toISOString(),
            'title' => 'New Chat Message',
            'message_preview' => 'New message from ' . $this->chatSession->getVisitorName(),
            'priority' => 'medium',
        ];
    }
}