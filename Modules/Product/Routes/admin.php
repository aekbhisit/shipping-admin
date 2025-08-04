<?php

/*
|--------------------------------------------------------------------------
| Product Admin Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:admin', 'adminAccessControl'],
    'as' => 'admin.'
], function () {
    
    // ========================================
    // PRODUCT MANAGEMENT ROUTES
    // Product Management Routes
    
    // DataTable AJAX route - Use a completely different path to avoid conflicts
    Route::get('product-datatable', [\Modules\Product\Http\Controllers\ProductAdminController::class, 'getDataTable'])->name('products.datatable_ajax');
    
    // Resource routes
    Route::resource('products', \Modules\Product\Http\Controllers\ProductAdminController::class)
        ->names([
            'index' => 'products.index',
            'create' => 'products.create',
            'store' => 'products.store',
            'show' => 'products.show',
            'edit' => 'products.edit',
            'update' => 'products.update',
            'destroy' => 'products.destroy'
        ]);
    
}); 