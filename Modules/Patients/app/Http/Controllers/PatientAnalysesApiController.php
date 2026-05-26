<?php

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\MedicalReferences\Models\MedicalService;
use Modules\Patients\Constants\CheckupAnalysisStatus;
use Modules\Patients\Http\Requests\CheckupAnalysis\StoreCheckupAnalysisRequest;
use Modules\Patients\Http\Requests\CheckupAnalysis\UpdateCheckupAnalysisRequest;
use Modules\Patients\Http\Requests\CheckupAnalysis\UpdateCheckupAnalysisStatusRequest;
use Modules\Patients\Http\Resources\CheckupAnalysisResource;
use Modules\Patients\Models\Checkup;
use Modules\Patients\Models\CheckupAnalysis;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\Type;
use Throwable;

class PatientAnalysesApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request, $patientId)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission([
        PermissionNames::CHECKUP_SERVICES_VIEW,
        PermissionNames::CHECKUP_RADIOLIGY_VIEW,
      ]);
    }

    try {
      $model = CheckupAnalysis::with(['services', 'checkup.patient', 'checkup.doctor'])->whereHas('checkup', function ($query) use ($patientId) {
        $query->where('patient_id', $patientId);
      })->orderBy('created_at', 'desc');

      if ($request->filled('doctor_id')) {
        $doctorId = is_array($request->doctor_id) ? $request->doctor_id : [$request->doctor_id];
        $model->whereHas('checkup', function ($query) use ($doctorId) {
          $query->whereIn('doctor_id', $doctorId);
        });
      }

      if ($request->filled('patient_id')) {
        $model->whereHas('checkup', function ($query) use ($request) {
          $query->where('patient_id', $request->patient_id);
        });
      }

      if ($request->filled('status')) {
        $status = is_array($request->status) ? $request->status : [$request->status];
        $model->whereIn('status', $status);
      }

      if (request()->boolean('paginate')) {
        $model = $model->paginate($request->get('per_page', 10));
      } else {
        $model = $model->get();
      }

      return $this->successResponse(
        data: CheckupAnalysisResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($patientId, $id)
  {
    $this->authorizePermission([
      PermissionNames::CHECKUP_SERVICES_VIEW,
      PermissionNames::CHECKUP_RADIOLIGY_VIEW,
    ]);

    try {
      $model = CheckupAnalysis::with(['services', 'checkup.patient', 'checkup.doctor'])
        ->whereHas('checkup', function ($query) use ($patientId) {
          $query->where('patient_id', $patientId);
        })
        ->where('id', $id)
        ->firstOrFail();

      return $this->successResponse(
        data: new CheckupAnalysisResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
