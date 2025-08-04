<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Carriers Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains default carrier configurations that will be seeded
    | into the database when the module is installed.
    |
    */

    'default_carriers' => [
        [
            'name' => 'Thailand Post',
            'code' => 'TP',
            'api_base_url' => 'https://trackapi.thailandpost.co.th',
            'api_version' => 'v1',
            'logo_path' => null,
            'is_active' => true,
            'supported_services' => ['EMS', 'Registered Mail', 'Surface Mail'],
            'api_documentation_url' => 'https://developer.thailandpost.co.th/docs'
        ],
        [
            'name' => 'J&T Express',
            'code' => 'JT',
            'api_base_url' => 'https://openapi.jtexpress.my',
            'api_version' => 'v1',
            'logo_path' => null,
            'is_active' => true,
            'supported_services' => ['Standard', 'Express', 'Premium'],
            'api_documentation_url' => 'https://developer.jtexpress.my/docs'
        ],
        [
            'name' => 'Flash Express',
            'code' => 'FLASH',
            'api_base_url' => 'https://open-api.flashexpress.com',
            'api_version' => 'v1',
            'logo_path' => null,
            'is_active' => true,
            'supported_services' => ['Standard', 'Express', 'Same Day'],
            'api_documentation_url' => 'https://developer.flashexpress.com/docs'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Global API settings and limits
    |
    */

    'api' => [
        'timeout' => 30, // seconds
        'retry_attempts' => 3,
        'rate_limit_delay' => 500, // milliseconds between requests
        'max_weight' => 30, // kg
        'max_dimension' => 100, // cm
        'min_weight' => 0.1, // kg
        'min_dimension' => 1, // cm
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Markup Configuration
    |--------------------------------------------------------------------------
    |
    | Default markup percentage if no branch-specific markup is configured
    |
    */

    'default_markup' => [
        'percentage' => 5.0, // 5% default markup
    ],

    /*
    |--------------------------------------------------------------------------
    | Credential Requirements
    |--------------------------------------------------------------------------
    |
    | Required credential fields for each carrier
    |
    */

    'credential_requirements' => [
        'TP' => [
            'api_key' => 'API Key',
            'username' => 'Username'
        ],
        'JT' => [
            'api_key' => 'API Key',
            'api_secret' => 'API Secret',
            'customer_code' => 'Customer Code'
        ],
        'FLASH' => [
            'api_key' => 'API Key',
            'api_token' => 'API Token',
            'shop_id' => 'Shop ID'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Type Mappings
    |--------------------------------------------------------------------------
    |
    | Map common service types to carrier-specific codes
    |
    */

    'service_mappings' => [
        'standard' => [
            'TP' => 'SURFACE',
            'JT' => 'STANDARD',
            'FLASH' => 'STANDARD'
        ],
        'express' => [
            'TP' => 'EMS',
            'JT' => 'EXPRESS',
            'FLASH' => 'EXPRESS'
        ],
        'premium' => [
            'TP' => 'EMS',
            'JT' => 'PREMIUM',
            'FLASH' => 'SAME_DAY'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for API request logging and debugging
    |
    */

    'logging' => [
        'enabled' => true,
        'log_requests' => true,
        'log_responses' => true,
        'log_errors' => true,
        'retention_days' => 30, // Keep logs for 30 days
    ]
]; 