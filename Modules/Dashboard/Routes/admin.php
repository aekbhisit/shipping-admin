<?php

/*
|--------------------------------------------------------------------------
| Admin Routes - Dashboard Module
|--------------------------------------------------------------------------
|
| Routes for dashboard administrative functionality
| Middleware: auth, admin permissions
*/

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\DashboardAdminController;
use Modules\Dashboard\Http\Controllers\DashboardController;
use Modules\Dashboard\Http\Controllers\ChartController;

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth:admin'],
    'as' => 'admin.'
], function () {
    
    // Homepage route (redirect to dashboard)
    Route::get('homepage', function() {
        return redirect()->route('admin.dashboard.index');
    })->name('homepage');
    
    // Dashboard Administration
    Route::get('dashboard', [DashboardAdminController::class, 'index'])->name('dashboard.index');
    Route::get('dashboard/stats', [DashboardAdminController::class, 'getStats'])->name('dashboard.stats');
    Route::get('dashboard/reports', [DashboardAdminController::class, 'reports'])->name('dashboard.reports');
    
    // Dashboard API Endpoints for ShipCentral Analytics
    Route::get('dashboard/shipments-overview', [DashboardAdminController::class, 'shipmentsOverview'])->name('dashboard.shipments-overview');
    Route::get('dashboard/revenue-analytics', [DashboardAdminController::class, 'revenueAnalytics'])->name('dashboard.revenue-analytics');
    Route::get('dashboard/branch-performance', [DashboardAdminController::class, 'branchPerformance'])->name('dashboard.branch-performance');
    Route::get('dashboard/carrier-usage', [DashboardAdminController::class, 'carrierUsage'])->name('dashboard.carrier-usage');
    Route::get('dashboard/recent-activities', [DashboardAdminController::class, 'recentActivities'])->name('dashboard.recent-activities');
    Route::get('dashboard/shipment-status', [DashboardAdminController::class, 'shipmentStatus'])->name('dashboard.shipment-status');
    
    // Dashboard Management
    Route::resource('dashboards', DashboardController::class);
    
    // Charts and Analytics
    Route::get('charts', [ChartController::class, 'index'])->name('charts.index');
    Route::get('charts/data/{type}', [ChartController::class, 'getData'])->name('charts.data');
    Route::post('charts/generate', [ChartController::class, 'generate'])->name('charts.generate');
    
}); 