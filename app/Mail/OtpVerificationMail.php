<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class OtpVerificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $otpCode;
    public $expiresInMinutes;

    public function __construct(User $user, string $otpCode, int $expiresInMinutes = 10)
    {
        $this->user = $user;
        $this->otpCode = $otpCode;
        $this->expiresInMinutes = $expiresInMinutes;
        
        // Debug: Log email creation
        \Log::info('Creating OTP email for ' . $user->email . ' with code: ' . $otpCode);
    }

    public function build()
    {
        return $this->subject('Verify Your Email - ' . config('app.name'))
                    ->view('emails.otp-verification')
                    ->with([
                        'userName' => $this->user->name,
                        'userEmail' => $this->user->email,
                        'otpCode' => $this->otpCode, // IMPORTANT: Make sure this is passed
                        'expiresInMinutes' => $this->expiresInMinutes,
                        'companyName' => config('app.name'),
                        'supportEmail' => config('mail.from.address'),
                        'appUrl' => config('app.url'),
                        'currentYear' => date('Y'),
                    ]);
    }
}