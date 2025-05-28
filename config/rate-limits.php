<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Simple rate limits for different types of requests.
    | Format: 'name' => 'attempts,minutes'
    |
    */

    // Authentication related
    'login' => '5,1',           // 5 attempts per minute
    'register' => '3,1',        // 3 attempts per minute (stricter)
    'password_reset' => '3,1',  // 3 attempts per minute
    'email_verify' => '6,1',    // 6 attempts per minute
    
    // Public forms (anti-spam)
    'contact_form' => '10,1',   // 10 submissions per minute
    'quotation_form' => '5,1',  // 5 quotations per minute
    
    // API endpoints
    'api_public' => '60,1',     // 60 requests per minute for public API
    'api_auth' => '100,1',      // 100 requests per minute for authenticated API
    
    // Admin actions (if needed)
    'admin_bulk' => '30,1',     // 30 bulk operations per minute
];