<?php

/*
|--------------------------------------------------------------------------
| Public Routes - Branch Module
|--------------------------------------------------------------------------
|
| Public routes that don't require authentication
| For frontend/public access only
*/

use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::group([
    'prefix' => 'branch',
    'namespace' => 'Modules\Branch\Http\Controllers'
], function () {
    // Test route to verify module is working
    Route::get('test', function() {
    return response()->json([
        'message' => 'Branch module is working!',
        'timestamp' => now(),
        'module' => 'Branch'
    ]);
})->name('branch.test');

// Debug route for testing branch data
Route::get('debug', function() {
    try {
        $branches = \Modules\Branch\Entities\Branch::all();
        return response()->json([
            'success' => true,
            'count' => $branches->count(),
            'branches' => $branches->map(function($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'code' => $branch->code,
                    'stats' => $branch->getStats(),
                    'performance' => $branch->getPerformanceMetrics()
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('branch.debug');

// Debug route for testing DataTable format
Route::get('debug-datatable', function() {
    try {
        $branches = \Modules\Branch\Entities\Branch::all();
        return response()->json([
            'draw' => 1,
            'recordsTotal' => $branches->count(),
            'recordsFiltered' => $branches->count(),
            'data' => $branches->map(function ($branch, $index) {
                return [
                    'DT_RowIndex' => $index + 1,
                    'name' => '<strong>' . $branch->name . '</strong><br><small class="text-muted">' . $branch->short_address . '</small>',
                    'code' => '<span class="badge bg-secondary">' . $branch->code . '</span>',
                    'contact_person' => '<i class="bx bx-user"></i> ' . $branch->contact_person . '<br><i class="bx bx-phone"></i> ' . $branch->formatted_phone,
                    'users_count' => '<span class="badge bg-info">' . $branch->getStats()['active_users'] . '</span> Active<br><span class="badge bg-light text-dark">' . $branch->getStats()['total_users'] . '</span> Total',
                    'performance' => '<strong>฿' . number_format($branch->getPerformanceMetrics()['total_revenue'], 0) . '</strong><br><small class="text-muted">' . $branch->getPerformanceMetrics()['total_shipments'] . ' shipments</small>',
                    'markups_count' => '<span class="badge bg-primary">' . $branch->getStats()['active_markups'] . '</span> Active<br><span class="badge bg-light text-dark">' . $branch->getStats()['total_markups'] . '</span> Total',
                    'status' => '<span class="' . $branch->status_badge . '">' . $branch->status_text . '</span>',
                    'actions' => '<a href="#" class="btn btn-sm btn-outline-primary me-1" title="View"><i class="bx bx-show"></i></a>'
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('branch.debug-datatable');
    
    // Branch locator (if needed)
    Route::get('locate', 'BranchPublicController@locate')
         ->name('branch.locate');
    
    // Branch operating hours
    Route::get('{branch}/hours', 'BranchPublicController@getOperatingHours')
         ->name('branch.hours');
});

/*
|--------------------------------------------------------------------------
| Route Model Binding
|--------------------------------------------------------------------------
*/

Route::bind('branch', function ($value) {
    return \Modules\Branch\Entities\Branch::where('id', $value)
           ->orWhere('code', $value)
           ->firstOrFail();
});

/*
|--------------------------------------------------------------------------
| Route Caching
|--------------------------------------------------------------------------
|
| These routes support Laravel's route caching for better performance
| Run: php artisan route:cache
|
*/ 