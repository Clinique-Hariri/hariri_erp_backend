<?php

use Illuminate\Support\Facades\Route;
use Modules\Clinic\app\Http\Controllers\DoctorTransactionsApiController;
use Modules\Clinic\Http\Controllers\DoctorsApiController;
use Modules\Clinic\Http\Controllers\SchedulesController;
use Modules\Clinic\Http\Controllers\SpecialitiesApiController;
Route::group(['prefix' => 'v1'], function () {
  Route::group(['prefix' => 'doctors/{doctorId}'], function () {
    Route::get('next-checkup', [DoctorsApiController::class, 'nextCheckup'])->name('doctors.next-checkup');
  });
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {

  Route::apiResource('specialities', SpecialitiesApiController::class);
  Route::get('doctors/statistics', [DoctorsApiController::class, 'statistics'])->name('doctors.statistics');
  Route::apiResource('doctors', DoctorsApiController::class)->only(['index', 'show']);
  Route::group(['prefix' => 'doctors/{doctorId}'], function () {
    Route::get('checkup-stats', [DoctorsApiController::class, 'checkupStats'])->name('doctors.checkup-stats');
    Route::apiResource('schedules', SchedulesController::class)->except(['update']);
    Route::apiResource('transactions', DoctorTransactionsApiController::class)->only(['index', 'show']);
    Route::post('transactions/{transaction_id}/update-status', [DoctorTransactionsApiController::class, 'updateStatus'])->name('doctors.transactions.update-status');
    Route::post('transactions/multi-update-status', [DoctorTransactionsApiController::class, 'multiUpdateStatus'])->name('doctors.transactions.multi-update-status');
  });

  Route::get('constants/week-days', function () {
    return response()->json([
      'data' => \App\Support\Enum\WeekDays::lists(true)
    ]);
  });
});
