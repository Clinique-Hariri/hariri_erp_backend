<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\InventoryItem\InventoryItemController;
use Modules\Inventory\Http\Controllers\InventoryTransaction\InventoryTransactionController;
use Modules\Inventory\Http\Controllers\ItemsCategory\ItemsCategoryApiController;
use Modules\Inventory\Http\Controllers\SupplyRequest\SupplyRequestController;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::prefix('inventory-categories')->group(function () {
        Route::get('/', [ItemsCategoryApiController::class, 'index']);
        Route::post('/', [ItemsCategoryApiController::class, 'store']);
        Route::get('/{id}', [ItemsCategoryApiController::class, 'show']);
        Route::put('/{id}', [ItemsCategoryApiController::class, 'update']);
        Route::delete('/{id}', [ItemsCategoryApiController::class, 'destroy']);
    });

    Route::prefix('inventory-items')->group(function () {
        Route::get('/', [InventoryItemController::class, 'index']);
        Route::post('/', [InventoryItemController::class, 'store']);
        Route::get('/{id}', [InventoryItemController::class, 'show']);
        Route::put('/{id}', [InventoryItemController::class, 'update']);
        Route::delete('/{id}', [InventoryItemController::class, 'destroy']);

        Route::get('/all/items', [InventoryItemController::class, 'all']);
    });

    // Route::apiResource('/stock-alerts', StockAlertController::class)->only(['index']);
    Route::prefix('purchases')->group(function () {
        Route::get('/', [InventoryTransactionController::class, 'index']);
        Route::post('/', [InventoryTransactionController::class, 'store']);
        Route::get('{id}', [InventoryTransactionController::class, 'show']);
        Route::put('{id}', [InventoryTransactionController::class, 'update']);
        Route::delete('{id}', [InventoryTransactionController::class, 'destroy']);
    });

    // Supply Requests
    Route::prefix('supply-requests')->group(function () {
        Route::get('/', [SupplyRequestController::class, 'index']);
        Route::post('/', [SupplyRequestController::class, 'store']);
        Route::get('/{id}', [SupplyRequestController::class, 'show']);
        Route::put('/{id}', [SupplyRequestController::class, 'update']);
        Route::delete('/{id}', [SupplyRequestController::class, 'destroy']);
        Route::post('/{id}/status', [SupplyRequestController::class, 'updateStatus'])->name('status');
        Route::get('/kpi/statistics', [SupplyRequestController::class, 'kpiStatistics']);
    });
});
