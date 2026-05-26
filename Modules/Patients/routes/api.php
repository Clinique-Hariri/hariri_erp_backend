<?php

use App\Constants\BloodType;
use App\Constants\Gender;
use Illuminate\Support\Facades\Route;
use Modules\Patients\Constants\CheckupAnalysisStatus;
use Modules\Patients\Constants\CheckupStatus;
use Modules\Patients\Constants\HospitalizationStatus;
use Modules\Patients\Constants\OperationStatus;
use Modules\Patients\Http\Controllers\AnalysesApiController;
use Modules\Patients\Http\Controllers\CheckupAnalysesApiController;
use Modules\Patients\Http\Controllers\CheckupsApiController;
use Modules\Patients\Http\Controllers\ChronicDiseasesPatientApiController;
use Modules\Patients\Http\Controllers\HospitalizationsApiController;
use Modules\Patients\Http\Controllers\OperationApiController;
use Modules\Patients\Http\Controllers\PatientAnalysesApiController;
use Modules\Patients\Http\Controllers\PatientPrescriptionsApiController;
use Modules\Patients\Http\Controllers\PatientsApiController;
use Modules\Patients\Http\Controllers\PrescriptionsApiController;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
  Route::group(['prefix' => 'patients/{patient_id}'], function () {
    Route::put('status', [PatientsApiController::class, 'updateStatus'])->name('patients.updateStatus');
    Route::apiResource('chronic-diseases', ChronicDiseasesPatientApiController::class);
    Route::apiResource('prescriptions', PatientPrescriptionsApiController::class)->only(['index', 'show']);
    Route::apiResource('analyses', PatientAnalysesApiController::class)->only(['index', 'show']);
  });
  Route::apiResource('patients', PatientsApiController::class);

  Route::post('patients-multi-destroy', [PatientsApiController::class, 'multiDestroy']);

  Route::put('analyses/{id}/status', [AnalysesApiController::class, 'updateStatus'])->name('analyses.updateStatus');
  Route::post('analyses/send-result-notification', [AnalysesApiController::class, 'sendResultNotification'])->name('analyses.sendResultNotification');
  Route::put('analyses/{id}/update-attachment', [AnalysesApiController::class, 'updateAttachment'])->name('analyses.updateAttachment');
  Route::apiResource('analyses', AnalysesApiController::class)->except(['store', 'update']);
  Route::post('analyses-multi-destroy', [AnalysesApiController::class, 'multiDestroy']);

  Route::group(['prefix' => 'checkups/{checkupId}'], function () {
    Route::put('status', [CheckupsApiController::class, 'updateStatus'])->name('checkups.updateStatus');
    Route::put('add-orientation', [CheckupsApiController::class, 'addOrientation'])->name('checkups.addOrientation');
    Route::apiResource('prescriptions', PrescriptionsApiController::class);

    Route::put('analyses/{id}/status', [CheckupAnalysesApiController::class, 'updateStatus'])->name('analyses.updateStatus');
    Route::put('analyses/{analysesId}/add-interpretation', [CheckupAnalysesApiController::class, 'addInterpretation']);
    Route::apiResource('analyses', CheckupAnalysesApiController::class);
    Route::post('analyses-multi-destroy', [CheckupAnalysesApiController::class, 'multiDestroy']);
  });
  Route::apiResource('checkups', CheckupsApiController::class);
  Route::post('checkups-multi-destroy', [CheckupsApiController::class, 'multiDestroy']);

  Route::apiResource('hospitalizations', HospitalizationsApiController::class);
//  Route::put('hospitalizations/{id}/extend-stay', [HospitalizationsApiController::class, 'extendStay'])->name('hospitalizations.extendStay');
//  Route::post('hospitalizations/{id}/pay', [HospitalizationsApiController::class, 'pay'])->name('hospitalizations.pay');
  Route::put('hospitalizations/{id}/status', [HospitalizationsApiController::class, 'updateStatus'])->name('hospitalizations.updateStatus');
  Route::post('hospitalizations-multi-destroy', [HospitalizationsApiController::class, 'multiDestroy']);

  Route::apiResource('operations', OperationApiController::class);
  Route::put('operations/{id}/status', [OperationApiController::class, 'updateStatus'])->name('operations.updateStatus');
  Route::post('operations-multi-destroy', [OperationApiController::class, 'multiDestroy']);


  Route::get('constants/operations', function () {
      return response()->json(data: OperationStatus::all());
  })->name('constants.operations');

  Route::get('constants/hospitalizations', function () {
      return response()->json(data: HospitalizationStatus::all(true));
  })->name('constants.hospitalizations');

  Route::get('constants/checkup-statuses', function () {
      return response()->json(data: CheckupStatus::all(true));
  })->name('constants.checkupStatuses');

  Route::get('constants/checkup-service-statuses', function () {
      return response()->json(data: CheckupAnalysisStatus::all(true));
  })->name('constants.checkupAnalysisStatuses');

  Route::get('constants/gender', function () {
      return response()->json(data: Gender::all(true));
  })->name('constants.gender');

  Route::get('constants/blood-types', function () {
      return response()->json(data: BloodType::all(true));
  })->name('constants.bloodTypes');



});
