<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',
        'https://hispanic-guy-lovely-pairs.trycloudflare.com',
        'https://alloy-shaped-composed-southern.trycloudflare.com',
        'https://*.trycloudflare.com'
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => ['Content-Disposition'],
    'max_age' => 0,
    'supports_credentials' => false,
];
