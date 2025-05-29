<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Notification Channels
    |--------------------------------------------------------------------------
    |
    | This option defines the default channels that will be used when sending
    | notifications. You can override this on a per-notification basis.
    |
    */
    'channels' => [
        'mail',
        'database',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Queue
    |--------------------------------------------------------------------------
    |
    | Enable queueing for notifications to improve performance.
    |
    */
    'queue' => env('NOTIFICATIONS_QUEUE', true),

    /*
    |--------------------------------------------------------------------------
    | Notification Preferences
    |--------------------------------------------------------------------------
    |
    | Default notification preferences for different user types.
    | Users can override these in their profile settings.
    |
    */
    'preferences' => [
        'client' => [
            'project_updates' => true,
            'quotation_updates' => true,
            'message_replies' => true,
            'deadline_alerts' => true,
            'system_notifications' => false,
            'marketing_emails' => false,
        ],
        'admin' => [
            'project_updates' => true,
            'quotation_updates' => true,
            'message_notifications' => true,
            'chat_notifications' => true,
            'system_alerts' => true,
            'user_registrations' => true,
            'urgent_notifications' => true,
        ],
        'super-admin' => [
            'all_notifications' => true,
            'system_alerts' => true,
            'security_alerts' => true,
            'backup_notifications' => true,
            'certificate_alerts' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Automatic Notifications
    |--------------------------------------------------------------------------
    |
    | Configure which model events should trigger automatic notifications.
    |
    */
    'auto_notifications' => [
        'project' => [
            'created' => true,
            'updated' => true,
            'status_changed' => true,
            'deadline_approaching' => true,
            'completed' => true,
        ],
        'quotation' => [
            'created' => true,
            'status_updated' => true,
            'approved' => true,
            'client_response_needed' => true,
        ],
        'message' => [
            'created' => true,
            'reply' => true,
            'urgent' => true,
        ],
        'user' => [
            'welcome' => true,
            'profile_incomplete' => true,
            'email_verified' => false,
        ],
        'testimonial' => [
            'created' => true,
            'approved' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Scheduling
    |--------------------------------------------------------------------------
    |
    | Configuration for scheduled notifications and reminders.
    |
    */
    'scheduling' => [
        'project_deadline_alerts' => [1, 3, 7], // Days before deadline
        'quotation_expiry_alerts' => [5, 1], // Days before expiry
        'certificate_expiry_alerts' => [30, 7, 1], // Days before expiry
        'profile_completion_reminder' => 7, // Days after registration
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Prevent spam by limiting the number of notifications sent to users.
    |
    */
    'rate_limiting' => [
        'enabled' => true,
        'max_per_hour' => 10,
        'max_per_day' => 50,
        'urgent_exceptions' => true, // Allow urgent notifications to bypass limits
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Templates
    |--------------------------------------------------------------------------
    |
    | Custom templates for different notification types.
    |
    */
    'templates' => [
        'mail' => [
            'layout' => 'emails.layout',
            'header_color' => '#3B82F6',
            'footer_text' => 'Thank you for using our services.',
        ],
        'database' => [
            'icon_mapping' => [
                'project' => 'folder',
                'quotation' => 'document-text',
                'message' => 'mail',
                'system' => 'cog',
                'user' => 'user',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Cleanup
    |--------------------------------------------------------------------------
    |
    | Automatic cleanup of old notifications to keep the database clean.
    |
    */
    'cleanup' => [
        'enabled' => true,
        'keep_unread_days' => 90,
        'keep_read_days' => 30,
        'schedule' => 'daily',
    ],
];