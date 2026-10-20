<?php

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Support\Enum\UserRoles;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Clinic\Models\Doctor;
use Modules\HRM\Models\Employee;
use Modules\Patients\Constants\CheckupStatus;
use Modules\Patients\Constants\HomeCheckupStatus;
use Modules\Patients\Constants\HomeCheckupVisitType;
use Modules\Patients\Http\Requests\HomeCheckup\StoreHomeCheckupRequest;
use Modules\Patients\Http\Requests\HomeCheckup\UpdateHomeCheckupRequest;
use Modules\Patients\Http\Requests\HomeCheckup\UpdateHomeCheckupStatusRequest;
use Modules\Patients\Http\Resources\HomeCheckupResource;
use Modules\HRM\Http\Resources\EmployeeMiniResource;
use Modules\Patients\Models\Checkup;
use Modules\Patients\Models\HomeCheckup;
use Modules\Patients\Models\Patient;
use Throwable;

class HomeCheckupsApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::HOME_CHECKUPS_VIEW);
    }

    try {
      $model = HomeCheckup::with(['patient', 'doctor.user.employee', 'services', 'nurses', 'checkup']);

      if ($request->has('search') && $request->get('search') !== null) {
        $search = $request->get('search');
        $model = $model->where(function ($query) use ($search) {
          $query->where('patient_fullname', 'like', '%' . $search . '%')
            ->orWhere('patient_phone', 'like', '%' . $search . '%')
            ->orWhere('home_checkup_number', 'like', '%' . $search . '%')
            ->orWhere('mobile_request_id', 'like', '%' . $search . '%');
        });
      }

      if ($request->filled('status')) {
        $status = is_array($request->status) ? $request->status : [$request->status];
        $model = $model->whereIn('status', $status);
      }

      if ($request->filled('visit_type')) {
        $model->where('visit_type', $request->get('visit_type'));
      }

      if ($request->filled('doctor_id')) {
        $model->where('doctor_id', $request->get('doctor_id'));
      }

      if ($request->filled('patient_id')) {
        $model->where('patient_id', $request->get('patient_id'));
      }

      if ($request->filled('nurse_id')) {
        $model->whereHas('nurses', fn($query) => $query->whereKey($request->get('nurse_id')));
      }

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      $allowedSorts = ['id', 'scheduled_at', 'status', 'total_price', 'created_at'];

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
        data: HomeCheckupResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission(PermissionNames::HOME_CHECKUPS_VIEW);

    try {
      $model = HomeCheckup::with(['patient', 'doctor.user.employee', 'services', 'nurses', 'checkup'])
        ->findOrFail($id);

      return $this->successResponse(
        data: new HomeCheckupResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNames::HOME_CHECKUPS_CREATE);

    $data = $this->validateRequest($request, StoreHomeCheckupRequest::rules());

    try {
      $data['status'] = HomeCheckupStatus::SCHEDULED;

      $model = DB::transaction(function () use ($data) {
        $services = $data['services'] ?? [];
        $nurseIds = $data['nurse_ids'] ?? [];

        unset($data['services'], $data['nurse_ids']);

        $model = HomeCheckup::create($data);

        foreach ($services as $service) {
          $model->services()->create([
            'service_name' => $service['service_name'],
            'service_price' => $service['service_price'],
          ]);
        }

        if (!empty($nurseIds)) {
          $model->nurses()->sync($nurseIds);
        }

        $model->load(['patient', 'doctor.user.employee', 'services', 'nurses']);

        return $model;
      });

      return $this->successResponse(
        data: new HomeCheckupResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::HOME_CHECKUPS_UPDATE);

    $data = $this->validateRequest($request, UpdateHomeCheckupRequest::rules());

    try {
      $model = HomeCheckup::findOrFail($id);

      DB::transaction(function () use ($model, $data) {
        $services = $data['services'] ?? null;
        $nurseIds = $data['nurse_ids'] ?? null;

        unset($data['services'], $data['nurse_ids']);

        $model->update($data);

        if (is_array($services)) {
          $model->services()->delete();
          foreach ($services as $service) {
            $model->services()->create([
              'service_name' => $service['service_name'],
              'service_price' => $service['service_price'],
            ]);
          }
        }

        if (is_array($nurseIds)) {
          $model->nurses()->sync($nurseIds);
        }
      });

      $model->load(['patient', 'doctor.user.employee', 'services', 'nurses', 'checkup']);

      return $this->successResponse(
        data: new HomeCheckupResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function updateStatus(Request $request, $id)
  {
    $data = $this->validateRequest($request, UpdateHomeCheckupStatusRequest::rules());
    $newStatus = $data['status'];

    try {
      match ($newStatus) {
        HomeCheckupStatus::IN_PROGRESS => $this->authorizePermission(PermissionNames::HOME_CHECKUPS_UPDATE_TO_IN_PROGRESS),
        HomeCheckupStatus::COMPLETED   => $this->authorizePermission(PermissionNames::HOME_CHECKUPS_UPDATE_TO_COMPLETED),
        HomeCheckupStatus::CANCELLED   => $this->authorizePermission(PermissionNames::HOME_CHECKUPS_UPDATE_TO_CANCELLED),
        default                        => throw new \InvalidArgumentException("Invalid status: {$newStatus}"),
      };

      $model = HomeCheckup::with(['doctor.user.employee', 'patient'])->findOrFail($id);

      $model->update(['status' => $newStatus]);

      $model->refresh()->load(['patient', 'doctor.user.employee', 'services', 'nurses', 'checkup']);

      return $this->successResponse(
        data: new HomeCheckupResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::HOME_CHECKUPS_DELETE);

    try {
      $model = HomeCheckup::findOrFail($id);
      $model->delete();

      return $this->successResponse(
        data: [
          'total' => HomeCheckup::count()
        ]
      );
    } catch (QueryException $e) {
      if ($e->getCode() == 23000) {
        return response()->json([
          'success' => false,
          'message' => __('messages.cannot_delete_record_linked_to_other_records')
        ], 400);
      }

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
    $this->authorizePermission(PermissionNames::HOME_CHECKUPS_DELETE);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array',
      'ids.*' => 'required|exists:home_checkups,id'
    ]);

    try {
      HomeCheckup::whereIn('id', $data['ids'])->delete();

      return $this->successResponse(
        data: [
          'total' => HomeCheckup::count()
        ]
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * List employees that hold the "nurse" role, for nurse assignment.
   */
  public function availableNurses(Request $request)
  {
    $this->authorizePermission(PermissionNames::HOME_CHECKUPS_VIEW);

    try {
      $nurses = Employee::whereHas('user', function ($query) {
        $query->role(UserRoles::NURSE);
      })->get();

      return $this->successResponse(
        data: EmployeeMiniResource::collection($nurses)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
