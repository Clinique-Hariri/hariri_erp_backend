<?php

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\Patient\UpdatePatientStatusRequest;
use Modules\Patients\Constants\PatientStatus;
use Modules\Patients\Http\Requests\Patient\StorePatientRequest;
use Modules\Patients\Http\Requests\Patient\UpdatePatientRequest;
use Modules\Patients\Http\Resources\CheckupResource;
use Modules\Patients\Http\Resources\PatientResource;
use Modules\Patients\Models\Patient;
use Throwable;

class PatientsApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission([
        PermissionNames::PATIENTS_VIEW,
        PermissionNames::CHECKUPS_DOCTOR_VIEW,
      ]);
    }

    try {
      $model = Patient::query()
        ->filterByInsuranceSociety()
        ->with(['insuranceSocietyBranch.insuranceSociety']);

      if ($request->has('search') && $request->get('search') !== null) {
        $search = $request->get('search');
        $model = $model->where('fullname', 'like', '%' . $search . '%')
          ->orWhere('patient_number', 'like', '%' . $search . '%')
          ->orWhere('id', 'like', '%' . $search . '%');
      }

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      $allowedSorts = ['id', 'fullname', 'patient_number', 'created_at'];

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
        data: PatientResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission([
      PermissionNames::PATIENTS_VIEW,
      PermissionNames::CHECKUPS_DOCTOR_VIEW,
    ]);

    try {
      $model = Patient::with(['insuranceSocietyBranch.insuranceSociety'])->findOrFail($id);

      return $this->successResponse(
        data: new PatientResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNames::PATIENTS_CREATE);

    $data = $this->validateRequest($request, StorePatientRequest::rules());
    $data['status'] = PatientStatus::ACTIVE;
    try {

      //if birthdate is null calculate it from age
      if (empty($data['birthdate']) && !empty($data['age'])) {
        $data['birthdate'] = now()->subYears($data['age'])->format('Y-m-d');
      }

      if ($request->hasFile('avatar')) {
        $data['avatar'] = storeWebP($request->file('avatar'), 'uploads/patients/avatars');
      }

      $model = Patient::create($data);

      return $this->successResponse(
        data: new PatientResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }


  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::PATIENTS_UPDATE);

    $data = $this->validateRequest($request, UpdatePatientRequest::rules($id));

    try {
      $model = Patient::findOrFail($id);

      if ($request->hasFile('avatar')) {
        $data['avatar'] = storeWebP($request->file('avatar'), 'uploads/patients/avatars');
      }

      $model->update($data);

      return $this->successResponse(
        data: new PatientResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function updateStatus(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::PATIENTS_UPDATE);

    $data = $this->validateRequest($request, UpdatePatientStatusRequest::rules());

    try {
      $model = Patient::findOrFail($id);
      $model->update($data);

      return $this->successResponse(
        data: new PatientResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::PATIENTS_DELETE);

    try {
      $model = Patient::findOrFail($id);
      $model->delete();

      return $this->successResponse(
        data: [
          'total' => Patient::count()
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

  public function multiDestroy(Request $request)
  {
    $this->authorizePermission(PermissionNames::PATIENTS_DELETE);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array',
      'ids.*' => 'required|exists:patients,id'
    ]);

    try {

      // Only delete patients that are in pending status
      $pendingPatients = Patient::whereIn('id', $data['ids'])
        ->pluck('id')
        ->toArray();

      Patient::whereIn('id', $pendingPatients)->delete();

      return $this->successResponse(
        data: [
          'total' => Patient::count()
        ]
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
