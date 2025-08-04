<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Branch Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the Branch module
    | including default settings, pagination, and feature flags.
    |
    */

    'name' => 'Branch',

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'pagination' => 15,
        'max_markup_percentage' => 100,
        'min_markup_amount' => 0,
        'branch_code_length' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Branch Features
    |--------------------------------------------------------------------------
    */
    'features' => [
        'markup_management' => true,
        'performance_tracking' => true,
        'branch_isolation' => true,
        'export_reports' => true,
        'bulk_operations' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'branch_code' => 'nullable|string|max:50|unique:branches,code',
        'markup_percentage' => 'required|numeric|min:0|max:100',
        'min_markup_amount' => 'nullable|numeric|min:0',
        'max_markup_percentage' => 'required|numeric|min:0|max:100',
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Tracking
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'default_period' => 30, // days
        'metrics' => [
            'shipment_volume',
            'revenue',
            'markup_earnings',
            'carrier_usage',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Settings
    |--------------------------------------------------------------------------
    */
    'export' => [
        'formats' => ['csv', 'xlsx'],
        'max_records' => 10000,
    ],
]; 