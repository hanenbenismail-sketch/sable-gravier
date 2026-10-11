<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\TruckController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::resource('sites', SiteController::class);
Route::resource('products', ProductController::class);

Route::resource('stocks', StockController::class)
    ->only(['index', 'create', 'store']);

Route::get('stocks/remove', [StockController::class, 'remove'])
    ->name('stocks.remove');

Route::post('stocks/remove', [StockController::class, 'destroy'])
    ->name('stocks.destroy');

Route::get('stock-movements', [StockMovementController::class, 'index'])
    ->name('stock-movements.index');

Route::resource('clients', ClientController::class);
Route::resource('trucks', TruckController::class);
Route::resource('drivers', DriverController::class);

// Charger un arrêt de mission.
Route::post(
    'missions/{mission}/stops/{stop}/load',
    [MissionController::class, 'loadStop']
)->name('missions.stops.load');

Route::resource('missions', MissionController::class);
