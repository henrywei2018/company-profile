<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Mail\OtpVerificationMail;

class OtpVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $otpCode;

    public function __construct($otpCode)
    {
        $this->otpCode = $otpCode;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return new OtpVerificationMail($notifiable, $this->otpCode);
    }
}