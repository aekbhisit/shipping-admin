<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Job Module
|--------------------------------------------------------------------------
|
| Routes for job and credit management administrative functionality
| Middleware: auth, admin permissions
*/

use Illuminate\Support\Facades\Route;
use Modules\Job\Http\Controllers\JobAdminController;
use Modules\Job\Http\Controllers\ManualCreditAdminController;
use Modules\Job\Http\Controllers\JobController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:admin'],
    'as' => 'admin.'
], function () {
    
    // Job Administration
    Route::resource('jobs', JobAdminController::class);
    
    // Manual Credit Administration
    Route::resource('manual-credits', ManualCreditAdminController::class);
    
    // Job Management
    Route::post('jobs/{job}/retry', [JobController::class, 'retry'])
        ->name('jobs.retry');
    
    Route::post('jobs/{job}/cancel', [JobController::class, 'cancel'])
        ->name('jobs.cancel');
    
    Route::get('jobs/{job}/logs', [JobController::class, 'logs'])
        ->name('jobs.logs');
    
    // Bulk Operations
    Route::post('jobs/bulk-retry', [JobAdminController::class, 'bulkRetry'])
        ->name('jobs.bulk-retry');
    
    Route::post('jobs/bulk-cancel', [JobAdminController::class, 'bulkCancel'])
        ->name('jobs.bulk-cancel');
    
}); 