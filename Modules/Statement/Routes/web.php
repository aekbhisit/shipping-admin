<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth:admin','AdminAccessControl']], function () {
    Route::prefix('admin/statement')->group(function () {
        Route::prefix('list')->group(function () {
            Route::get('/', 'StatementAdminController@index')->name('admin.statement.list.index');
            Route::get('/datatable_ajax', 'StatementAdminController@datatable_ajax')->name('admin.statement.list.datatable_ajax');
            Route::get('/view/{id?}', 'StatementAdminController@form')->name('admin.statement.list.view');
        });
        Route::prefix('sms')->group(function () {
            Route::get('/', 'SmsAdminController@index')->name('admin.statement.sms.index');
            Route::get('/datatable_ajax', 'SmsAdminController@datatable_ajax')->name('admin.statement.sms.datatable_ajax');
            Route::get('/view/{id?}', 'SmsAdminController@form')->name('admin.statement.sms.view');
        });
        Route::prefix('transfer')->group(function () {
            Route::get('/', 'TransferAdminController@index')->name('admin.statement.transfer.index');
            Route::get('/datatable_ajax', 'TransferAdminController@datatable_ajax')->name('admin.statement.transfer.datatable_ajax');
            Route::get('/view/{id?}', 'TransferAdminController@form')->name('admin.statement.transfer.view');
        });
        Route::prefix('temp')->group(function () {
            Route::get('/', 'TempAdminController@index')->name('admin.statement.temp.index');
            Route::get('/datatable_ajax', 'TempAdminController@datatable_ajax')->name('admin.statement.temp.datatable_ajax');
            Route::get('/view/{id?}', 'TempAdminController@form')->name('admin.statement.temp.view');
        });
    });
});
