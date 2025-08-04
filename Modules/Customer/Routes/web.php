<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\CustomerController;
use Modules\Customer\Http\Controllers\SenderController;
use Modules\Customer\Http\Controllers\ReceiverController;

/*
|--------------------------------------------------------------------------
| Customer Module Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Customer module.
| These routes are loaded by the RouteServiceProvider.
|
*/

// Public routes for customer search and selection
Route::prefix('customers')->name('customers.')->group(function () {
    Route::get('/search', [CustomerController::class, 'search'])->name('search');
    Route::get('/autocomplete', [CustomerController::class, 'autocomplete'])->name('autocomplete');
    Route::get('/recent', [CustomerController::class, 'recent'])->name('recent');
    Route::get('/{id}/select', [CustomerController::class, 'select'])->name('select');
    Route::get('/{id}/details', [CustomerController::class, 'getDetails'])->name('details');
    Route::post('/quick-add', [CustomerController::class, 'quickAdd'])->name('quick-add');
});

// Sender routes
Route::prefix('senders')->name('senders.')->group(function () {
    Route::get('/search', [SenderController::class, 'search'])->name('search');
    Route::get('/{customerId}/{senderId}/addresses', [SenderController::class, 'getAddresses'])->name('addresses');
    Route::post('/{customerId}/{senderId}/addresses', [SenderController::class, 'addAddress'])->name('add-address');
    Route::put('/{customerId}/{senderId}/addresses/{addressId}/default', [SenderController::class, 'selectAddress'])->name('select-address');
});

// Receiver routes
Route::prefix('receivers')->name('receivers.')->group(function () {
    Route::get('/search', [ReceiverController::class, 'search'])->name('search');
    Route::get('/frequent', [ReceiverController::class, 'frequent'])->name('frequent');
    Route::get('/{id}/details', [ReceiverController::class, 'getDetails'])->name('details');
    Route::post('/quick-add', [ReceiverController::class, 'quickAdd'])->name('quick-add');
}); 