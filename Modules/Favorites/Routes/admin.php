<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Favorites Module
|--------------------------------------------------------------------------
|
| Routes for favorites administrative functionality
| Middleware: auth, admin permissions
*/

use Illuminate\Support\Facades\Route;
use Modules\Favorites\Http\Controllers\FavoritesAdminController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:admin'],
    'as' => 'admin.'
], function () {
    
    // Favorites Management
    Route::prefix('favorites')->group(function () {
        Route::get('shipments', [FavoritesAdminController::class, 'shipments'])->name('favorites.shipments.index');
        Route::get('customers', [FavoritesAdminController::class, 'customers'])->name('favorites.customers.index');
        Route::get('products', [FavoritesAdminController::class, 'products'])->name('favorites.products.index');
        Route::get('saved', [FavoritesAdminController::class, 'saved'])->name('favorites.saved.index');
        
        // API endpoints for favorite actions
        Route::post('toggle', [FavoritesAdminController::class, 'toggle'])->name('favorites.toggle');
        Route::delete('remove/{id}', [FavoritesAdminController::class, 'remove'])->name('favorites.remove');
        Route::put('notes/{id}', [FavoritesAdminController::class, 'updateNotes'])->name('favorites.update-notes');
    });
    
}); 