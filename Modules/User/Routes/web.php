<?php

// use Modules\User\Http\Controllers\PassportAuthController;
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

// echo 'route module user';

// ============================== Public User Routes ============================== //

Route::prefix('user')->group(function () {
    Route::post('/register', 'UserController@register');
    Route::post('/update_profile', 'UserController@update_profile');
});

// ============================== Public API Routes ============================== //

Route::prefix('api')->group(function () {
    Route::post('/register', 'PassportAuthController@register')->name('api.register');
    Route::post('/login', 'PassportAuthController@login')->name('api.login');
});

// Note: Admin routes have been moved to Routes/admin.php for better organization

/*
|--------------------------------------------------------------------------
| Web Routes - Authentication
|--------------------------------------------------------------------------
|
| Centralized authentication routes for all user types
| These routes handle login/logout for the entire application
*/

use Modules\User\Http\Controllers\LoginController;

// Authentication Routes (Public)
Route::group(['middleware' => 'guest'], function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');
});

// Logout Route (Authenticated)
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Password Reset Routes (if needed)
Route::get('password/reset', [LoginController::class, 'showLinkRequestForm'])->name('password.request');
