<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Default Module
|--------------------------------------------------------------------------
|
| Routes for default values administrative functionality
| Middleware: auth, admin permissions
*/

use Illuminate\Support\Facades\Route;
use Modules\Default\Http\Controllers\DefaultsAdminController;
use Modules\Default\Http\Controllers\DefaultCategoriesAdminController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth'],
    'as' => 'admin.'
], function () {
    
    // Default Values Management
    Route::resource('defaults', DefaultsAdminController::class);
    
    // Default Categories Management
    Route::resource('default-categories', DefaultCategoriesAdminController::class);
    
    // Additional Admin Actions
    Route::post('defaults/bulk-update', [DefaultsAdminController::class, 'bulkUpdate'])
        ->name('defaults.bulk-update');
    
    Route::get('defaults/export', [DefaultsAdminController::class, 'export'])
        ->name('defaults.export');
    
}); 