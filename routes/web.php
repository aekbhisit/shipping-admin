<?php

Route::get('/clear-cache', function() {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    return 'Cache cleared successfully!';
});

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

Route::get('/', function () {
  return redirect('/admin');
});

/*
|--------------------------------------------------------------------------
| LIFF (LINE Front-end Framework) Routes
|--------------------------------------------------------------------------
*/

Route::prefix('liffv2')->group(function () {
    Route::get('/user', function () {
        return view('liff.user'); // You'll need to create this view
    })->name('liff.user');
    
    Route::get('/user/{id}', function ($id) {
        return view('liff.user-detail', compact('id'));
    })->name('liff.user.detail');
});

