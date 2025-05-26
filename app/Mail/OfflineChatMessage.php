<?php

namespace App\Mail;

use App\Models\ChatSession;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OfflineChatMessage extends Mailable
{
    use Queueable, SerializesModels;

    public ChatSession $session;
    public string $message;

    public function __construct(ChatSession $session, string $message)
    {
        $this->session = $session;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject('Offline Chat Message - ' . $this->session->getVisitorName())
                    ->view('emails.offline-chat-message')
                    ->with([
                        'session' => $this->session,
                        'visitorMessage' => $this->message,
                        'visitorName' => $this->session->getVisitorName(),
                        'visitorEmail' => $this->session->getVisitorEmail(),
                        'sessionUrl' => route('admin.chat.show', $this->session),
                    ]);
    }
}