<?php

/*
|--------------------------------------------------------------------------
| Public Routes - Setting Module
|--------------------------------------------------------------------------
|
| Public routes that don't require authentication
| For frontend/public access only
*/

use Illuminate\Support\Facades\Route;

// Public API routes for settings (no authentication required)
Route::prefix('api')->group(function () {
    Route::prefix('web')->group(function () {
        Route::get('/get', 'SettingController@getSetting')->name('api.setting');
        Route::get('/get/meta', 'SettingController@getMeta')->name('api.setting.meta');
        Route::get('/get/tag', 'SettingController@getTag')->name('api.setting.tag');
    });
});
