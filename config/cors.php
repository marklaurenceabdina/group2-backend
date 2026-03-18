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
        'http://127.0.0.1:5173',  // Vite dev server (127.0.0.1)
        'http://localhost:3000',  // Alternative React dev port
        'http://127.0.0.1:3000',  // Alternative dev port (127.0.0.1)
        'http://localhost:8080',  // Alternative dev port
        'http://127.0.0.1:8080',  // Alternative dev port (127.0.0.1)
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
