<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    /**
     * Send email notification
     */
    public function send(string $to, string $subject, string $template, array $data = []): bool
    {
        try {
            Mail::send($template, $data, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email notification: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ]);
            
            return false;
        }
    }

    /**
     * Send bulk email notifications
     */
    public function sendBulk(array $recipients, string $subject, string $template, array $data = []): array
    {
        $results = [];
        
        foreach ($recipients as $recipient) {
            $results[$recipient] = $this->send($recipient, $subject, $template, $data);
        }
        
        return $results;
    }

    /**
     * Queue email for later sending
     */
    public function queue(string $to, string $subject, string $template, array $data = []): bool
    {
        try {
            Mail::queue($template, $data, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to queue email notification: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ]);
            
            return false;
        }
    }

    /**
     * Send templated email with Laravel's Mailable
     */
    public function sendMailable(string $to, $mailable): bool
    {
        try {
            Mail::to($to)->send($mailable);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send mailable notification: ' . $e->getMessage(), [
                'to' => $to,
                'mailable' => get_class($mailable)
            ]);
            return false;
        }
    }

    /**
     * Queue mailable for later sending
     */
    public function queueMailable(string $to, $mailable): bool
    {
        try {
            Mail::to($to)->queue($mailable);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to queue mailable notification: ' . $e->getMessage(), [
                'to' => $to,
                'mailable' => get_class($mailable)
            ]);
            return false;
        }
    }

    /**
     * Test email configuration
     */
    public function testConfiguration(string $testEmail = null): array
    {
        $testEmail = $testEmail ?? config('mail.from.address');
        
        try {
            $this->send(
                $testEmail,
                'Test Email Configuration',
                'emails.test',
                ['timestamp' => now()->format('Y-m-d H:i:s')]
            );
            
            return [
                'success' => true,
                'message' => 'Test email sent successfully',
                'sent_to' => $testEmail
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }
}