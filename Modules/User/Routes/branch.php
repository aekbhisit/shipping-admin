<?php

/*
|--------------------------------------------------------------------------
| Branch Admin Routes - Branch User Management
|--------------------------------------------------------------------------
|
| Routes for Branch Admin to manage users within their assigned branch only
| Middleware: auth, role:branch_admin, branch.isolation
*/

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\BranchUserAdminController;

Route::group([
    'prefix' => 'admin/branch',
    'middleware' => ['auth', 'role:branch_admin', 'branch.isolation'],
    'as' => 'admin.branch.'
], function () {
    
    // Branch User Management Routes (Branch-scoped)
    Route::resource('users', BranchUserAdminController::class);
    
    // Branch Statistics
    Route::get('users-summary', [BranchUserAdminController::class, 'getBranchSummary'])
        ->name('users.summary');
    
    // Export Branch Users
    Route::get('users-export', [BranchUserAdminController::class, 'export'])
        ->name('users.export');
    
    // AJAX Routes for DataTable
    Route::get('users-data', [BranchUserAdminController::class, 'index'])
        ->name('users.data');
}); 