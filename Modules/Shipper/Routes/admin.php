<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Shipper Module
|--------------------------------------------------------------------------
|
| Routes for shipping carrier administrative functionality
| Middleware: auth, admin permissions
*/

use Illuminate\Support\Facades\Route;
use Modules\Shipper\Http\Controllers\CarrierAdminController;
use Modules\Shipper\Http\Controllers\ShipperAdminController;
use Modules\Shipper\Http\Controllers\ShipperApiController;
use Modules\Shipper\Http\Controllers\RateController;
use Modules\Shipper\Http\Controllers\LabelController;
use Modules\Shipper\Http\Controllers\QuoteApiController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:admin', 'adminAccessControl'],
    'as' => 'admin.'
], function () {
    
    // ========================================
    // CARRIER ADMINISTRATION
    // ========================================
    
    // Carrier Management (CarrierAdminController)
    Route::resource('carriers', 'CarrierAdminController');
    
    // Carrier Configuration
    Route::get('carriers/{carrier}/config', 'CarrierAdminController@configuration')
        ->name('carriers.config');
    
    Route::post('carriers/{carrier}/config', 'CarrierAdminController@updateConfiguration')
        ->name('carriers.config.update');
    
    // Carrier Test Connection
    Route::post('carriers/{carrier}/test-connection', 'CarrierAdminController@testConnection')
        ->name('carriers.test-connection');
    
    // Carrier Status Toggle
    Route::post('carriers/{carrier}/toggle-status', 'CarrierAdminController@toggleStatus')
        ->name('carriers.toggle-status');
    
    // ========================================
    // SHIPPER ADMINISTRATION
    // ========================================
    
    // Shipper DataTable AJAX (MUST BE BEFORE RESOURCE ROUTE)
    Route::get('shippers/datatable_ajax', 'ShipperAdminController@datatable_ajax')
        ->name('shippers.datatable_ajax');
    
    // Shipper API Logs
    Route::get('shippers/logs', 'ShipperAdminController@viewLogs')
        ->name('shippers.logs');
    
    // Shipper Status Toggle
    Route::post('shippers/set-status', 'ShipperAdminController@setStatus')
        ->name('shippers.set-status');
    
    // Shipper Activate/Deactivate
    Route::post('shippers/{shipper}/activate', 'ShipperAdminController@activate')
        ->name('shippers.activate');
    
    Route::post('shippers/{shipper}/deactivate', 'ShipperAdminController@deactivate')
        ->name('shippers.deactivate');
    
    // Shipper Test Connection
    Route::post('shippers/{shipper}/test-connection', 'ShipperAdminController@testConnection')
        ->name('shippers.test-connection');
    
    // Shipper Configuration
    Route::get('shippers/{shipper}/config', 'ShipperAdminController@configuration')
        ->name('shippers.config');
    
    Route::post('shippers/{shipper}/config', 'ShipperAdminController@updateConfiguration')
        ->name('shippers.config.update');
    
    // Shipper Management (ShipperAdminController) - MUST BE AFTER SPECIFIC ROUTES
    Route::resource('shippers', 'ShipperAdminController');
    
    // Shipper API Logs (with parameter)
    Route::get('shippers/{shipper}/logs', 'ShipperAdminController@viewLogs')
        ->name('shippers.carrier-logs');
    
    // ========================================
    // INTERNAL API ROUTES
    // ========================================
    
    // Shipper API (Internal)
    Route::prefix('api/shipper')->name('shipper-api.')->group(function() {
        Route::post('quotes', 'ShipperApiController@getQuotes')->name('quotes');
        Route::post('shipments', 'ShipperApiController@createShipment')->name('shipments');
        Route::post('labels', 'ShipperApiController@generateLabel')->name('labels');
        Route::post('tracking', 'ShipperApiController@trackShipment')->name('tracking');
        Route::post('pickup', 'ShipperApiController@schedulePickup')->name('pickup');
        Route::post('webhook', 'ShipperApiController@handleWebhook')->name('webhook');
    });
    
    // Rate Management (Internal)
    Route::prefix('api/rates')->name('rates.')->group(function() {
        Route::post('compare', 'RateController@compareRates')->name('compare');
        Route::post('markup', 'RateController@applyMarkup')->name('markup');
        Route::get('cached', 'RateController@getCachedRates')->name('cached');
        Route::post('refresh', 'RateController@refreshRates')->name('refresh');
        Route::post('calculate', 'RateController@calculateFinalPrice')->name('calculate');
    });
    
    // ========================================
    // LABEL MANAGEMENT
    // ========================================
    
    Route::get('labels', 'LabelController@index')
        ->name('labels.index');
    
    Route::post('labels/print', 'LabelController@print')
        ->name('labels.print');
    
    Route::get('labels/{label}/download', 'LabelController@download')
        ->name('labels.download');
    
    Route::post('labels/regenerate', 'LabelController@regenerate')
        ->name('labels.regenerate');
    
    // ========================================
    // QUOTE MANAGEMENT
    // ========================================
    
    Route::get('quotes', 'QuoteApiController@index')
        ->name('quotes.index');
    
    Route::post('quotes/test', 'QuoteApiController@test')
        ->name('quotes.test');
    
    // ========================================
    // BULK OPERATIONS
    // ========================================
    
    Route::post('carriers/bulk-update', 'CarrierAdminController@bulkUpdate')
        ->name('carriers.bulk-update');
    
}); 