<?php

return [
    'driver' => env('MAIL_DRIVER', 'log'),

    'host' => env('MAIL_HOST', 'localhost'),

    'port' => env('MAIL_PORT', 1025),

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'scada@simulator.local'),
        'name' => env('MAIL_FROM_NAME', 'Tank Level SCADA'),
    ],

    'encryption' => env('MAIL_ENCRYPTION'),

    'username' => env('MAIL_USERNAME'),

    'password' => env('MAIL_PASSWORD'),

    'timeout' => null,

    'local_domain' => env('MAIL_LOCAL_DOMAIN', 'localhost'),
];
