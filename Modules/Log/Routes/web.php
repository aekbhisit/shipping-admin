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

Route::group(['middleware' => ['auth:admin','adminAccessControl']], function () {
    Route::prefix('admin/logs')->group(function() {
        Route::get('/', 'LogsAdminController@index')->name('admin.log.log.index');
        Route::get('/datatable_ajax', 'LogsAdminController@datatable_ajax')->name('admin.log.log.datatable_ajax');
        Route::get('/view/{log_id}', 'LogsAdminController@view')->name('admin.log.log.view');
    });
});