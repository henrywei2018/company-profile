<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecurityAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $alertDetails;
    protected string $alertType;
    protected string $severity;

    public function __construct(array $alertDetails, string $alertType = 'general', string $severity = 'medium')
    {
        $this->alertDetails = $alertDetails;
        $this->alertType = $alertType;
        $this->severity = $severity;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mailMethod = match($this->severity) {
            'critical', 'high' => 'error',
            'medium' => 'warning',
            default => 'info',
        };

        $mail = (new MailMessage)
            ->subject('Security Alert: ' . ucfirst($this->alertType))
            ->{$mailMethod}()
            ->greeting('Security Alert!')
            ->line('🚨 A security event has been detected on your system.')
            ->line('**Alert Details:**')
            ->line('• **Type:** ' . ucfirst(str_replace('_', ' ', $this->alertType)))
            ->line('• **Severity:** ' . ucfirst($this->severity))
            ->line('• **Detected:** ' . now()->format('M d, Y H:i'))
            ->line('• **Source IP:** ' . ($this->alertDetails['ip'] ?? 'Unknown'));

        if (isset($this->alertDetails['description'])) {
            $mail->line('**Description:**')
                 ->line($this->alertDetails['description']);
        }

        if (isset($this->alertDetails['user_id'])) {
            $mail->line('**Affected User:** ' . ($this->alertDetails['user_name'] ?? 'User ID: ' . $this->alertDetails['user_id']));
        }

        $mail->line('**Recommended Actions:**')
             ->line('• Review system logs immediately')
             ->line('• Verify user account security')
             ->line('• Check for any unauthorized changes')
             ->line('• Update security measures if necessary');

        if ($this->severity === 'critical' || $this->severity === 'high') {
            $mail->line('⚠️ **This is a high-priority security alert that requires immediate attention.**');
        }

        return $mail
            ->action('View Security Logs', route('admin.dashboard'))
            ->line('Take immediate action to ensure system security.')
            ->salutation('Security Team,<br>' . config('app.name') . ' System');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'security_alert',
            'alert_type' => $this->alertType,
            'severity' => $this->severity,
            'detected_at' => now()->toISOString(),
            'source_ip' => $this->alertDetails['ip'] ?? 'Unknown',
            'user_id' => $this->alertDetails['user_id'] ?? null,
            'user_name' => $this->alertDetails['user_name'] ?? null,
            'description' => $this->alertDetails['description'] ?? '',
            'details' => $this->alertDetails,
            'title' => 'Security Alert',
            'message' => ucfirst(str_replace('_', ' ', $this->alertType)) . ' security event detected',
            'priority' => $this->severity === 'critical' ? 'critical' : ($this->severity === 'high' ? 'high' : 'medium'),
        ];
    }
}