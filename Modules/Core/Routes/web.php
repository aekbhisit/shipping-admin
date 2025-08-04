<?php

/*
|--------------------------------------------------------------------------
| Public Routes - Core Module
|--------------------------------------------------------------------------
|
| Public routes that don't require authentication
| For frontend/public access only
*/

use Illuminate\Support\Facades\Route;

// Public Core API routes (no authentication required)
Route::prefix('core')->group(function() {
    Route::get('/get_province/{type}', 'CoreAdminController@getProvince')->name('core.address.province');
    Route::get('/get_city/{type}/{province_id}', 'CoreAdminController@getCity')->name('core.address.city');
    Route::get('/get_city_full/{type}/{province_id}', 'CoreAdminController@getCityFull')->name('core.address.cityfull');
    Route::get('/get_districe/{type}/{city_id}', 'CoreAdminController@getDistrict')->name('core.address.district');
    Route::get('/get_districe_full/{type}/{city_id}', 'CoreAdminController@getDistrictFull')->name('core.address.districtfull');
    Route::get('/get_address_by_zipcode/{type}/{zipcode}', 'CoreAdminController@getAddressByZipcode')->name('core.address.districtzipcode');
    Route::get('/get_tin/{tax_id}', 'CoreAdminController@getTIN')->name('core.rd.tin');
    Route::post('/multifiles/upload', 'CoreAdminController@multifiles_upload')->name('master.multifiles.upload');
});