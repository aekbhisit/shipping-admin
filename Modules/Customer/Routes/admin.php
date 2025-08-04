<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\CustomerAdminController;
use Modules\Customer\Http\Controllers\SenderController;
use Modules\Customer\Http\Controllers\ReceiverController;

/*
|--------------------------------------------------------------------------
| Customer Module Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for the Customer module.
| These routes are loaded by the RouteServiceProvider.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth:admin', 'adminAccessControl', 'HttpAccessLogFile'])->group(function () {
    
    // Customer management routes
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerAdminController::class, 'index'])->name('index');
        Route::get('/create', [CustomerAdminController::class, 'create'])->name('create');
        Route::post('/', [CustomerAdminController::class, 'store'])->name('store');
        
        // Customer search and utilities (must come before {id} routes)
        Route::get('/search/autocomplete', [CustomerAdminController::class, 'search'])->name('search');
        Route::post('/merge', [CustomerAdminController::class, 'merge'])->name('merge');
        Route::get('/datatable', [CustomerAdminController::class, 'getDataTable'])->name('datatable');
        Route::get('/export', [CustomerAdminController::class, 'export'])->name('export');
        
        Route::get('/{id}', [CustomerAdminController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CustomerAdminController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomerAdminController::class, 'update'])->name('update');
        Route::delete('/{id}', [CustomerAdminController::class, 'destroy'])->name('destroy');
        
        // Sender management routes
        Route::prefix('{customerId}/senders')->name('senders.')->group(function () {
            Route::get('/', [SenderController::class, 'index'])->name('index');
            Route::get('/create', [SenderController::class, 'create'])->name('create');
            Route::post('/', [SenderController::class, 'store'])->name('store');
            Route::get('/{senderId}', [SenderController::class, 'show'])->name('show');
            Route::get('/{senderId}/edit', [SenderController::class, 'edit'])->name('edit');
            Route::put('/{senderId}', [SenderController::class, 'update'])->name('update');
            
            // Address management
            Route::post('/{senderId}/addresses', [SenderController::class, 'addAddress'])->name('add-address');
            Route::put('/{senderId}/addresses/{addressId}/default', [SenderController::class, 'selectAddress'])->name('select-address');
        });
    });
    
    // Receiver management routes
    Route::prefix('receivers')->name('receivers.')->group(function () {
        Route::get('/create', [ReceiverController::class, 'create'])->name('create');
        Route::post('/', [ReceiverController::class, 'store'])->name('store');
        Route::get('/{id}', [ReceiverController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ReceiverController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ReceiverController::class, 'update'])->name('update');
    });
}); 