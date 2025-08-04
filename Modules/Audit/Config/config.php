<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Audit Module Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for the Audit module
    |
    */

    'name' => 'Audit',

    /*
    |--------------------------------------------------------------------------
    | Data Retention Settings
    |--------------------------------------------------------------------------
    |
    | Configure how long audit data should be retained
    |
    */
    'retention' => [
        'audit_logs' => 730, // 2 years
        'user_activity' => 365, // 1 year
        'compliance_reports' => 90, // 90 days
        'failed_attempts' => 30, // 30 days
    ],

    /*
    |--------------------------------------------------------------------------
    | Sensitive Data Fields
    |--------------------------------------------------------------------------
    |
    | Fields that should be masked in audit logs
    |
    */
    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'api_key',
        'secret',
        'token',
        'credit_card',
        'ssn',
        'phone',
        'email',
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Events
    |--------------------------------------------------------------------------
    |
    | Events that should be audited
    |
    */
    'events' => [
        'created',
        'updated',
        'deleted',
        'login',
        'logout',
        'failed_login',
        'export',
        'import',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models to Audit
    |--------------------------------------------------------------------------
    |
    | Models that should be audited
    |
    */
    'auditable_models' => [
        'Modules\User\Entities\User',
        'Modules\Branch\Entities\Branch',
        'Modules\Customer\Entities\Customer',
        'Modules\Shipment\Entities\Shipment',
        'Modules\Shipper\Entities\Shipper',
        'Modules\Product\Entities\Product',
    ],
]; 