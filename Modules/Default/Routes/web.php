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


Route::prefix('admin/default')->group(function () {
    Route::get('/', 'DefaultsAdminController@index')->name('admin.default.default.index');
    Route::get('/datatable_ajax', 'DefaultsAdminController@datatable_ajax')->name('admin.default.default.datatable_ajax');

    Route::get('/add', 'DefaultsAdminController@form')->name('admin.default.default.add');
    Route::get('/edit/{id}', 'DefaultsAdminController@form')->name('admin.default.default.edit');
    Route::post('/save', 'DefaultsAdminController@save')->name('admin.default.default.save');
    Route::post('/set_re_order', 'DefaultsAdminController@set_re_order')->name('admin.default.default.set_re_order');

    Route::post('/set_status', 'DefaultsAdminController@set_status')->name('admin.default.default.set_status');
    Route::post('/set_delete', 'DefaultsAdminController@set_delete')->name('admin.default.default.set_delete');

    Route::post('/get_default', 'DefaultsAdminController@get_default')->name('admin.default.default.get_default');

    Route::get('/datatable', 'DefaultsAdminController@datatable')->name('admin.default.default.datatable');
    Route::get('/table', 'DefaultsAdminController@table')->name('admin.default.default.table');
    Route::get('/tab', 'DefaultsAdminController@tab')->name('admin.default.default.tab');
    Route::get('/form_form', 'DefaultsAdminController@form_form')->name('admin.default.default.form_form');
    Route::get('/form_field', 'DefaultsAdminController@form_field')->name('admin.default.default.form_field');
    Route::get('/form_tab', 'DefaultsAdminController@form_tab')->name('admin.default.default.form_tab');
});

 // ============================== category ============================== //
    Route::prefix('admin/default/category')->group(function () {
        Route::get('/', 'DefaultCategoriesAdminController@index')->name('admin.default.category.index');
        Route::get('/datatable_ajax', 'DefaultCategoriesAdminController@datatable_ajax')->name('admin.default.category.datatable_ajax');

        Route::get('/add', 'DefaultCategoriesAdminController@form')->name('admin.default.category.add');
        Route::get('/edit/{category_id}', 'DefaultCategoriesAdminController@form')->name('admin.default.category.edit');
        Route::post('/save', 'DefaultCategoriesAdminController@save')->name('admin.default.category.save');

        Route::post('/sort', 'DefaultCategoriesAdminController@sort')->name('admin.default.category.sort');
        Route::post('/set_move_node', 'DefaultCategoriesAdminController@set_move_node')->name('admin.default.category.set_move_node');
        
        Route::post('/set_status', 'DefaultCategoriesAdminController@set_status')->name('admin.default.category.set_status');
        Route::post('/set_delete', 'DefaultCategoriesAdminController@set_delete')->name('admin.default.category.set_delete');

        Route::post('/get_category', 'DefaultCategoriesAdminController@get_category')->name('admin.default.category.get_category');
    });