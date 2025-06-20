<?php

return [
    /*
     * The property id of which you want to display data.
     */
    'property_id' => env('ANALYTICS_PROPERTY_ID'),

    /*
     * Path to the client secret json file.
     */
    'service_account_credentials_json' => storage_path('app/analytics/service-account-credentials.json'),

    /*
     * The amount of minutes the Google API responses will be cached.
     */
    'cache_lifetime_in_minutes' => 60 * 24,

    /*
     * Cache configuration
     */
    'cache' => [
        'store' => 'file',
    ],
];