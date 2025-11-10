<?php

/**
 * Rate Limiting Configuration
 * 
 * Throttle format: throttle:requests,minutes
 * Example: throttle:5,1 = 5 requests per 1 minute
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Login Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Limit login attempts to prevent brute force attacks
    | 5 attempts per minute per IP address
    |
    */
    'login' => '5,1',

    /*
    |--------------------------------------------------------------------------
    | General API Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Standard rate limit for general API routes
    | 60 requests per minute per IP address
    |
    */
    'api' => '60,1',

    /*
    |--------------------------------------------------------------------------
    | Strict Rate Limiting (for sensitive operations)
    |--------------------------------------------------------------------------
    |
    | Very strict limit for password reset, registration, etc.
    | 3 attempts per 15 minutes per IP address
    |
    */
    'strict' => '3,15',
];
