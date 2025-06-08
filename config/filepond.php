<?php
// config/filepond.php

return [
    /*
    |--------------------------------------------------------------------------
    | FilePond Storage Disk
    |--------------------------------------------------------------------------
    |
    | Disk for storing temporary FilePond uploads
    |
    */
    'disk' => env('FILEPOND_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | FilePond Temporary Path
    |--------------------------------------------------------------------------
    |
    | Path for storing temporary uploads
    |
    */
    'path' => 'filepond/tmp',

    /*
    |--------------------------------------------------------------------------
    | FilePond Cleanup
    |--------------------------------------------------------------------------
    |
    | Whether to automatically clean up old temporary files
    |
    */
    'cleanup' => true,

    /*
    |--------------------------------------------------------------------------
    | FilePond Max File Age
    |--------------------------------------------------------------------------
    |
    | Maximum age of temporary files before cleanup (in minutes)
    |
    */
    'max_file_age' => 60, // 1 hour

    /*
    |--------------------------------------------------------------------------
    | FilePond Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size in bytes
    |
    */
    'max_file_size' => 10 * 1024 * 1024, // 10MB

    /*
    |--------------------------------------------------------------------------
    | FilePond Allowed MIME Types
    |--------------------------------------------------------------------------
    |
    | Allowed file types
    |
    */
    'allowed_mime_types' => [
        // Images
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/svg+xml',
        'image/webp',

        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'text/csv',

        // Archives
        'application/zip',
        'application/x-rar-compressed',
        'application/x-7z-compressed',

        // Other
        'application/json',
        'application/xml',
        'text/xml',
    ],
];