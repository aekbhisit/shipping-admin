<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Core Module
|--------------------------------------------------------------------------
|
| Routes for core administrative functionality
| Middleware: auth, admin permissions
*/

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\AdminController;
use Modules\Core\Http\Controllers\AdminMenuController;
use Modules\Core\Http\Controllers\AdminMenusAdminController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:admin'],
    'as' => 'admin.'
], function () {
    
    // Admin Menu Management (matching sidebar expectations)
    Route::prefix('admin_menu')->group(function () {
        Route::get('/', [AdminMenusAdminController::class, 'index'])->name('admin_menu.admin_menu.index');
        Route::get('/add', [AdminMenusAdminController::class, 'form'])->name('admin_menu.admin_menu.add');
        Route::get('/edit/{id}', [AdminMenusAdminController::class, 'form'])->name('admin_menu.admin_menu.edit');
        Route::post('/save', [AdminMenusAdminController::class, 'save'])->name('admin_menu.admin_menu.save');
        Route::post('/set_delete', [AdminMenusAdminController::class, 'set_delete'])->name('admin_menu.admin_menu.set_delete');
        Route::post('/set_status', [AdminMenusAdminController::class, 'set_status'])->name('admin_menu.admin_menu.set_status');
        Route::post('/sort', [AdminMenusAdminController::class, 'sort'])->name('admin_menu.admin_menu.sort');
        Route::post('/set_move_node', [AdminMenusAdminController::class, 'set_move_node'])->name('admin_menu.admin_menu.set_move_node');
        Route::get('/datatable_ajax', [AdminMenusAdminController::class, 'datatable_ajax'])->name('admin_menu.admin_menu.datatable_ajax');
    });
    
    // Core Menu Controllers
    Route::get('menu/structure', [AdminMenuController::class, 'structure'])->name('menu.structure');
    Route::post('menu/reorder', [AdminMenuController::class, 'reorder'])->name('menu.reorder');
    Route::get('menu/permissions', [AdminMenuController::class, 'permissions'])->name('menu.permissions');
    Route::post('menu/permissions', [AdminMenuController::class, 'updatePermissions'])->name('menu.permissions.update');
    
    // Not Permitted Route (for access control)
    Route::get('not-permitted', [AdminController::class, 'not_permit'])->name('not.permitted');
    
}); 