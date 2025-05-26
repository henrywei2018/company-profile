<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyChatReport extends Mailable
{
    use Queueable, SerializesModels;

    public array $stats;

    public function __construct(array $stats)
    {
        $this->stats = $stats;
    }

    public function build()
    {
        return $this->subject('Daily Chat Report - ' . now()->format('M j, Y'))
                    ->view('emails.daily-chat-report')
                    ->with([
                        'stats' => $this->stats,
                        'date' => now()->format('F j, Y'),
                    ]);
    }
}