<?php

namespace App\Mail;

use App\Models\ChatSession;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaleChatNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public ChatSession $session;

    public function __construct(ChatSession $session)
    {
        $this->session = $session;
    }

    public function build()
    {
        return $this->subject('⏰ Stale Chat Alert - Customer Waiting')
                    ->view('emails.stale-chat-notification')
                    ->with([
                        'session' => $this->session,
                        'visitorName' => $this->session->getVisitorName(),
                        'visitorEmail' => $this->session->getVisitorEmail(),
                        'waitingTime' => $this->session->created_at->diffForHumans(),
                        'chatUrl' => route('admin.chat.show', $this->session),
                    ]);
    }
}