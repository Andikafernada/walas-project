<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway Configuration
    |--------------------------------------------------------------------------
    */
    'whatsapp' => [
        'driver' => env('WA_DRIVER', 'n8n'), // n8n, fonnte, wablas

        // N8N Configuration
        'n8n' => [
            'webhook_url' => env('N8N_WEBHOOK_URL'),
            'secret_token' => env('N8N_SECRET_TOKEN'),
        ],

        // Fonnte Configuration
        'fonnte' => [
            'api_key' => env('FONNTE_API_KEY'),
            'url' => env('FONNTE_URL', 'https://mu.fonnte.com/api/send'),
        ],

        // Wablas Configuration
        'wablas' => [
            'url' => env('WABLAS_URL'),
            'token' => env('WABLAS_TOKEN'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Attendance Settings
    |--------------------------------------------------------------------------
    */
    'attendance' => [
        'default_expires_at' => env('ATTENDANCE_EXPIRES_AT', '15:00'), // 3 PM WIB
        'pin_length' => 4,
        'token_length' => 64,
        'auto_expire_check_interval' => 60, // minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Violation Settings
    |--------------------------------------------------------------------------
    */
    'violation' => [
        'default_poin' => 100,
        'max_poin' => 100,
        'severity_poin' => [
            'ringan' => 5,
            'sedang' => 10,
            'berat' => 15,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Settings
    |--------------------------------------------------------------------------
    */
    'export' => [
        'disk' => env('EXPORT_DISK', 'local'),
        'path' => env('EXPORT_PATH', 'exports'),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    */
    'api' => [
        'version' => 'v1',
        'rate_limit' => 60, // requests per minute
        'token_expiry' => 365, // days
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Tiers
    |--------------------------------------------------------------------------
    */
    'subscription' => [
        'free' => [
            'max_classes' => 1,
            'max_students' => 30,
            'features' => ['basic_attendance', 'basic_reports'],
        ],
        'pro' => [
            'max_classes' => 5,
            'max_students' => 500,
            'features' => ['advanced_attendance', 'advanced_reports', 'api_access', 'whatsapp'],
        ],
        'enterprise' => [
            'max_classes' => -1, // unlimited
            'max_students' => -1,
            'features' => ['all'],
        ],
    ],
];
