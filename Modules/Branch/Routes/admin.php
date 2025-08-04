<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Company Admin Branch Management
|--------------------------------------------------------------------------
|
| Routes for Company Admin to manage all branches
| Middleware: auth, role:company_admin
*/

use Illuminate\Support\Facades\Route;
use Modules\Branch\Http\Controllers\BranchAdminController;
use Modules\Branch\Http\Controllers\BranchSettingsController;
use Modules\Branch\Http\Controllers\BranchReportController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:admin', 'role:company_admin'],
    'as' => 'admin.'
], function () {
    
    // DataTable AJAX - Must be before resource routes to avoid route model binding conflicts
    Route::get('branches/datatable_ajax', [BranchAdminController::class, 'datatable_ajax'])
        ->name('branches.datatable_ajax');
    

    
    // Export - Must be before resource routes
    Route::get('branches-export', [BranchAdminController::class, 'export'])
        ->name('branches.export');
    
    // Bulk Operations - Must be before resource routes
    Route::post('branches/bulk-update', [BranchAdminController::class, 'bulkUpdate'])
        ->name('branches.bulk-update');
    
    // Branch Management Routes - All routes have proper names
    Route::resource('branches', BranchAdminController::class)->names([
        'index' => 'branches.index',
        'create' => 'branches.create',
        'store' => 'branches.store',
        'show' => 'branches.show',
        'edit' => 'branches.edit',
        'update' => 'branches.update',
        'destroy' => 'branches.destroy'
    ]);
    
    // Branch Markups Management
    Route::get('branches/{branch}/markups', [BranchAdminController::class, 'viewMarkups'])
        ->name('branches.markups');
    
    Route::post('branches/{branch}/markups', [BranchAdminController::class, 'updateMarkups'])
        ->name('branches.markups.update');
    
    // Branch Performance
    Route::get('branches/{branch}/performance', [BranchAdminController::class, 'performance'])
        ->name('branches.performance');
    
    // Branch Activation/Deactivation
    Route::patch('branches/{branch}/activate', [BranchAdminController::class, 'activate'])
        ->name('branches.activate');
    
    // AJAX Data
    Route::get('branches/{branch}/data', [BranchAdminController::class, 'getBranchData'])
        ->name('branches.data');
    
    // =================================================================
    // BRANCH SETTINGS ROUTES (Branch Admin Only)
    // =================================================================
    Route::group([
        'prefix' => 'branch-settings',
        'middleware' => ['auth:admin', 'role:branch_admin'],
        'as' => 'branch.settings.'
    ], function () {
        Route::get('/', [BranchSettingsController::class, 'index'])->name('index');
        Route::get('/edit', [BranchSettingsController::class, 'edit'])->name('edit');
        Route::put('/update', [BranchSettingsController::class, 'update'])->name('update');
        Route::get('/markups', [BranchSettingsController::class, 'markups'])->name('markups');
        Route::post('/markups', [BranchSettingsController::class, 'updateMarkups'])->name('markups.update');
        Route::get('/profile', [BranchSettingsController::class, 'profile'])->name('profile');
    });
    
    // =================================================================
    // BRANCH REPORTS ROUTES (Company Admin & Branch Admin)
    // =================================================================
    Route::group([
        'prefix' => 'branch-reports',
        'middleware' => ['auth:admin', 'role:company_admin|branch_admin'],
        'as' => 'branch.reports.'
    ], function () {
        Route::get('/dashboard', [BranchReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/shipment-stats', [BranchReportController::class, 'shipmentStats'])->name('shipment-stats');
        Route::get('/revenue', [BranchReportController::class, 'revenueReport'])->name('revenue');
        Route::get('/carrier-performance', [BranchReportController::class, 'carrierPerformance'])->name('carrier-performance');
        Route::get('/export', [BranchReportController::class, 'export'])->name('export');
    });
}); 