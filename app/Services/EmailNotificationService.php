<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailable;

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
     * Queue email for later sending using closure-based approach
     */
    public function queue(string $to, string $subject, string $template, array $data = []): bool
    {
        try {
            $mailable = new class($subject, $template, $data) extends Mailable {
                protected $subjectText;
                protected $template;
                protected $dataArr;

                public function __construct($subjectText, $template, $dataArr)
                {
                    $this->subjectText = $subjectText;
                    $this->template = $template;
                    $this->dataArr = $dataArr;
                }

                public function build()
                {
                    return $this->subject($this->subjectText)
                                ->view($this->template, $this->dataArr)
                                ->from(config('mail.from.address'), config('mail.from.name'));
                }
            };

            Mail::to($to)->queue($mailable);

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
    public function sendMailable(string $to, Mailable $mailable): bool
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
    public function queueMailable(string $to, Mailable $mailable): bool
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
     * Send email with delay (queue with delay)
     */
    public function sendLater(string $to, Mailable $mailable, \Carbon\Carbon $when): bool
    {
        try {
            Mail::to($to)->later($when, $mailable);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to schedule email notification: ' . $e->getMessage(), [
                'to' => $to,
                'mailable' => get_class($mailable),
                'scheduled_for' => $when->toISOString()
            ]);
            return false;
        }
    }

    /**
     * Send notification to multiple recipients using Mailable
     */
    public function sendBulkMailable(array $recipients, Mailable $mailable): array
    {
        $results = [];
        
        foreach ($recipients as $recipient) {
            $results[$recipient] = $this->sendMailable($recipient, $mailable);
        }
        
        return $results;
    }

    /**
     * Queue notification to multiple recipients using Mailable
     */
    public function queueBulkMailable(array $recipients, Mailable $mailable): array
    {
        $results = [];
        
        foreach ($recipients as $recipient) {
            $results[$recipient] = $this->queueMailable($recipient, $mailable);
        }
        
        return $results;
    }

    /**
     * Test email configuration with improved error handling
     */
    public function testConfiguration(string $testEmail = null): array
    {
        $testEmail = $testEmail ?? config('mail.from.address');
        
        try {
            $timestamp = now()->format('Y-m-d H:i:s');
            
            $success = $this->send(
                $testEmail,
                'Test Email Configuration - ' . config('app.name'),
                'emails.test',
                ['timestamp' => $timestamp]
            );
            
            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Test email sent successfully',
                    'sent_to' => $testEmail,
                    'timestamp' => $timestamp
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to send test email',
                    'error' => 'Check application logs for details'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification with custom headers
     */
    public function sendWithHeaders(string $to, string $subject, string $template, array $data = [], array $headers = []): bool
    {
        try {
            Mail::send($template, $data, function ($message) use ($to, $subject, $headers) {
                $message->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
                        
                // Add custom headers
                foreach ($headers as $key => $value) {
                    $message->getHeaders()->addTextHeader($key, $value);
                }
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email with custom headers: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'template' => $template,
                'headers' => $headers
            ]);
            
            return false;
        }
    }

    /**
     * Send notification with attachments
     */
    public function sendWithAttachments(string $to, string $subject, string $template, array $data = [], array $attachments = []): bool
    {
        try {
            Mail::send($template, $data, function ($message) use ($to, $subject, $attachments) {
                $message->to($to)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
                        
                // Add attachments
                foreach ($attachments as $attachment) {
                    if (is_string($attachment)) {
                        $message->attach($attachment);
                    } elseif (is_array($attachment)) {
                        $message->attach(
                            $attachment['path'],
                            $attachment['options'] ?? []
                        );
                    }
                }
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email with attachments: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'template' => $template,
                'attachments_count' => count($attachments)
            ]);
            
            return false;
        }
    }

    /**
     * Check if email service is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty(config('mail.from.address')) && 
               !empty(config('mail.from.name')) &&
               !empty(config('mail.default'));
    }

    /**
     * Get email configuration status
     */
    public function getConfigurationStatus(): array
    {
        return [
            'is_configured' => $this->isConfigured(),
            'driver' => config('mail.default'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'encryption' => config('mail.mailers.' . config('mail.default') . '.encryption'),
            'host' => config('mail.mailers.' . config('mail.default') . '.host'),
            'port' => config('mail.mailers.' . config('mail.default') . '.port'),
        ];
    }

    /**
     * Get email sending statistics (if logging is enabled)
     */
    public function getStatistics(): array
    {
        // This is a basic implementation
        // You might want to implement more sophisticated tracking
        return [
            'total_sent_today' => 0, // Implement based on your logging strategy
            'total_failed_today' => 0,
            'queue_size' => 0, // Implement if using database queue
            'last_sent' => null,
        ];
    }
}