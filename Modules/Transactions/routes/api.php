<?php

use Illuminate\Support\Facades\Route;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\Type;
use Modules\Transactions\Http\Controllers\TransactionsApiController;


Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
  Route::get('transactions/stats', [TransactionsApiController::class, 'stats']);
  Route::apiResource('transactions', TransactionsApiController::class)->except(['destroy']);


  Route::get('constants/transaction-types ', function () {
    return response()->json(data: Type::all(true));
  })->name('constants.transactionTypes');

  Route::get('constants/transaction-statuses', function () {
    return response()->json(data: Status::all(true));
  })->name('constants.transactionStatuses');
});
