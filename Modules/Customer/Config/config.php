<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Customer Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the Customer module
    | including customer types, validation rules, and search settings.
    |
    */

    'name' => 'Customer',

    /*
    |--------------------------------------------------------------------------
    | Customer Types
    |--------------------------------------------------------------------------
    |
    | Define the types of customers that can be created in the system.
    |
    */
    'customer_types' => [
        'individual' => 'Individual',
        'business' => 'Business',
        'corporate' => 'Corporate',
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for customer search and autocomplete functionality.
    |
    */
    'search' => [
        'min_length' => 2,
        'max_results' => 20,
        'recent_limit' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Address Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for address management and validation.
    |
    */
    'address' => [
        'max_per_sender' => 10,
        'require_validation' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for customer lists.
    |
    */
    'pagination' => [
        'customers_per_page' => 25,
        'senders_per_page' => 20,
        'receivers_per_page' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Custom validation rules for customer data.
    |
    */
    'validation' => [
        'customer' => [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:50',
            'customer_type' => 'required|in:individual,business,corporate',
        ],
        'sender' => [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ],
        'receiver' => [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ],
        'address' => [
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
        ],
    ],
]; 