<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\InventoryController;

Route::post('/payments/register-urls', [MpesaController::class, 'registerUrls']);
Route::post('/search-payments', [POSController::class, 'searchPayments']);

// Inventory Management API Routes
Route::middleware(['auth'])->group(function () {
    Route::prefix('inventory')->group(function () {
        Route::get('/status', [InventoryController::class, 'getInventoryStatus']);
        Route::post('/update-stock/{product}', [InventoryController::class, 'updateStock']);
        Route::post('/toggle-availability/{product}', [InventoryController::class, 'toggleAvailability']);
        Route::post('/bulk-update', [InventoryController::class, 'bulkUpdateStock']);
    });
});
