<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Filemanager Module
|--------------------------------------------------------------------------
|
| Routes for file management administrative functionality
| Middleware: auth, admin permissions
*/

use Illuminate\Support\Facades\Route;
use Modules\Filemanager\Http\Controllers\FilemanagerAdminController;
use Modules\Filemanager\Http\Controllers\FilemanagerController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:admin'],
    'as' => 'admin.'
], function () {
    
    // File Manager Administration (matching sidebar expectations)
    Route::prefix('filemanager')->group(function () {
        Route::get('/', [FilemanagerAdminController::class, 'index'])->name('filemanager.filemanager.index');
        Route::get('/add', [FilemanagerAdminController::class, 'create'])->name('filemanager.filemanager.add');
        Route::get('/edit/{id}', [FilemanagerAdminController::class, 'edit'])->name('filemanager.filemanager.edit');
        Route::post('/save', [FilemanagerAdminController::class, 'store'])->name('filemanager.filemanager.save');
        Route::post('/set_delete', [FilemanagerAdminController::class, 'destroy'])->name('filemanager.filemanager.set_delete');
        
        // File Management
        Route::post('/upload', [FilemanagerController::class, 'upload'])->name('filemanager.filemanager.upload');
        Route::delete('/delete/{id}', [FilemanagerController::class, 'delete'])->name('filemanager.filemanager.delete');
        Route::get('/download/{id}', [FilemanagerController::class, 'download'])->name('filemanager.filemanager.download');
        
        // Bulk Operations
        Route::post('/bulk-delete', [FilemanagerAdminController::class, 'bulkDelete'])->name('filemanager.filemanager.bulk-delete');
    });
    
}); 