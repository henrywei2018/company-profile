<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class OtpVerificationMail extends Mailable
{
        

    public $user;
    public $otpCode;
    public $expiresInMinutes;

    public function __construct(User $user, string $otpCode, int $expiresInMinutes = 10)
    {
        $this->user = $user;
        $this->otpCode = $otpCode;
        $this->expiresInMinutes = $expiresInMinutes;
        
        // Set higher priority to help avoid spam
        $this->priority = 1;
    }

    public function build()
    {
        return $this->subject('Welcome! Please verify your email - ' . config('app.name'))
                    ->view('emails.otp-verification')
                    ->text('emails.otp-verification-text') // Add plain text version
                    ->with([
                        'userName' => $this->user->name,
                        'userEmail' => $this->user->email,
                        'otpCode' => $this->otpCode,
                        'expiresInMinutes' => $this->expiresInMinutes,
                        'companyName' => config('app.name'),
                        'supportEmail' => config('mail.from.address'),
                        'appUrl' => config('app.url'),
                        'currentYear' => date('Y'),
                    ])
                    // Add headers to improve deliverability
                    ->withSwiftMessage(function ($message) {
                        $message->getHeaders()
                            ->addTextHeader('X-Priority', '1')
                            ->addTextHeader('X-MSMail-Priority', 'High')
                            ->addTextHeader('Importance', 'High')
                            ->addTextHeader('X-Mailer', config('app.name'))
                            ->addTextHeader('List-Unsubscribe', '<mailto:' . config('mail.from.address') . '>')
                            ->addTextHeader('Precedence', 'bulk')
                            ->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, NDR, RN, NRN');
                    });
    }
}