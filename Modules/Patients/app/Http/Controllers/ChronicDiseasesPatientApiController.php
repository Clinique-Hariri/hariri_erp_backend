<?php

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Modules\Patients\Http\Requests\ChronicDiseasePatient\StoreChronicDiseasePatientRequest;
use Modules\Patients\Http\Requests\ChronicDiseasePatient\UpdateChronicDiseasePatientRequest;
use Modules\Patients\Http\Resources\ChronicDiseasePatientResource;
use Modules\Patients\Models\ChronicDiseasePatient;
use Modules\Patients\Models\Patient;
use Throwable;

class ChronicDiseasesPatientApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request, $patientId)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::CHRONIC_DISEASES_PATIENT_VIEW);
    }

    try {
      $model = ChronicDiseasePatient::with(['chronicDisease'])
        ->where('patient_id', $patientId);

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      $allowedSorts = ['id', 'created_at'];

      $model->when(
        in_array($request->get('sort_by'), $allowedSorts),
        fn($q) => $q->orderBy($request->get('sort_by'), $request->get('sort_order', 'desc')),
        fn($q) => $q->latest()
      );

      if (request()->boolean('paginate')) {
        $model = $model->paginate($request->get('per_page', 10));
      } else {
        $model = $model->get();
      }

      return $this->successResponse(
        data: ChronicDiseasePatientResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($patientId, $id)
  {
    $this->authorizePermission(PermissionNames::CHRONIC_DISEASES_PATIENT_VIEW);

    try {
      $model = ChronicDiseasePatient::with(['chronicDisease'])
        ->where('patient_id', $patientId)
        ->where('id', $id)
        ->firstOrFail();

      return $this->successResponse(
        data: new ChronicDiseasePatientResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request, $patientId)
  {

    $this->authorizePermission(PermissionNames::CHRONIC_DISEASES_PATIENT_CREATE);
    $data = $this->validateRequest($request, StoreChronicDiseasePatientRequest::rules());

    try {
      $patient = Patient::findOrFail($patientId);

      //unique check for chronic disease and patient
      $existing = ChronicDiseasePatient::where('chronic_disease_id', $data['chronic_disease_id'])
        ->where('patient_id', $patientId)
        ->exists();

      if ($existing) {
        return $this->errorResponse('This chronic disease is already assigned to the patient.', 422);
      }

      $model = $patient->chronicDiseases()->create($data);

      return $this->successResponse(
        data: new ChronicDiseasePatientResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $patientId, $id)
  {

    $this->authorizePermission(PermissionNames::CHRONIC_DISEASES_PATIENT_UPDATE);
    $data = $this->validateRequest($request, UpdateChronicDiseasePatientRequest::rules());

    try {
      $model = ChronicDiseasePatient::where('patient_id', $patientId)
        ->where('id', $id)
        ->firstOrFail();

      $model->update($data);

      return $this->successResponse(
        data: new ChronicDiseasePatientResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($patientId, $id)
  {
    $this->authorizePermission(PermissionNames::CHRONIC_DISEASES_PATIENT_DELETE);

    try {
      $model = ChronicDiseasePatient::where('patient_id', $patientId)
        ->where('id', $id)
        ->firstOrFail();

      $model->delete();

      return $this->successResponse(
        data: [
          'total' => ChronicDiseasePatient::count()
        ]
      );
    } catch (QueryException $e) {
      // MySQL foreign key violation code = 23000
      if ($e->getCode() == 23000) {
        return response()->json([
          'success' => false,
          'message' => __('messages.cannot_delete_record_linked_to_other_records')
        ], 400);
      }

      // Fallback for any other database error
      return response()->json([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
      ], 500);
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
