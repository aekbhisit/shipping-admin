<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Setting Module
|--------------------------------------------------------------------------
|
| Routes for system settings administrative functionality
| Middleware: auth, admin permissions
*/

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\SlugAdminController;
use Modules\Setting\Http\Controllers\TagsAdminController;
use Modules\Setting\Http\Controllers\SettingController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:admin'],
    'as' => 'admin.'
], function () {
    
    // Web Settings (matching sidebar expectations)
    Route::prefix('setting')->group(function () {
        Route::get('web', [SettingController::class, 'index'])->name('setting.web.index');
        Route::post('web', [SettingController::class, 'store'])->name('setting.web.save');
        
        // Slug Administration
        Route::prefix('slug')->group(function () {
            Route::get('/', [SlugAdminController::class, 'index'])->name('setting.slug.index');
            Route::get('/edit/{id}', [SlugAdminController::class, 'edit'])->name('setting.slug.edit');
            Route::post('/set_delete', [SlugAdminController::class, 'destroy'])->name('setting.slug.set_delete');
            Route::post('/regenerate', [SlugAdminController::class, 'regenerateAll'])->name('setting.slug.regenerate');
        });
        
        // Tags Administration
        Route::prefix('tag')->group(function () {
            Route::get('/', [TagsAdminController::class, 'index'])->name('setting.tag.index');
            Route::get('/add', [TagsAdminController::class, 'create'])->name('setting.tag.add');
            Route::get('/edit/{id}', [TagsAdminController::class, 'edit'])->name('setting.tag.edit');
            Route::post('/save', [TagsAdminController::class, 'store'])->name('setting.tag.save');
            Route::post('/set_delete', [TagsAdminController::class, 'destroy'])->name('setting.tag.set_delete');
            Route::post('/bulk-delete', [TagsAdminController::class, 'bulkDelete'])->name('setting.tag.bulk-delete');
        });
    });
    
}); 