<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Audit Module
|--------------------------------------------------------------------------
|
| Routes for audit functionality accessible by admin users
| Middleware: auth, role-based permissions
*/

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:admin', 'adminAccessControl'],
    'as' => 'admin.'
], function () {
    
    // Audit Logs Management (Company Admin + Branch Admin with scope)
    Route::get('audit/datatable_ajax', 'AuditAdminController@datatable_ajax')->name('audit.datatable_ajax');
    Route::get('audit/export', 'AuditAdminController@export')->name('audit.export');
    Route::get('audit/search', 'AuditAdminController@search')->name('audit.search');
    Route::resource('audit', 'AuditAdminController')
        ->only(['index', 'show'])
        ->names([
            'index' => 'audit.index',
            'show' => 'audit.show'
        ]);

    // User Activity Logs (Company Admin + Branch Admin with scope)
    Route::get('user-activity/datatable_ajax', 'UserActivityAdminController@datatable_ajax')->name('user-activity.datatable_ajax');
    Route::get('user-activity/timeline/{user}', 'UserActivityAdminController@timeline')->name('user-activity.timeline');
    Route::get('user-activity/failed-attempts', 'UserActivityAdminController@failedAttempts')->name('user-activity.failed-attempts');
    Route::resource('user-activity', 'UserActivityAdminController')
        ->only(['index', 'show'])
        ->names([
            'index' => 'user-activity.index',
            'show' => 'user-activity.show'
        ]);

    // Compliance Reports (Company Admin only)
    Route::post('compliance/generate', 'ComplianceAdminController@generate')->name('compliance.generate');
    Route::get('compliance/{report}/download', 'ComplianceAdminController@download')->name('compliance.download');
    Route::get('compliance/dashboard', 'ComplianceAdminController@dashboard')->name('compliance.dashboard');
    Route::resource('compliance', 'ComplianceAdminController')
        ->except(['edit', 'update'])
        ->names([
            'index' => 'compliance.index',
            'create' => 'compliance.create',
            'store' => 'compliance.store',
            'show' => 'compliance.show',
            'destroy' => 'compliance.destroy'
        ]);
}); 