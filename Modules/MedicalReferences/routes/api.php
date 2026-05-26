<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalReferences\Constants\MedicalServiceTypes;
use Modules\MedicalReferences\Http\Controllers\ChronicDiseasesApiController;
use Modules\MedicalReferences\Http\Controllers\InsuranceSocietiesApiController;
use Modules\MedicalReferences\Http\Controllers\InsuranceSocietyBranchesApiController;
use Modules\MedicalReferences\Http\Controllers\InsuranceSocietyCheckupPricingApiController;
use Modules\MedicalReferences\Http\Controllers\InsuranceSocietyMedicalServicesApiController;
use Modules\MedicalReferences\Http\Controllers\InsuranceSocietyTransactionsApiController;
use Modules\MedicalReferences\Http\Controllers\MedicalServiceGroupsApiController;
use Modules\MedicalReferences\Http\Controllers\MedicalServicesApiController;
use Modules\MedicalReferences\Http\Controllers\MedicinesApiController;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
  Route::prefix('insurance-societies/{society_id}')
    ->group(function () {
      Route::apiResource('branches', InsuranceSocietyBranchesApiController::class);
      Route::apiResource('checkup-pricing', InsuranceSocietyCheckupPricingApiController::class);
      Route::apiResource('medical-services', InsuranceSocietyMedicalServicesApiController::class);
      Route::apiResource('transactions', InsuranceSocietyTransactionsApiController::class)->only(['index', 'show']);
      Route::post('transactions/{transaction_id}/update-status', [InsuranceSocietyTransactionsApiController::class, 'updateStatus'])->name('insurance-societies.transactions.update-status');
      Route::post('transactions/multi-update-status', [InsuranceSocietyTransactionsApiController::class, 'multiUpdateStatus'])->name('insurance-societies.transactions.multi-update-status');
    });
  Route::apiResource('insurance-societies', InsuranceSocietiesApiController::class);


  Route::group(['prefix' => 'medical-service-groups/{group_id}'], function () {
    Route::apiResource('medical-services', MedicalServicesApiController::class);
  });
  Route::apiResource('medical-service-groups', MedicalServiceGroupsApiController::class);
  Route::get('medical-services/list-grouped', [MedicalServicesApiController::class, 'listGrouped'])->name('medical-services.list');
  Route::apiResource('medicines', MedicinesApiController::class);
  Route::apiResource('chronic-diseases', ChronicDiseasesApiController::class);

  Route::get('constants/medical-service/types', function () {
    return response()->json(data: MedicalServiceTypes::all(true));
  })->name('constants.operations');
});
