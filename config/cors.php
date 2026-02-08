<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin request sharing
    | or "CORS". This determines what cross-origin requests are allowed
    | to execute in a web browser. You are free to adjust these settings
    | as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',  // Vite dev server
        'http://localhost:3000',  // Alternative React dev port
        'http://localhost:8080',  // Alternative dev port
        // Add production domains here
        // 'https://yourdomain.com',
    ],

    'allowed_origins_patterns' => [
        // Uncomment for pattern-based origins
        // '#^https://.*\.yourdomain\.com$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
