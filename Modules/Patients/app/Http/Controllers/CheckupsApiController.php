<?php

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Clinic\Models\Doctor;
use Modules\Patients\Constants\CheckupStatus;
use Modules\Patients\Http\Requests\Checkup\StoreCheckupRequest;
use Modules\Patients\Http\Requests\Checkup\UpdateCheckupRequest;
use Modules\Patients\Http\Requests\Checkup\UpdateCheckupStatusRequest;
use Modules\Patients\Http\Resources\CheckupResource;
use Modules\Patients\Models\Checkup;
use Modules\Patients\Models\CheckupTicket;
use Modules\Patients\Models\Patient;
use Modules\Actions\Constants\ActionType;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\Type;
use Modules\Transactions\Models\Transaction;
use Throwable;

class CheckupsApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission([
        PermissionNames::CHECKUPS_VIEW,
        PermissionNames::CHECKUPS_DOCTOR_VIEW,
        PermissionNames::CHECKUPS_VITAL_SIGNS_VIEW,
      ]);
    }

    try {
      $model = Checkup::with(['doctor.user.employee', 'ticket', 'patient.insuranceSocietyBranch.insuranceSociety', 'unpaidCheckupAnalyses.services.medicalService', 'paidCheckupAnalyses.services.medicalService', 'paymentAction.user'])
        ->whereHas('patient', function ($query) {
          $query->filterByInsuranceSociety();
        });

      if ($request->has('search') && $request->get('search') !== null) {
        $search = $request->get('search');
        $model = $model->whereHas('patient', function ($query) use ($search) {
          $query->where('fullname', 'like', '%' . $search . '%');
        })->orWhereHas('doctor.user.employee', function ($query) use ($search) {
          $query->where('fullname', 'like', '%' . $search . '%');
        })
          ->orWhere('checkup_number', 'like', '%' . $search . '%');
      }

      //status filter
      if ($request->filled('status')) {
        $status = is_array($request->status) ? $request->status : [$request->status];
        $model = $model->whereIn('status', $status);
      }

      if ($request->filled('patient_id')) {
        $model->where('patient_id', $request->get('patient_id'));
      }

      if ($request->filled('doctor_id')) {
        $model->where('doctor_id', $request->get('doctor_id'));
      }

      if ($request->filled('has_insurance')){
        if ($request->boolean('has_insurance')) {
          $model->whereHas('patient.insuranceSocietyBranch');
        } else {
          $model->whereDoesntHave('patient.insuranceSocietyBranch');
        }
      }

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      $allowedSorts = ['id', 'date', 'status', 'total_price', 'created_at'];

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
        data: CheckupResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission([
      PermissionNames::CHECKUPS_VIEW,
      PermissionNames::CHECKUPS_DOCTOR_VIEW,
      PermissionNames::CHECKUPS_VITAL_SIGNS_VIEW,
    ]);

    try {
      $model = Checkup::with(['doctor.user.employee', 'ticket', 'patient.insuranceSocietyBranch.insuranceSociety', 'paymentAction.user'])->findOrFail($id);

      return $this->successResponse(
        data: new CheckupResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNames::CHECKUPS_CREATE);

    $data = $this->validateRequest($request, StoreCheckupRequest::rules());


    try {
      $data['status'] = CheckupStatus::DRAFT;

      $patient = Patient::with('insuranceSocietyBranch.insuranceSociety')->findOrFail($data['patient_id']);
      $insurance_society_branch = $patient?->insuranceSocietyBranch;
      $insurance_society = $insurance_society_branch?->insuranceSociety;
      $doctor = Doctor::findOrFail($data['doctor_id']);

      $visitDate = Carbon::parse($data['date'])->toDateString();

      $exists = Checkup::where('patient_id', $data['patient_id'])
        ->where('doctor_id', $data['doctor_id'])
        ->whereBetween('date', [
          Carbon::parse($visitDate)->subDays(14)->toDateString(),
          $visitDate
        ])
        ->exists();

      /* if ($exists) {
        return $this->errorResponse(
          'Votre consultation est valable.',
          400
        );
      } */

      //calculate Prices
      $checkup_price = $exists ? 0 : round(
        $insurance_society?->checkupPricings()
        ->where('doctor_id', $doctor->id)
        ->value('checkup_price')
        ?? $doctor->checkup_price, 2);


      $data['coverage_amount'] = insurance_coverage_amount($insurance_society_branch, $checkup_price);
      $data['original_price'] = $doctor->checkup_price;
      $data['total_price'] = $data['initial_price'] = $checkup_price - $data['coverage_amount'];

      $model = DB::transaction(function () use($data){
        $model = Checkup::create($data);

        // Create the checkup ticket
        $model->ticket()->create([
          'date' => $model->date,
        ]);

        $model->load(['doctor.user.employee', 'patient', 'ticket']);

        return $model;
      });

      return $this->successResponse(
        data: new CheckupResource($model)
      );

    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {
    $this->authorizePermission([
      PermissionNames::CHECKUPS_UPDATE,
      PermissionNames::CHECKUPS_DOCTOR_UPDATE,
      PermissionNames::CHECKUPS_VITAL_SIGNS_UPDATE,
    ]);

    $data = $this->validateRequest($request, UpdateCheckupRequest::rules());

    try {
      $model = Checkup::with(['doctor.user.employee', 'patient'])->findOrFail($id);

      $model->update($data);

      return $this->successResponse(
        data: new CheckupResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function old_updateStatus(Request $request, $id)
  {
    $data = $this->validateRequest($request, UpdateCheckupStatusRequest::rules());
    $newStatus = $data['status'];

    try {
      match ($newStatus) {
        CheckupStatus::PENDING        => $this->authorizePermission(PermissionNames::CHECKUPS_UPDATE_TO_PENDING),
        CheckupStatus::IN_CONSULTATION => $this->authorizePermission(PermissionNames::CHECKUPS_UPDATE_TO_IN_CONSULTATION),
        CheckupStatus::COMPLETED      => $this->authorizePermission(PermissionNames::CHECKUPS_UPDATE_TO_COMPLETED),
        default                       => throw new \InvalidArgumentException("Invalid status: {$newStatus}"),
      };

      $model = Checkup::with(['doctor.user.employee', 'patient.insuranceSocietyBranch.insuranceSociety'])->findOrFail($id);
      $insurance_society = $model->patient->insuranceSocietyBranch?->insuranceSociety;
      $doctor = $model->doctor;

      if ($newStatus === CheckupStatus::PENDING) {
        DB::transaction(function () use ($model, $insurance_society, $doctor, $newStatus) {
          // update status inside the same transaction
          $model->update(['status' => $newStatus]);

          // Record action
          $model->actions()->create([
            'action_type' => ActionType::CHECKUP_PAYMENT_ACTION,
            'from_status' => CheckupStatus::DRAFT,
            'to_status'   => CheckupStatus::PENDING,
            'user_id'     => auth()->id(),
          ]);

          // Create transactions
          $model->transactions()->create([
            'amount'   => $model->total_price,
            'details'  => "Checkup payment for #{$model->checkup_number} (Patient: {$model->patient->fullname})",
            'type'     => Type::CREDIT,
            'status'   => Status::COMPLETED,
            'user_id'  => auth()->id(),
          ]);

          $insurance_society?->transactions()->create([
            'amount'   => $model->coverage_amount,
            'details'  => "Insurance coverage for checkup #{$model->checkup_number} (Patient: {$model->patient->fullname})",
            'type'     => Type::CREDIT,
            'status'   => Status::PENDING,
            'user_id'  => auth()->id(),
          ]);

          $commission_percentage = (float) data_get($doctor?->commission_percentages, 'checkup', 0);
          if($commission_percentage > 0 && $model->initial_price >= 0) {
            $commission_amount = round(($commission_percentage / 100) * $doctor->checkup_price, 2);
            if($commission_amount > 0) {
              $doctor?->transactions()->create([
                'amount'   => $commission_amount,
                'details'  => "Commission for checkup #{$model->checkup_number} (Patient: {$model->patient->fullname})",
                'type'     => Type::DEBIT,
                'status'   => Status::PENDING,
                'user_id'  => auth()->id(),
              ]);
            }
          }
        });
      }
      elseif ($newStatus === CheckupStatus::IN_CONSULTATION) {
        DB::transaction(function () use ($model, $newStatus) {
          $model->update(['status' => $newStatus]);
          $model->ticket->update(['status' => 'served']);
        });
      }
      else {
        $model->update(['status' => $newStatus]);
      }

      $model->refresh()->load(['doctor.user.employee', 'paymentAction.user']);

      return $this->successResponse(
        data: new CheckupResource($model)
      );

    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function updateStatus(Request $request, $id)
  {
    $data = $this->validateRequest($request, UpdateCheckupStatusRequest::rules());
    $newStatus = $data['status'];

    try {
      match ($newStatus) {
        CheckupStatus::PENDING        => $this->authorizePermission(PermissionNames::CHECKUPS_UPDATE_TO_PENDING),
        CheckupStatus::IN_CONSULTATION => $this->authorizePermission(PermissionNames::CHECKUPS_UPDATE_TO_IN_CONSULTATION),
        CheckupStatus::COMPLETED      => $this->authorizePermission(PermissionNames::CHECKUPS_UPDATE_TO_COMPLETED),
        default                       => throw new \InvalidArgumentException("Invalid status: {$newStatus}"),
      };

      $model = Checkup::with(['doctor.user.employee', 'patient.insuranceSocietyBranch.insuranceSociety'])->findOrFail($id);
      $insuranceSociety = $model->patient->insuranceSocietyBranch?->insuranceSociety;
      $doctor = $model->doctor;

      if ($newStatus === CheckupStatus::PENDING) {
        DB::transaction(function () use ($model, $insuranceSociety, $doctor, $newStatus) {
          $model->update(['status' => $newStatus]);

          $model->actions()->create([
            'action_type' => ActionType::CHECKUP_PAYMENT_ACTION,
            'from_status' => CheckupStatus::DRAFT,
            'to_status'   => CheckupStatus::PENDING,
            'user_id'     => auth()->id(),
          ]);

          $model->transactions()->create([
            'amount'   => $model->total_price,
            'details'  => "Checkup payment for #{$model->checkup_number} (Patient: {$model->patient->fullname})",
            'type'     => Type::CREDIT,
            'status'   => Status::COMPLETED,
            'user_id'  => auth()->id(),
            'accountable_type' => $model->patient::class,
            'accountable_id' => $model->patient->id,
          ]);

          if ($insuranceSociety) {
            $model->transactions()->create([
              'amount'   => $model->coverage_amount,
              'details'  => "Insurance coverage for checkup #{$model->checkup_number} (Patient: {$model->patient->fullname})",
              'type'     => Type::CREDIT,
              'status'   => Status::PENDING,
              'user_id'  => auth()->id(),
              'accountable_type' => $insuranceSociety::class,
              'accountable_id' => $insuranceSociety->id,
            ]);
          }

          $commissionPercentage = (float) data_get($doctor?->commission_percentages, 'checkup', 0);
          if ($commissionPercentage > 0 && $model->initial_price >= 0) {
            $commissionAmount = round(($commissionPercentage / 100) * $doctor->checkup_price, 2);
            if ($commissionAmount > 0) {
              $model->transactions()->create([
                'amount'   => $commissionAmount,
                'details'  => "Commission for checkup #{$model->checkup_number} (Patient: {$model->patient->fullname})",
                'type'     => Type::DEBIT,
                'status'   => Status::PENDING,
                'user_id'  => auth()->id(),
                'accountable_type' => $doctor::class,
                'accountable_id' => $doctor->id,
              ]);
            }
          }
        });
      } elseif ($newStatus === CheckupStatus::IN_CONSULTATION) {
        DB::transaction(function () use ($model, $newStatus) {
          $model->update(['status' => $newStatus]);
          $model->ticket->update(['status' => 'served']);
        });
      } else {
        $model->update(['status' => $newStatus]);
      }

      $model->refresh()->load(['doctor.user.employee', 'paymentAction.user']);

      return $this->successResponse(
        data: new CheckupResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::CHECKUPS_DELETE);

    try {
      $model = Checkup::findOrFail($id);
      $model->delete();

      return $this->successResponse(
        data: [
          'total' => Checkup::count()
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
    $this->authorizePermission(PermissionNames::CHECKUPS_DELETE);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array',
      'ids.*' => 'required|exists:checkups,id'
    ]);

    try {
      Checkup::whereIn('id', $data['ids'])->delete();

      return $this->successResponse(
        data: [
          'total' => Checkup::count()
        ]
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}

