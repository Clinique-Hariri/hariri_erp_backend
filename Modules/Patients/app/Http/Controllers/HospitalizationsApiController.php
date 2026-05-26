<?php

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Actions\Constants\ActionType;
use Modules\Patients\Constants\HospitalizationStatus;
use Modules\Patients\Http\Requests\Hospitalization\StoreHospitalizationRequest;
use Modules\Patients\Http\Requests\Hospitalization\UpdateHospitalizationRequest;
use Modules\Patients\Http\Requests\Hospitalization\UpdateHospitalizationStatusRequest;
use Modules\Patients\Http\Resources\HospitalizationResource;
use Modules\Patients\Models\Hospitalization;
use Modules\Settings\Constants\SettingsKeys;
use Modules\Settings\Models\Setting;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\Type;
use Throwable;

class HospitalizationsApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission([
        PermissionNames::HOSPITALIZATIONS_VIEW,
        PermissionNames::CHECKUPS_DOCTOR_VIEW,
      ]);
    }

    try {
      $model = Hospitalization::with(['patient', 'doctor', 'paymentAction.user', 'extensionAction.user'])
        ->whereHas('patient', function ($query) {
          $query->filterByInsuranceSociety();
        });

      if ($request->has('search') && $request->get('search') !== null) {
        $search = $request->get('search');
        $model = $model->whereHas('patient', function ($query) use ($search) {
          $query->where('fullname', 'like', '%' . $search . '%');
        })
          ->orWhere('room_number', 'like', '%' . $search . '%')
          ->orWhere('id', 'like', '%' . $search . '%')
          ->orWhere('hospitalization_number', 'like', '%' . $search . '%');
      }

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

      if ($request->filled('room_number')) {
        $model->where('room_number', $request->get('room_number'));
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

      if ($request->filled('admission_date')) {
        $model->whereDate('admission_date', $request->admission_date);
      }

      if ($request->filled('discharge_date')) {
        $model->whereDate('discharge_date', $request->discharge_date);
      }

      $allowedSorts = ['id', 'admission_date', 'discharge_date', 'status', 'total_price', 'created_at'];

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
        data: HospitalizationResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission([
      PermissionNames::HOSPITALIZATIONS_VIEW,
      PermissionNames::CHECKUPS_DOCTOR_VIEW,
    ]);

    try {
      $model = Hospitalization::with(['patient.insuranceSocietyBranch.insuranceSociety', 'doctor', 'paymentAction.user', 'extensionAction.user'])->findOrFail($id);

      return $this->successResponse(
        data: new HospitalizationResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {
    $this->authorizePermission([
      PermissionNames::HOSPITALIZATIONS_CREATE,
      PermissionNames::CHECKUPS_DOCTOR_UPDATE
    ]);

    $data = $this->validateRequest($request, StoreHospitalizationRequest::rules());

    try {
      $data['initial_price'] = $data['total_price'] = Hospitalization::calculateHospitalizationPrice($data['stay_length']);
      $data['status'] = HospitalizationStatus::DRAFT;
//      $admissionDate = new \DateTime($data['admission_date']);
//      $data['admission_date'] = $admissionDate->format('Y-m-d H:i:s');
//      $data['discharge_date'] = $admissionDate->add(new \DateInterval('PT' . $data['stay_length'] . 'H'))->format('Y-m-d H:i:s');

      $model = Hospitalization::create($data);

      return $this->successResponse(
        data: new HospitalizationResource($model->load(['patient', 'paymentAction.user', 'extensionAction.user']))
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::HOSPITALIZATIONS_UPDATE);

    $data = $this->validateRequest($request, UpdateHospitalizationRequest::rules());

    try {
      $model = Hospitalization::with(['patient', 'doctor.user.employee', 'paymentAction.user', 'extensionAction.user'])->findOrFail($id);
      //update just when draft status in the old model
      if ($model->status !== HospitalizationStatus::DRAFT) {
        return $this->errorResponse('You can only update hospitalizations with DRAFT status.', 403);
      }

      $data['initial_price'] = $data['total_price'] = Hospitalization::calculateHospitalizationPrice($data['stay_length']);
//      $admissionDate = new \DateTime($data['admission_date']);
//      $data['admission_date'] = $admissionDate->format('Y-m-d H:i:s');
//      $data['discharge_date'] = $admissionDate->add(new \DateInterval('PT' . $data['stay_length'] . 'H'))->format('Y-m-d H:i:s');

      $model->update($data);

      return $this->successResponse(
        data: new HospitalizationResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function old_updateStatus(Request $request, $id)
  {
    $data = $this->validateRequest($request, UpdateHospitalizationStatusRequest::rules());
    $newStatus = $data['status'];

    try {
      match ($newStatus) {
        HospitalizationStatus::ACCEPTED   => $this->authorizePermission(PermissionNames::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED),
        HospitalizationStatus::ADMITTED   => $this->authorizePermission(PermissionNames::HOSPITALIZATIONS_UPDATE_TO_ADMITTED),
        HospitalizationStatus::DISCHARGED => $this->authorizePermission(PermissionNames::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED),
        default => throw new \InvalidArgumentException("Invalid status: {$newStatus}")
      };

      $model = Hospitalization::with(['patient', 'doctor.user.employee', 'paymentAction.user', 'extensionAction.user'])->findOrFail($id);

      if ($newStatus === HospitalizationStatus::ACCEPTED) {
        if ($model->status !== HospitalizationStatus::DRAFT) {
          return $this->errorResponse('Can only accept hospitalizations that are currently in DRAFT status.', 403);
        }

        $model = DB::transaction(function () use ($model, $newStatus) {
          $model->update([
            'status' => $newStatus,
          ]);

          // Record action
          $model->actions()->create([
            'action_type' => ActionType::HOSPITALIZATION_PAYMENT_ACTION,
            'from_status' => HospitalizationStatus::DRAFT,
            'to_status'   => HospitalizationStatus::ACCEPTED,
            'user_id'     => auth()->id(),
          ]);

          if ($model->initial_price > 0) {
            $model->transactions()->create([
              'amount'  => $model->initial_price,
              'details' => "Initial hospitalization payment for #{$model->hospitalization_number} (Patient: {$model->patient->fullname})",
              'type'    => Type::CREDIT,
              'status'  => Status::COMPLETED,
              'user_id' => auth()->id(),
            ]);
          }

          $commission_percentage = (float) data_get($model->doctor?->commission_percentages, 'hospitalization', 0);
          if ($commission_percentage > 0 && $model->initial_price >= 0) {
            $commission_amount = round(($commission_percentage / 100) * $model->initial_price, 2);

            if ($commission_amount > 0) {
              $model->doctor?->transactions()->create([
                'amount'  => $commission_amount,
                'details' => "Doctor commission for hospitalization #{$model->hospitalization_number} (Patient: {$model->patient->fullname})",
                'type'    => Type::DEBIT,
                'status'  => Status::PENDING,
                'user_id' => auth()->id(),
              ]);
            }
          }
          return $model;
        });
      } elseif ($newStatus === HospitalizationStatus::ADMITTED) {
        if ($model->status !== HospitalizationStatus::ACCEPTED) {
          return $this->errorResponse('Can only admit hospitalizations that are currently ACCEPTED.', 403);
        }

        $admissionDate = now();
        $dischargeDate = $admissionDate->copy()->addHours($model->stay_length);

        $model->update([
          'admission_date' => $admissionDate->format('Y-m-d H:i:s'),
          'discharge_date' => $dischargeDate->format('Y-m-d H:i:s'),
          'status'         => $newStatus,
        ]);

      } elseif ($newStatus === HospitalizationStatus::DISCHARGED) {
        if ($model->status !== HospitalizationStatus::ADMITTED) {
          return $this->errorResponse('Can only discharge hospitalizations that are currently ADMITTED.', 403);
        }

        $now = now();
        $scheduledDischarge = Carbon::parse($model->discharge_date);
        $extendedStayLength = max(0, $scheduledDischarge->diffInHours($now, false));

          $model = DB::transaction(function () use ($model, $extendedStayLength, $newStatus) {
            $extensionFees = 0;
            if ($extendedStayLength > 0) {
              $extensionFees = Hospitalization::calculateHospitalizationPrice($extendedStayLength);

              $model->update([
                'stay_length'      => $model->stay_length + $extendedStayLength,
                'total_price'      => $model->total_price + $extensionFees,
                'extension_fees'   => $extensionFees
              ]);
            }

            $model->update([
              'discharge_date' => now(),
              'status'         => $newStatus,
            ]);

            if ($extensionFees  > 0) {
              $model->transactions()->create([
                'amount'  => $extensionFees,
                'details' => "Final hospitalization payment for #{$model->hospitalization_number} (Patient: {$model->patient->fullname})",
                'type'    => Type::CREDIT,
                'status'  => Status::COMPLETED,
                'user_id' => auth()->id(),
              ]);

              $commission_percentage = (float) data_get($model->doctor?->commission_percentages, 'hospitalization', 0);
              if ($commission_percentage > 0) {
                $extension_commission_amount = round(($commission_percentage / 100) * $extensionFees, 2);

                if ($extension_commission_amount > 0) {
                  $model->doctor?->transactions()->create([
                    'amount'  => $extension_commission_amount,
                    'details' => "Doctor extension commission for hospitalization #{$model->hospitalization_number} (Patient: {$model->patient->fullname})",
                    'type'    => Type::DEBIT,
                    'status'  => Status::PENDING,
                    'user_id' => auth()->id(),
                  ]);
                }
              }

              $model->actions()->create([
                'action_type' => ActionType::HOSPITALIZATION_EXTENSION_ACTION,
                'from_status' => HospitalizationStatus::ADMITTED,
                'to_status'   => HospitalizationStatus::DISCHARGED,
                'user_id'     => auth()->id(),
              ]);
            }

            return $model;
          });

      } else {
        $model->update([
          'status' => $newStatus,
        ]);
      }

      $model->load(['paymentAction.user', 'extensionAction.user']);

      return $this->successResponse(
        data: new HospitalizationResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function updateStatus(Request $request, $id)
  {
    $data = $this->validateRequest($request, UpdateHospitalizationStatusRequest::rules());
    $newStatus = $data['status'];

    try {
      match ($newStatus) {
        HospitalizationStatus::ACCEPTED   => $this->authorizePermission(PermissionNames::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED),
        HospitalizationStatus::ADMITTED   => $this->authorizePermission(PermissionNames::HOSPITALIZATIONS_UPDATE_TO_ADMITTED),
        HospitalizationStatus::DISCHARGED => $this->authorizePermission(PermissionNames::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED),
        default => throw new \InvalidArgumentException("Invalid status: {$newStatus}")
      };

      $model = Hospitalization::with(['patient', 'doctor.user.employee', 'paymentAction.user', 'extensionAction.user'])->findOrFail($id);

      if ($newStatus === HospitalizationStatus::ACCEPTED) {
        if ($model->status !== HospitalizationStatus::DRAFT) {
          return $this->errorResponse('Can only accept hospitalizations that are currently in DRAFT status.', 403);
        }

        $model = DB::transaction(function () use ($model, $newStatus) {
          $model->update([
            'status' => $newStatus,
          ]);

          $model->actions()->create([
            'action_type' => ActionType::HOSPITALIZATION_PAYMENT_ACTION,
            'from_status' => HospitalizationStatus::DRAFT,
            'to_status'   => HospitalizationStatus::ACCEPTED,
            'user_id'     => auth()->id(),
          ]);

          if ($model->initial_price > 0) {
            $model->transactions()->create([
              'amount'  => $model->initial_price,
              'details' => "Initial hospitalization payment for #{$model->hospitalization_number} (Patient: {$model->patient->fullname})",
              'type'    => Type::CREDIT,
              'status'  => Status::COMPLETED,
              'user_id' => auth()->id(),
              'accountable_type' => $model->patient::class,
              'accountable_id' => $model->patient->id,
            ]);
          }

          $commissionPercentage = (float) data_get($model->doctor?->commission_percentages, 'hospitalization', 0);
          if ($commissionPercentage > 0 && $model->initial_price >= 0) {
            $commissionAmount = round(($commissionPercentage / 100) * $model->initial_price, 2);

            if ($commissionAmount > 0) {
              $model->transactions()->create([
                'amount'  => $commissionAmount,
                'details' => "Doctor commission for hospitalization #{$model->hospitalization_number} (Patient: {$model->patient->fullname})",
                'type'    => Type::DEBIT,
                'status'  => Status::PENDING,
                'user_id' => auth()->id(),
                'accountable_type' => $model->doctor::class,
                'accountable_id' => $model->doctor->id,
              ]);
            }
          }
          return $model;
        });
      } elseif ($newStatus === HospitalizationStatus::ADMITTED) {
        if ($model->status !== HospitalizationStatus::ACCEPTED) {
          return $this->errorResponse('Can only admit hospitalizations that are currently ACCEPTED.', 403);
        }

        $admissionDate = now();
        $dischargeDate = $admissionDate->copy()->addHours($model->stay_length);

        $model->update([
          'admission_date' => $admissionDate->format('Y-m-d H:i:s'),
          'discharge_date' => $dischargeDate->format('Y-m-d H:i:s'),
          'status'         => $newStatus,
        ]);
      } elseif ($newStatus === HospitalizationStatus::DISCHARGED) {
        if ($model->status !== HospitalizationStatus::ADMITTED) {
          return $this->errorResponse('Can only discharge hospitalizations that are currently ADMITTED.', 403);
        }

        $now = now();
        $scheduledDischarge = Carbon::parse($model->discharge_date);
        $extendedStayLength = max(0, $scheduledDischarge->diffInHours($now, false));

        $model = DB::transaction(function () use ($model, $extendedStayLength, $newStatus) {
          $extensionFees = 0;
          if ($extendedStayLength > 0) {
            $extensionFees = Hospitalization::calculateHospitalizationPrice($extendedStayLength);

            $model->update([
              'stay_length'      => $model->stay_length + $extendedStayLength,
              'total_price'      => $model->total_price + $extensionFees,
              'extension_fees'   => $extensionFees
            ]);
          }

          $model->update([
            'discharge_date' => now(),
            'status'         => $newStatus,
          ]);

          if ($extensionFees > 0) {
            $model->transactions()->create([
              'amount'  => $extensionFees,
              'details' => "Final hospitalization payment for #{$model->hospitalization_number} (Patient: {$model->patient->fullname})",
              'type'    => Type::CREDIT,
              'status'  => Status::COMPLETED,
              'user_id' => auth()->id(),
              'accountable_type' => $model->patient::class,
              'accountable_id' => $model->patient->id,
            ]);

            $commissionPercentage = (float) data_get($model->doctor?->commission_percentages, 'hospitalization', 0);
            if ($commissionPercentage > 0) {
              $extensionCommissionAmount = round(($commissionPercentage / 100) * $extensionFees, 2);

              if ($extensionCommissionAmount > 0) {
                $model->transactions()->create([
                  'amount'  => $extensionCommissionAmount,
                  'details' => "Doctor extension commission for hospitalization #{$model->hospitalization_number} (Patient: {$model->patient->fullname})",
                  'type'    => Type::DEBIT,
                  'status'  => Status::PENDING,
                  'user_id' => auth()->id(),
                  'accountable_type' => $model->doctor::class,
                  'accountable_id' => $model->doctor->id,
                ]);
              }
            }

            $model->actions()->create([
              'action_type' => ActionType::HOSPITALIZATION_EXTENSION_ACTION,
              'from_status' => HospitalizationStatus::ADMITTED,
              'to_status'   => HospitalizationStatus::DISCHARGED,
              'user_id'     => auth()->id(),
            ]);
          }

          return $model;
        });
      } else {
        $model->update([
          'status' => $newStatus,
        ]);
      }

      $model->load(['paymentAction.user', 'extensionAction.user']);

      return $this->successResponse(
        data: new HospitalizationResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::HOSPITALIZATIONS_DELETE);

    try {
      $model = Hospitalization::findOrFail($id);
      $model->delete();

      return $this->successResponse(
        data: [
          'total' => Hospitalization::count()
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
    $this->authorizePermission(PermissionNames::HOSPITALIZATIONS_DELETE);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array',
      'ids.*' => 'required|exists:hospitalizations,id'
    ]);

    try {
      Hospitalization::whereIn('id', $data['ids'])->delete();

      return $this->successResponse(
        data: [
          'total' => Hospitalization::count()
        ]
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
