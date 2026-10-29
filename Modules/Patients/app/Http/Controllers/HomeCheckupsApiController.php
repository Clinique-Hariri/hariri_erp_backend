<?php

namespace Modules\Patients\Http\Controllers;

use App\Constants\BloodType;
use App\Constants\Gender;
use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Support\Enum\UserRoles;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Actions\Constants\ActionType;
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
use Modules\Settings\Constants\SettingsKeys;
use Modules\Settings\Models\Setting;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\TransactionCategory;
use Modules\Transactions\Constants\Type;
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
      $data = $this->resolvePatient($data);

      $data['status'] = HomeCheckupStatus::SCHEDULED;

      $services = $data['services'] ?? [];
      $prices = $this->calculatePrices((float) ($data['base_price'] ?? 0), $services);
      $data['services_total'] = $prices['services_total'];
      $data['total_price'] = $prices['total_price'];

      $model = DB::transaction(function () use ($data, $services) {
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

        $this->spawnDoctorCheckup($model);

        $model->load(['patient', 'doctor.user.employee', 'services', 'nurses', 'checkup.doctor.user.employee', 'checkup.patient', 'checkup.ticket']);

        return $model;
      });

      return $this->successResponse(
        data: new HomeCheckupResource($model)
      );
    } catch (ValidationException $e) {
      return $this->errorResponse($e->getMessage(), 422);
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Resolve the patient for a home checkup, or create a new one from the
   * supplied patient details. Ensures `patient_id` is always set before
   * persisting the home checkup.
   */
  private function resolvePatient(array $data): array
  {
    $patient = null;

    if (!empty($data['patient_id'])) {
      $patient = Patient::findOrFail($data['patient_id']);
    } elseif (!empty($data['patient_number'])) {
      $patient = Patient::where('patient_number', $data['patient_number'])->first();

      if (!$patient) {
        throw ValidationException::withMessages([
          'patient_number' => 'The provided patient number is not linked to any patient.',
        ]);
      }
    } elseif (!empty($data['patient_fullname'])) {
      $patient = Patient::create([
        'fullname' => $data['patient_fullname'],
        'phone' => $data['patient_phone'] ?? null,
        'birthdate' => $data['patient_birthdate'] ?? null,
        'gender' => Gender::default(),
        'blood_type' => BloodType::default(),
      ]);
    } else {
      throw ValidationException::withMessages([
        'patient' => 'Patient details are required. Provide patient_id, patient_number or patient_fullname.',
      ]);
    }

    $data['patient_id'] = $patient->id;
    unset($data['patient_number']);

    return $data;
  }

  /**
   * Calculate the services total and total price for a home checkup.
   */
  private function calculatePrices(float $basePrice, array $services): array
  {
    $servicesTotal = round(
      collect($services)->sum(fn($service) => (float) ($service['service_price'] ?? 0)),
      2
    );

    return [
      'services_total' => $servicesTotal,
      'total_price' => round($basePrice + $servicesTotal, 2),
    ];
  }

  /**
   * Spawn a regular Checkup for a home checkup when a doctor is involved.
   *
   * The spawned checkup is priced with the doctor's `checkup_price` fee,
   * following the same pricing rules applied to regular checkups (insurance
   * society override, coverage amount, and payment waiving).
   */
  private function spawnDoctorCheckup(HomeCheckup $homeCheckup): void
  {
    if ($homeCheckup->visit_type !== HomeCheckupVisitType::WITH_DOCTOR) {
      return;
    }

    if (!$homeCheckup->doctor_id || !$homeCheckup->patient_id || $homeCheckup->checkup_id) {
      return;
    }

    $doctor = Doctor::findOrFail($homeCheckup->doctor_id);
    $patient = Patient::with('insuranceSocietyBranch.insuranceSociety')->findOrFail($homeCheckup->patient_id);

    $insuranceSociety = $patient->insuranceSocietyBranch?->insuranceSociety;

    $checkupPrice = round(
      $insuranceSociety?->checkupPricings()
        ->where('doctor_id', $doctor->id)
        ->value('checkup_price')
        ?? $doctor->checkup_price,
      2
    );

    $coverageAmount = insurance_coverage_amount($patient->insuranceSocietyBranch, $checkupPrice);

    $waiveCheckup = $patient->insuranceSocietyBranch
      && (bool) Setting::where('key', SettingsKeys::WAIVE_CHECKUP_PAYMENTS)->value('value');

    $initialPrice = round($checkupPrice - $coverageAmount, 2);

    $checkup = Checkup::create([
      'home_checkup_id' => $homeCheckup->id,
      'doctor_id' => $doctor->id,
      'patient_id' => $homeCheckup->patient_id,
      'date' => $homeCheckup->scheduled_at?->format('Y-m-d') ?? now()->toDateString(),
      'time' => $homeCheckup->scheduled_at?->format('H:i'),
      'status' => CheckupStatus::DRAFT,
      'original_price' => $doctor->checkup_price,
      'initial_price' => $initialPrice,
      'total_price' => $waiveCheckup ? 0 : $initialPrice,
      'coverage_amount' => $coverageAmount,
    ]);

    $checkup->ticket()->create([
      'date' => $checkup->date,
    ]);

    $homeCheckup->checkup_id = $checkup->id;
    $homeCheckup->save();
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

        if (array_key_exists('base_price', $data) || is_array($services)) {
          $basePrice = array_key_exists('base_price', $data)
            ? (float) ($data['base_price'] ?? 0)
            : (float) $model->base_price;

          $effectiveServices = is_array($services)
            ? $services
            : $model->services()->get(['service_price'])->toArray();

          $prices = $this->calculatePrices($basePrice, $effectiveServices);

          $data['services_total'] = $prices['services_total'];
          $data['total_price'] = $prices['total_price'];
        }

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

        $this->spawnDoctorCheckup($model);
      });

      $model->load(['patient', 'doctor.user.employee', 'services', 'nurses', 'checkup.doctor.user.employee', 'checkup.patient', 'checkup.ticket']);

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

      DB::transaction(function () use ($model, $newStatus) {
        $model->update(['status' => $newStatus]);

        if ($newStatus === HomeCheckupStatus::COMPLETED) {
          $model->actions()->create([
            'action_type' => ActionType::HOME_CHECKUP_PAYMENT_ACTION,
            'from_status' => HomeCheckupStatus::IN_PROGRESS,
            'to_status'   => HomeCheckupStatus::COMPLETED,
            'user_id'     => auth()->id(),
          ]);

          if ($model->total_price > 0) {
            $model->transactions()->create([
              'amount'   => $model->total_price,
              'details'  => "Home checkup payment for #{$model->home_checkup_number} (Patient: {$model->patient->fullname})",
              'type'     => Type::CREDIT,
              'status'   => Status::COMPLETED,
              'category' => TransactionCategory::HOME_CHECKUP,
              'user_id'  => auth()->id(),
              'accountable_type' => $model->patient::class,
              'accountable_id' => $model->patient->id,
            ]);
          }
        }
      });

      $model->refresh()->load(['patient', 'doctor.user.employee', 'services', 'nurses', 'checkup', 'paymentAction.user']);

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
