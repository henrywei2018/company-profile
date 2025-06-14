<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chat Queue Configuration
    |--------------------------------------------------------------------------
    */
    'queue' => [
        'auto_assign_enabled' => env('CHAT_AUTO_ASSIGN_ENABLED', true),
        'max_wait_time_minutes' => env('CHAT_MAX_WAIT_TIME', 30),
        'priority_boost_after_minutes' => env('CHAT_PRIORITY_BOOST_AFTER', 10),
        'urgent_priority_after_minutes' => env('CHAT_URGENT_PRIORITY_AFTER', 20),
        'max_concurrent_sessions_per_operator' => env('CHAT_MAX_CONCURRENT_SESSIONS', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Chat Operator Configuration
    |--------------------------------------------------------------------------
    */
    'operators' => [
        'auto_offline_after_minutes' => env('CHAT_AUTO_OFFLINE_AFTER', 15),
        'max_inactive_time_minutes' => env('CHAT_MAX_INACTIVE_TIME', 10),
        'default_availability' => env('CHAT_DEFAULT_AVAILABILITY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Chat Session Configuration
    |--------------------------------------------------------------------------
    */
    'sessions' => [
        'archive_after_days' => env('CHAT_ARCHIVE_AFTER_DAYS', 30),
        'cleanup_archived_after_days' => env('CHAT_CLEANUP_ARCHIVED_AFTER_DAYS', 90),
        'max_session_duration_hours' => env('CHAT_MAX_SESSION_DURATION', 4),
    ],

    /*
    |--------------------------------------------------------------------------
    | Chat Monitoring & Alerts
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'queue_alert_threshold' => env('CHAT_QUEUE_ALERT_THRESHOLD', 10),
        'response_time_alert_threshold' => env('CHAT_RESPONSE_TIME_ALERT', 5), // minutes
        'enable_performance_monitoring' => env('CHAT_ENABLE_MONITORING', true),
        'metrics_retention_days' => env('CHAT_METRICS_RETENTION_DAYS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Chat Widget Configuration
    |--------------------------------------------------------------------------
    */
    'widget' => [
        'polling_interval_ms' => env('CHAT_WIDGET_POLLING_INTERVAL', 1000),
        'max_polling_interval_ms' => env('CHAT_WIDGET_MAX_POLLING_INTERVAL', 5000),
        'show_queue_position' => env('CHAT_SHOW_QUEUE_POSITION', true),
        'show_estimated_wait' => env('CHAT_SHOW_ESTIMATED_WAIT', true),
    ],
];