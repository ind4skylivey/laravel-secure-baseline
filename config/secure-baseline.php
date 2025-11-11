<?php

return [
    'enabled' => env('SECURE_BASELINE_ENABLED', true),

    'scanners' => [
        'config' => true,
        'session' => true,
        'cors' => true,
        'headers' => true,
        'routes' => true,
        'dependencies' => true,
    ],

    'custom_scanners' => [
        // 'custom-key' => \App\SecureBaseline\Scanners\CustomScanner::class,
    ],

    'report' => [
        'default_format' => env('SECURE_BASELINE_REPORT_FORMAT', 'md'),
        'default_output_path' => env('SECURE_BASELINE_REPORT_PATH', storage_path('logs/secure-baseline')),
        'include_timestamp' => true,
    ],

    'cli' => [
        'fail_on' => env('SECURE_BASELINE_FAIL_ON', 'fail'),
        'error_exit_code' => env('SECURE_BASELINE_EXIT_CODE', 2),
        'sarif_schema' => env('SECURE_BASELINE_SARIF_SCHEMA', 'https://json.schemastore.org/sarif-2.1.0.json'),
    ],

    'cors' => [
        'fail_wildcards_in_production' => true,
        'warn_on_credentials_without_https' => true,
    ],

    'routes' => [
        'sensitive_paths' => [
            '/telescope',
            '/horizon',
            '/phpinfo',
            '/_debugbar',
        ],
        'protected_middleware_indicators' => [
            'auth',
            'verified',
            'password.confirm',
            'can:',
            'ability:',
        ],
        'fail_when_unprotected_in_production' => true,
    ],

    'headers' => [
        'expectations' => [
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'no-referrer-when-downgrade',
            'Strict-Transport-Security' => 'max-age=63072000; includeSubDomains; preload',
            'Content-Security-Policy' => null,
        ],
        'detected' => [],
        'secure_headers_config_key' => 'secure-headers',
        'critical' => [
            'Strict-Transport-Security',
            'X-Content-Type-Options',
        ],
    ],

    'dependencies' => [
        'laravel' => [
            'min_supported_major' => 10,
            'latest_known_version' => env('SECURE_BASELINE_LARAVEL_LATEST', '11.46.1'),
        ],
    ],
];
