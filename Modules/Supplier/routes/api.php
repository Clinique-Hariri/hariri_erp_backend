<?php

use Illuminate\Support\Facades\Route;
use Modules\Supplier\Http\Controllers\SupplierApiController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::prefix('suppliers')->group(function () {
        Route::get('/', [SupplierApiController::class, 'index']);
        Route::post('/', [SupplierApiController::class, 'store']);
        Route::get('/{id}', [SupplierApiController::class, 'show']);
        Route::put('/{id}', [SupplierApiController::class, 'update']);
        Route::delete('/{id}', [SupplierApiController::class, 'destroy']);

        Route::post('career-changes-multi-update-status', [SupplierApiController::class, 'updateStatus']);
      });
      Route::post('suppliers-multi-destroy', [SupplierApiController::class, 'multiDestroy']);


});

