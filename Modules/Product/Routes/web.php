<?php

/*
|--------------------------------------------------------------------------
| Product Module Web Routes  
|--------------------------------------------------------------------------
|
| Product management routes organized by role and access level:
| - Company Admin: Global product catalog management
| - Branch Admin: Branch-specific product availability  
| - Branch Staff: Product selection for shipments
|
*/

// ========================================
// COMPANY ADMIN ROUTES
// ========================================
Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth', 'role:company_admin'],
    'as' => 'admin.'
], function () {
    
    // Product Management Routes
    Route::resource('products', \Modules\Product\Http\Controllers\ProductAdminController::class);
    Route::post('products/{product}/toggle-status', [\Modules\Product\Http\Controllers\ProductAdminController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('products/bulk-action', [\Modules\Product\Http\Controllers\ProductAdminController::class, 'bulkAction'])->name('products.bulk-action');
    
    // Category Management Routes  
    Route::resource('categories', \Modules\Product\Http\Controllers\CategoryAdminController::class);
    Route::post('categories/{category}/toggle-status', [\Modules\Product\Http\Controllers\CategoryAdminController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::post('categories/bulk-action', [\Modules\Product\Http\Controllers\CategoryAdminController::class, 'bulkAction'])->name('categories.bulk-action');
    Route::post('categories/update-sort-order', [\Modules\Product\Http\Controllers\CategoryAdminController::class, 'updateSortOrder'])->name('categories.update-sort-order');
});

// ========================================
// BRANCH ADMIN ROUTES
// ========================================
Route::group([
    'prefix' => 'admin/branch',
    'middleware' => ['auth', 'role:branch_admin', 'branch.isolation'],
    'as' => 'admin.branch.'
], function () {
    
    // Branch Product Management Routes
    Route::get('products', [\Modules\Product\Http\Controllers\BranchProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [\Modules\Product\Http\Controllers\BranchProductController::class, 'show'])->name('products.show');
    Route::post('products/{product}/toggle-availability', [\Modules\Product\Http\Controllers\BranchProductController::class, 'toggleAvailability'])->name('products.toggle-availability');
    Route::post('products/{product}/update-price', [\Modules\Product\Http\Controllers\BranchProductController::class, 'updatePrice'])->name('products.update-price');
    Route::post('products/bulk-action', [\Modules\Product\Http\Controllers\BranchProductController::class, 'bulkAction'])->name('products.bulk-action');
    Route::get('products/{product}/data', [\Modules\Product\Http\Controllers\BranchProductController::class, 'getBranchProductData'])->name('products.data');
});

// ========================================
// BRANCH STAFF ROUTES
// ========================================
Route::group([
    'prefix' => 'staff',
    'middleware' => ['auth', 'role:branch_staff'],
    'as' => 'staff.'
], function () {
    
    // Product Selection Routes
    Route::get('products', [\Modules\Product\Http\Controllers\ProductSelectionController::class, 'catalog'])->name('products.catalog');
    Route::get('products/search', [\Modules\Product\Http\Controllers\ProductSelectionController::class, 'search'])->name('products.search');
    Route::post('products/select', [\Modules\Product\Http\Controllers\ProductSelectionController::class, 'select'])->name('products.select');
    Route::get('products/{product}/details', [\Modules\Product\Http\Controllers\ProductSelectionController::class, 'getProductDetails'])->name('products.details');
    Route::get('categories/{category}/products', [\Modules\Product\Http\Controllers\ProductSelectionController::class, 'getByCategory'])->name('categories.products');
    Route::post('products/calculate-total', [\Modules\Product\Http\Controllers\ProductSelectionController::class, 'calculateTotal'])->name('products.calculate-total');
});

// ========================================
// API ROUTES FOR AJAX CALLS
// ========================================
Route::group([
    'prefix' => 'api/products',
    'middleware' => ['auth'],
    'as' => 'api.products.'
], function () {
    
    // Global API routes (accessible by all authenticated users)
    Route::get('search', function(\Illuminate\Http\Request $request) {
        // Quick product search for all users
        $search = $request->get('q', '');
        if (strlen($search) < 2) {
            return response()->json(['products' => []]);
        }
        
        $products = \Modules\Product\Entities\Product::active()
            ->search($search)
            ->limit(10)
            ->get(['id', 'name', 'sku', 'price']);
            
        return response()->json(['products' => $products]);
    })->name('search');
    
    Route::get('{product}', function(\Modules\Product\Entities\Product $product) {
        // Basic product info for all users
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->price,
            'formatted_price' => $product->formatted_price,
            'unit' => $product->unit,
            'category' => $product->category->name
        ]);
    })->name('show');
});

// ========================================
// LEGACY/FALLBACK ROUTES (if needed)
// ========================================
Route::group([
    'prefix' => 'product',
    'middleware' => ['auth']
], function () {
    
    // Redirect old routes to new structure
    Route::get('/', function() {
        if (auth()->user()->hasRole('company_admin')) {
            return redirect()->route('admin.products.index');
        } elseif (auth()->user()->hasRole('branch_admin')) {
            return redirect()->route('admin.branch.products.index');
        } elseif (auth()->user()->hasRole('branch_staff')) {
            return redirect()->route('staff.products.catalog');
        }
        
        return redirect('/dashboard');
    })->name('product.index');
}); 