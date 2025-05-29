<?php
// File: app/Notifications/BackupCompletedNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackupCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $backupDetails;

    public function __construct(array $backupDetails)
    {
        $this->backupDetails = $backupDetails;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $size = $this->backupDetails['size'] ?? 'Unknown';
        $duration = $this->backupDetails['duration'] ?? 'Unknown';
        $type = $this->backupDetails['type'] ?? 'Full';

        return (new MailMessage)
            ->subject('System Backup Completed Successfully')
            ->success()
            ->greeting('System Administrator')
            ->line('✅ System backup has been completed successfully.')
            ->line('**Backup Details:**')
            ->line('• **Type:** ' . $type . ' Backup')
            ->line('• **Completed:** ' . now()->format('M d, Y H:i'))
            ->line('• **Size:** ' . $size)
            ->line('• **Duration:** ' . $duration)
            ->line('• **Status:** Completed Successfully')
            ->line('**Backup Contents:**')
            ->line('• Database backup')
            ->line('• Application files')
            ->line('• User uploaded files')
            ->line('• Configuration files')
            ->line('The backup has been stored securely and is ready for disaster recovery if needed.')
            ->action('View System Status', route('admin.dashboard'))
            ->line('Regular backups ensure data safety and business continuity.')
            ->salutation('System Alert,<br>' . config('app.name') . ' System');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'backup_completed',
            'backup_type' => $this->backupDetails['type'] ?? 'Full',
            'backup_size' => $this->backupDetails['size'] ?? 'Unknown',
            'backup_duration' => $this->backupDetails['duration'] ?? 'Unknown',
            'completed_at' => now()->toISOString(),
            'status' => 'success',
            'location' => $this->backupDetails['location'] ?? 'Default storage',
            'title' => 'Backup Completed',
            'message' => 'System backup completed successfully',
            'priority' => 'low',
        ];
    }
}