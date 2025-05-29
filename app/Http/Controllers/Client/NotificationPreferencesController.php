<?php

// File: app/Http/Controllers/Client/NotificationPreferencesController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationPreferencesController extends Controller
{
    /**
     * Show notification preferences form
     */
    public function show()
    {
        $user = auth()->user();
        
        $preferences = [
            'email_notifications' => $user->email_notifications ?? true,
            'project_updates' => $user->project_update_notifications ?? true,
            'quotation_updates' => $user->quotation_update_notifications ?? true,
            'message_replies' => $user->message_reply_notifications ?? true,
            'deadline_alerts' => $user->deadline_alert_notifications ?? true,
            'system_notifications' => $user->system_notifications ?? false,
            'marketing_emails' => $user->marketing_notifications ?? false,
            'notification_frequency' => $user->notification_frequency ?? 'immediate',
            'quiet_hours' => $user->quiet_hours ?? null,
        ];

        return view('client.notifications.preferences', compact('preferences'));
    }

    /**
     * Update notification preferences
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'project_updates' => 'boolean',
            'quotation_updates' => 'boolean',
            'message_replies' => 'boolean',
            'deadline_alerts' => 'boolean',
            'system_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'notification_frequency' => 'in:immediate,hourly,daily,weekly',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
        ]);

        $user = auth()->user();
        
        // Prepare quiet hours data
        $quietHours = null;
        if ($validated['quiet_hours_start'] && $validated['quiet_hours_end']) {
            $quietHours = [
                'start' => $validated['quiet_hours_start'],
                'end' => $validated['quiet_hours_end'],
            ];
        }

        // Update user preferences
        $user->update([
            'email_notifications' => $validated['email_notifications'] ?? false,
            'project_update_notifications' => $validated['project_updates'] ?? false,
            'quotation_update_notifications' => $validated['quotation_updates'] ?? false,
            'message_reply_notifications' => $validated['message_replies'] ?? false,
            'deadline_alert_notifications' => $validated['deadline_alerts'] ?? false,
            'system_notifications' => $validated['system_notifications'] ?? false,
            'marketing_notifications' => $validated['marketing_emails'] ?? false,
            'notification_frequency' => $validated['notification_frequency'] ?? 'immediate',
            'quiet_hours' => $quietHours,
        ]);

        return redirect()->route('client.notifications.preferences')
            ->with('success', 'Notification preferences updated successfully!');
    }
}