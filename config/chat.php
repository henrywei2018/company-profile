<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chat System Configuration
    |--------------------------------------------------------------------------
    */

    'enabled' => env('CHAT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Queue Settings
    |--------------------------------------------------------------------------
    */

    'queue' => [
        'connection' => env('CHAT_QUEUE_CONNECTION', 'redis'),
        'name' => env('CHAT_QUEUE_NAME', 'chat'),
        'auto_process' => env('CHAT_AUTO_PROCESS_QUEUE', true),
        'process_interval' => env('CHAT_QUEUE_PROCESS_INTERVAL', 30), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Settings
    |--------------------------------------------------------------------------
    */

    'session' => [
        'timeout_minutes' => env('CHAT_SESSION_TIMEOUT', 30),
        'max_waiting_time' => env('CHAT_MAX_WAITING_TIME', 60), // minutes
        'auto_assignment' => env('CHAT_AUTO_ASSIGNMENT', true),
        'rating_enabled' => env('CHAT_RATING_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Operator Settings
    |--------------------------------------------------------------------------
    */

    'operators' => [
        'max_concurrent_chats' => env('CHAT_MAX_CONCURRENT_CHATS', 5),
        'auto_away_minutes' => env('CHAT_AUTO_AWAY_MINUTES', 10),
        'notification_channels' => ['database', 'mail'],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */

    'files' => [
        'enabled' => env('CHAT_FILES_ENABLED', true),
        'max_size' => env('CHAT_MAX_FILE_SIZE', 10240), // KB
        'allowed_types' => [
            'image/jpeg', 'image/png', 'image/gif',
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ],
        'storage_disk' => env('CHAT_FILES_DISK', 'public'),
        'storage_path' => 'chat-files',
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Settings
    |--------------------------------------------------------------------------
    */

    'widget' => [
        'enabled' => env('CHAT_WIDGET_ENABLED', true),
        'theme' => env('CHAT_WIDGET_THEME', 'blue'),
        'position' => env('CHAT_WIDGET_POSITION', 'bottom-right'),
        'auto_open' => env('CHAT_WIDGET_AUTO_OPEN', false),
        'show_online_status' => env('CHAT_WIDGET_SHOW_STATUS', true),
        'enable_sound' => env('CHAT_WIDGET_SOUND', true),
        'polling_interval' => env('CHAT_WIDGET_POLLING_INTERVAL', 2000), // ms
    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcasting Settings
    |--------------------------------------------------------------------------
    */

    'broadcasting' => [
        'enabled' => env('CHAT_BROADCASTING_ENABLED', true),
        'driver' => env('BROADCAST_DRIVER', 'pusher'),
    ],
];