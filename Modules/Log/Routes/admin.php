<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Log Module
|--------------------------------------------------------------------------
|
| Routes for log management administrative functionality
| Middleware: auth, admin permissions
*/

use Illuminate\Support\Facades\Route;
use Modules\Log\Http\Controllers\LogsAdminController;
use Modules\Log\Http\Controllers\LogsController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:admin'],
    'as' => 'admin.'
], function () {
    
    // Log Administration (matching sidebar expectations)
    Route::prefix('log')->group(function () {
        Route::get('/', [LogsAdminController::class, 'index'])->name('log.log.index');
        Route::get('/add', [LogsAdminController::class, 'create'])->name('log.log.add');
        Route::get('/edit/{id}', [LogsAdminController::class, 'edit'])->name('log.log.edit');
        Route::post('/save', [LogsAdminController::class, 'store'])->name('log.log.save');
        Route::post('/set_delete', [LogsAdminController::class, 'destroy'])->name('log.log.set_delete');
        
        // Log Management
        Route::get('/view/{id}', [LogsController::class, 'view'])->name('log.log.view');
        Route::delete('/clear', [LogsController::class, 'clear'])->name('log.log.clear');
        Route::get('/download', [LogsController::class, 'download'])->name('log.log.download');
        
        // Log Filtering
        Route::get('/filter/{type}', [LogsAdminController::class, 'filterByType'])->name('log.log.filter');
        Route::get('/search', [LogsAdminController::class, 'search'])->name('log.log.search');
    });
    
}); 