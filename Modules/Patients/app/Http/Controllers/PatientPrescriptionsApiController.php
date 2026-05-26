<?php

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Patients\Http\Requests\ChronicDiseasePatient\StoreChronicDiseasePatientRequest;
use Modules\Patients\Http\Requests\ChronicDiseasePatient\UpdateChronicDiseasePatientRequest;
use Modules\Patients\Http\Requests\Prescription\StorePrescriptionRequest;
use Modules\Patients\Http\Requests\Prescription\UpdatePrescriptionRequest;
use Modules\Patients\Http\Resources\ChronicDiseasePatientResource;
use Modules\Patients\Http\Resources\PrescriptionResource;
use Modules\Patients\Models\Checkup;
use Modules\Patients\Models\ChronicDiseasePatient;
use Modules\Patients\Models\Prescription;
use Throwable;

class PatientPrescriptionsApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request, $patientId)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission([
        PermissionNames::PRESCRIPTIONS_VIEW,
        PermissionNames::CHECKUPS_DOCTOR_VIEW,
      ]);
    }

    try {
      $model = Prescription::with(['medicines', 'checkup.patient.insuranceSocietyBranch.insuranceSociety', 'doctor.user.employee'])->whereHas('checkup', function ($query) use ($patientId) {
        $query->where('patient_id', $patientId);
      })->orderBy('created_at', 'desc');

      if (request()->boolean('paginate')) {
        $model = $model->paginate($request->get('per_page', 10));
      } else {
        $model = $model->get();
      }

      return $this->successResponse(
        data: PrescriptionResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($patientId, $id)
  {
    $this->authorizePermission([
      PermissionNames::PRESCRIPTIONS_VIEW,
      PermissionNames::CHECKUPS_DOCTOR_VIEW,
    ]);

    try {
      $model = Prescription::with(['medicines', 'checkup.patient.insuranceSocietyBranch.insuranceSociety', 'doctor.user.employee'])->whereHas('checkup', function ($query) use ($patientId) {
        $query->where('patient_id', $patientId);
      })
        ->where('id', $id)
        ->firstOrFail();

      return $this->successResponse(
        data: new PrescriptionResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
