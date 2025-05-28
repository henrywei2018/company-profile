<?php

namespace App\Helpers;

class RateLimit
{
    /**
     * Get rate limit configuration
     */
    public static function get(string $key): string
    {
        $limits = config('rate-limits', []);
        
        return $limits[$key] ?? '60,1'; // Default: 60 per minute
    }
    
    /**
     * Common rate limits as constants for easy use
     */
    const LOGIN = '5,1';
    const REGISTER = '3,1';
    const PASSWORD_RESET = '3,1';
    const EMAIL_VERIFY = '6,1';
    const CONTACT_FORM = '10,1';
    const QUOTATION_FORM = '5,1';
    const API_PUBLIC = '60,1';
    const API_AUTH = '100,1';
    
    /**
     * Apply rate limit to route
     */
    public static function apply(string $type): string
    {
        return "throttle:" . constant("self::" . strtoupper($type));
    }
}   