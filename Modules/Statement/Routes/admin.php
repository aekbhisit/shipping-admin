<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Statement Module
|--------------------------------------------------------------------------
|
| Routes for statement and SMS administrative functionality
| Middleware: auth, admin permissions
*/

use Illuminate\Support\Facades\Route;
use Modules\Statement\Http\Controllers\SmsAdminController;
use Modules\Statement\Http\Controllers\StatementAdminController;
use Modules\Statement\Http\Controllers\TempAdminController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth'],
    'as' => 'admin.'
], function () {
    
    // SMS Administration
    Route::resource('sms', SmsAdminController::class);
    
    // Statement Administration
    Route::resource('statements', StatementAdminController::class);
    
    // Temporary Data Administration
    Route::resource('temp-data', TempAdminController::class);
    
    // SMS Operations
    Route::post('sms/send', [SmsAdminController::class, 'send'])
        ->name('sms.send');
    
    Route::get('sms/templates', [SmsAdminController::class, 'templates'])
        ->name('sms.templates');
    
    // Statement Operations
    Route::get('statements/generate', [StatementAdminController::class, 'generate'])
        ->name('statements.generate');
    
    Route::post('statements/export', [StatementAdminController::class, 'export'])
        ->name('statements.export');
    
    // Temp Data Operations
    Route::post('temp-data/cleanup', [TempAdminController::class, 'cleanup'])
        ->name('temp-data.cleanup');
    
    Route::post('temp-data/archive', [TempAdminController::class, 'archive'])
        ->name('temp-data.archive');
    
}); 