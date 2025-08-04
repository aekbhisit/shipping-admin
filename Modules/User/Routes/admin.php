<?php

use Modules\User\Http\Controllers\UserAdminController;
use Modules\User\Http\Controllers\RoleAdminController;  
use Modules\User\Http\Controllers\PermissionAdminController;
use Modules\User\Http\Controllers\UserPasswordController;

/*
|--------------------------------------------------------------------------
| User Module Admin Routes
|--------------------------------------------------------------------------
| Keep existing route structure - just organized in admin.php
| All patterns preserved for zero breaking changes
*/

// ============================== Authentication Routes ============================== //

use Modules\User\Http\Controllers\LoginController;

// Login & Logout (Public - no middleware) - Admin prefix group
Route::prefix('admin')->group(function () {
    Route::get('/', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/check_login', [LoginController::class, 'checkLogin'])->name('admin.login.check_login');
    Route::get('/logout', [LoginController::class, 'logout'])->name('admin.logout');

    // Password Reset (Public - no middleware)
    Route::get('/forget-password', [UserPasswordController::class, 'forget_password'])->middleware('guest')->name('admin.forget_password');
    Route::post('/forget-password', [UserPasswordController::class, 'forget_password'])->middleware('guest');
    Route::get('/reset-password/{token}', [UserPasswordController::class, 'reset_password'])->middleware('guest')->name('admin.reset_password');
    Route::post('/reset-password/', [UserPasswordController::class, 'set_reset_password'])->middleware('guest')->name('admin.set_reset_password');
    Route::get('/notify', [UserPasswordController::class, 'notify'])->middleware('guest')->name('admin.notify');
});

// ============================== Protected Admin Routes ============================== //

Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'adminAccessControl', 'HttpAccessLogFile']], function () {
    
    // ============================== User Management Routes ============================== //
    Route::prefix('user')->group(function () {
        Route::get('/', [UserAdminController::class, 'index'])->name('admin.user.user.index');
        Route::get('/datatable_ajax', [UserAdminController::class, 'datatable_ajax'])->name('admin.user.user.datatable_ajax');
        
        Route::get('/add', [UserAdminController::class, 'form'])->name('admin.user.user.add');
        Route::get('/edit/{id}', [UserAdminController::class, 'form'])->name('admin.user.user.edit');
        Route::post('/save', [UserAdminController::class, 'save'])->name('admin.user.user.save');
        
        Route::post('/set_status', [UserAdminController::class, 'set_status'])->name('admin.user.user.set_status');
        Route::post('/set_delete', [UserAdminController::class, 'set_delete'])->name('admin.user.user.set_delete');
    });

    // ============================== Company Admin Management Routes ============================== //
    Route::prefix('user/company')->group(function () {
        Route::get('/', [UserAdminController::class, 'companyIndex'])->name('admin.user.company.index');
        Route::get('/datatable_ajax', [UserAdminController::class, 'companyDatatableAjax'])->name('admin.user.company.datatable_ajax');
        
        Route::get('/add', [UserAdminController::class, 'companyForm'])->name('admin.user.company.add');
        Route::get('/edit/{id}', [UserAdminController::class, 'companyForm'])->name('admin.user.company.edit');
        Route::post('/save', [UserAdminController::class, 'companySave'])->name('admin.user.company.save');
        
        Route::post('/set_status', [UserAdminController::class, 'companySetStatus'])->name('admin.user.company.set_status');
        Route::post('/set_delete', [UserAdminController::class, 'companySetDelete'])->name('admin.user.company.set_delete');
    });

    // ============================== Branch Admin Management Routes ============================== //
    Route::prefix('user/branch')->group(function () {
        Route::get('/', [UserAdminController::class, 'branchIndex'])->name('admin.user.branch.index');
        Route::get('/datatable_ajax', [UserAdminController::class, 'branchDatatableAjax'])->name('admin.user.branch.datatable_ajax');
        
        Route::get('/add', [UserAdminController::class, 'branchForm'])->name('admin.user.branch.add');
        Route::get('/edit/{id}', [UserAdminController::class, 'branchForm'])->name('admin.user.branch.edit');
        Route::post('/save', [UserAdminController::class, 'branchSave'])->name('admin.user.branch.save');
        
        Route::post('/set_status', [UserAdminController::class, 'branchSetStatus'])->name('admin.user.branch.set_status');
        Route::post('/set_delete', [UserAdminController::class, 'branchSetDelete'])->name('admin.user.branch.set_delete');
    });

    // ============================== Branch Staff Management Routes ============================== //
    Route::prefix('user/staff')->group(function () {
        Route::get('/', [UserAdminController::class, 'staffIndex'])->name('admin.user.staff.index');
        Route::get('/datatable_ajax', [UserAdminController::class, 'staffDatatableAjax'])->name('admin.user.staff.datatable_ajax');
        
        Route::get('/add', [UserAdminController::class, 'staffForm'])->name('admin.user.staff.add');
        Route::get('/edit/{id}', [UserAdminController::class, 'staffForm'])->name('admin.user.staff.edit');
        Route::post('/save', [UserAdminController::class, 'staffSave'])->name('admin.user.staff.save');
        
        Route::post('/set_status', [UserAdminController::class, 'staffSetStatus'])->name('admin.user.staff.set_status');
        Route::post('/set_delete', [UserAdminController::class, 'staffSetDelete'])->name('admin.user.staff.set_delete');
    });

    // ============================== Role Management Routes ============================== //
    Route::prefix('user/role')->group(function () {
        Route::get('/', [RoleAdminController::class, 'index'])->name('admin.user.role.index');
        Route::get('/datatable_ajax', [RoleAdminController::class, 'datatable_ajax'])->name('admin.user.role.datatable_ajax');
        
        Route::get('/add', [RoleAdminController::class, 'form'])->name('admin.user.role.add');
        Route::get('/edit/{id}', [RoleAdminController::class, 'form'])->name('admin.user.role.edit');
        Route::post('/save', [RoleAdminController::class, 'save'])->name('admin.user.role.save'); // Fixed typo from original
        Route::post('/set_re_order', [RoleAdminController::class, 'set_re_order'])->name('admin.user.role.set_re_order');
        
        Route::post('/set_status', [RoleAdminController::class, 'set_status'])->name('admin.user.role.set_status');
        Route::post('/set_delete', [RoleAdminController::class, 'set_delete'])->name('admin.user.role.set_delete');
        
        Route::post('/get_role', [RoleAdminController::class, 'get_role'])->name('admin.user.role.get_role');
    });

    // ============================== Permission Management Routes ============================== //
    Route::prefix('user/permission')->group(function () {
        Route::get('/', [PermissionAdminController::class, 'index'])->name('admin.user.permission.index');
        Route::get('/datatable_ajax', [PermissionAdminController::class, 'datatable_ajax'])->name('admin.user.permission.datatable_ajax');
        
        Route::get('/add', [PermissionAdminController::class, 'form'])->name('admin.user.permission.add');
        Route::get('/edit/{id}', [PermissionAdminController::class, 'form'])->name('admin.user.permission.edit');
        Route::post('/save', [PermissionAdminController::class, 'save'])->name('admin.user.permission.save');
        Route::post('/set_re_order', [PermissionAdminController::class, 'set_re_order'])->name('admin.user.permission.set_re_order');
        
        Route::post('/set_status', [PermissionAdminController::class, 'set_status'])->name('admin.user.permission.set_status');
        Route::post('/set_delete', [PermissionAdminController::class, 'set_delete'])->name('admin.user.permission.set_delete');
        
        Route::post('/get_permission', [PermissionAdminController::class, 'get_permission'])->name('admin.user.permission.get_permission');
        Route::post('/get_route_name', [PermissionAdminController::class, 'get_route_name'])->name('admin.user.permission.get_route_name');
        Route::post('/generate_permission', [PermissionAdminController::class, 'generate_permission'])->name('admin.user.permission.generate_permission');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes - Company Admin User Management
|--------------------------------------------------------------------------
|
| Routes for Company Admin to manage all users across all branches
| Middleware: auth, role:company_admin, permission:users.manage_all
*/

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth', 'role:company_admin'],
    'as' => 'admin.'
], function () {
    
    // User Management Routes
    Route::resource('users', UserAdminController::class);
    
    // Additional User Management Actions
    Route::post('users/{id}/assign-branch', [UserAdminController::class, 'assignBranch'])
        ->name('users.assign-branch');
    
    Route::post('users/{id}/change-role', [UserAdminController::class, 'changeRole'])
        ->name('users.change-role');
    
    // Export Users
    Route::get('users-export', [UserAdminController::class, 'export'])
        ->name('users.export');
    
    // AJAX Routes for DataTable
    Route::get('users-data', [UserAdminController::class, 'index'])
        ->name('users.data');
}); 