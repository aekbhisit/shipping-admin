<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Product module.
    |
    */

    'name' => 'Product',
    
    'description' => 'Product management module for e-commerce functionality',
    
    /*
    |--------------------------------------------------------------------------
    | Product Settings
    |--------------------------------------------------------------------------
    */
    
    'default_currency' => 'THB',
    
    'default_unit' => 'piece',
    
    'image_path' => 'products',
    
    'max_image_size' => 2048, // KB
    
    'allowed_image_types' => ['jpeg', 'png', 'jpg', 'gif'],
    
    /*
    |--------------------------------------------------------------------------
    | Add-On Service Settings
    |--------------------------------------------------------------------------
    */
    
    'service_types' => [
        'insurance' => 'Insurance Services',
        'handling' => 'Special Handling',
        'delivery' => 'Delivery Options',
        'cod' => 'COD Services'
    ],
    
    'pricing_types' => [
        'fixed' => 'Fixed Price',
        'percentage' => 'Percentage of Base Amount',
        'tiered' => 'Tiered Pricing'
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Category Settings
    |--------------------------------------------------------------------------
    */
    
    'max_category_depth' => 3,
    
    'default_sort_order' => 0,
    
    /*
    |--------------------------------------------------------------------------
    | Branch Settings
    |--------------------------------------------------------------------------
    */
    
    'enable_branch_pricing' => true,
    
    'enable_branch_availability' => true,
]; 