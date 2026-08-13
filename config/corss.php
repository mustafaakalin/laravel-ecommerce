<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:3001',
        'https://ecommerce1.akalin.tech:443',
        'http://akalintech.test:2121',
        'https://akalintech.test:2121',
        env('FRONTEND_URL', 'http://localhost:3000')
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

    // owasp cors headers doc : https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Headers_Cheat_Sheet.html
    // mozilla cors docs : https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    // X-XSS koruma başlığı 
    'X-XSS-Protection' => '1; mode=block',

    'X-Frame-Options' => 'DENY',


    // 'allowed_headers' => [
    //     'X-Requested-With',
    //     'Content-Type',
    //     'Accept',
    //     'Authorization',
    //     'X-CSRF-TOKEN',
    //     'Origin',
    // ],

    // 'exposed_headers' => [
    //     'Cache-Control',
    //     'Content-Language',
    //     'Content-Type',
    //     'Expires',
    //     'Last-Modified',
    //     'Pragma',
    // ],

    // 'max_age' => 60 * 60 * 24, // 24 hours

    // 'supports_credentials' => true,

    // // Security headers
    // 'X-Content-Type-Options' => 'nosniff',
    // 'X-XSS-Protection' => '1; mode=block',
    // 'X-Frame-Options' => 'DENY',
    // 'Referrer-Policy' => 'strict-origin-when-cross-origin',


];
