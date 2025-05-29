<?php

namespace App\Notifications;

use App\Models\ChatSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChatSessionInactiveNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected ChatSession $chatSession;
    protected int $inactiveMinutes;

    public function __construct(ChatSession $chatSession, int $inactiveMinutes = null)
    {
        $this->chatSession = $chatSession;
        $this->inactiveMinutes = $inactiveMinutes ?? now()->diffInMinutes($this->chatSession->last_activity_at);
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Chat Session Inactive - Action Needed')
            ->greeting('Chat Session Alert!')
            ->line('A chat session has been inactive for ' . $this->inactiveMinutes . ' minutes.')
            ->line('**Session Details:**')
            ->line('• **Visitor:** ' . $this->chatSession->getVisitorName())
            ->line('• **Email:** ' . ($this->chatSession->getVisitorEmail() ?: 'Not provided'))
            ->line('• **Session ID:** ' . $this->chatSession->session_id)
            ->line('• **Last Activity:** ' . $this->chatSession->last_activity_at->format('M d, Y H:i'))
            ->line('• **Inactive Duration:** ' . $this->inactiveMinutes . ' minutes')
            ->line('**Recommended Actions:**')
            ->line('• Check if the visitor is still online')
            ->line('• Send a follow-up message')
            ->line('• Consider closing the session if no response')
            ->action('View Chat Session', route('admin.chat.show', $this->chatSession))
            ->line('Maintain good customer service by addressing inactive sessions.')
            ->salutation('Best regards,<br>' . config('app.name') . ' System');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'chat_session_inactive',
            'chat_session_id' => $this->chatSession->id,
            'session_id' => $this->chatSession->session_id,
            'visitor_name' => $this->chatSession->getVisitorName(),
            'visitor_email' => $this->chatSession->getVisitorEmail(),
            'inactive_minutes' => $this->inactiveMinutes,
            'last_activity' => $this->chatSession->last_activity_at->toISOString(),
            'title' => 'Chat Session Inactive',
            'message' => "Chat with {$this->chatSession->getVisitorName()} inactive for {$this->inactiveMinutes} minutes",
            'priority' => 'medium',
        ];
    }
}