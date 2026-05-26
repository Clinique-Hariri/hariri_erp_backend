<?php

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

class CheckupAnalysesApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request, $checkupId)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission([
        PermissionNames::CHECKUP_SERVICES_VIEW,
        PermissionNames::CHECKUP_RADIOLIGY_VIEW,
        PermissionNames::CHECKUPS_DOCTOR_VIEW
      ]);
    }

    try {
      $model = CheckupAnalysis::with(['services.medicalService', 'checkup.patient', 'checkup.doctor', 'paymentAction.user', 'resultAction.user'])->where('checkup_id', $checkupId);

      if ($request->filled('checkup_id')) {
        $model->where('checkup_id', $request->checkup_id);
      }

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

      if ($request->filled('type')) {
        $type = is_array($request->type) ? $request->type : [$request->type];
        $model->whereIn('type', $type);
      }

      if ($request->filled('status')) {
        $status = is_array($request->status) ? $request->status : [$request->status];
        $model->whereIn('status', $status);
      }

      if ($request->filled('has_insurance')){
        if ($request->boolean('has_insurance')) {
          $model->whereHas('checkup.patient.insuranceSocietyBranch');
        } else {
          $model->whereDoesntHave('checkup.patient.insuranceSocietyBranch');
        }
      }

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $model->where(function($q) use ($searchTerm) {
          $q->where('checkup_analysis_number', 'like', "%{$searchTerm}%")
          ->orWhere('notes', 'like', "%{$searchTerm}%");
        });
      }

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
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

  public function show($checkupId, $id)
  {
    $this->authorizePermission([
      PermissionNames::CHECKUP_SERVICES_VIEW,
      PermissionNames::CHECKUP_RADIOLIGY_VIEW,
      PermissionNames::CHECKUPS_DOCTOR_VIEW
    ]);
    try {
      $model = CheckupAnalysis::with(['services.medicalService', 'checkup.patient', 'checkup.doctor', 'paymentAction', 'resultAction'])
        ->where('checkup_id', $checkupId)
        ->where('id', $id)
        ->firstOrFail();

      return $this->successResponse(
        data: new CheckupAnalysisResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request, $checkupId)
  {
    $this->authorizePermission([
      PermissionNames::CHECKUP_SERVICES_CREATE,
      PermissionNames::CHECKUP_RADIOLIGY_CREATE,
      PermissionNames::CHECKUPS_DOCTOR_UPDATE
    ]);
    $data = $this->validateRequest($request, StoreCheckupAnalysisRequest::rules());

    try {
      $checkup = Checkup::with('patient.insuranceSocietyBranch.insuranceSociety')->findOrFail($checkupId);
      $insurance_society_branch = $checkup->patient?->insuranceSocietyBranch;
      $insurance_society = $insurance_society_branch?->insuranceSociety;

      $serviceIds = collect($data['medical_services'])->pluck('id');

      $medicalServices = MedicalService::whereIn('id', $serviceIds)->get();

      // Build a pricing map if insurance exists
      $pricingMap = $insurance_society
        ? $insurance_society->medicalServicePricings()
          ->whereIn('medical_service_id', $serviceIds)
          ->pluck('medical_service_price', 'medical_service_id')
          ->toArray()
        : [];

      $services = [];
      $total_services_price = 0;
      $total_original_price = 0;

      foreach ($medicalServices as $service) {
        $originalPrice = $service->price;
        $price = $pricingMap[$service->id] ?? $service->price;
        $price = round($price, 2);

        $services[] = [
          'service' => $service,
          'price'   => $price,
        ];

        $total_original_price += $originalPrice;
        $total_services_price += $price;
      }

      $coverage_amount = insurance_coverage_amount($insurance_society_branch, $total_services_price);

      $model = DB::transaction(function () use ($checkup, $data, $services, $coverage_amount, $total_services_price) {
        $model = $checkup->checkupAnalyses()->create([
          'notes'            => $data['notes'] ?? null,
          'type'             => $services[0]['service']->type ?? null,
          'coverage_amount'  => $coverage_amount,
          'total_price'      => $total_services_price - $coverage_amount,
          'original_price'   => $total_services_price,
        ]);

        foreach ($services as $service) {
          $model->services()->create([
            'medical_service_id' => $service['service']->id,
            'service_price'      => $service['price'],
          ]);
        }

        return $model->refresh();
      });

      return $this->successResponse(
        data: new CheckupAnalysisResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $checkupId, $id)
  {
    $this->authorizePermission([
      PermissionNames::CHECKUP_SERVICES_UPDATE,
      PermissionNames::CHECKUP_RADIOLIGY_UPDATE,
      PermissionNames::CHECKUPS_DOCTOR_UPDATE
    ]);
    $data = $this->validateRequest($request, UpdateCheckupAnalysisRequest::rules());

    try {
      $model = CheckupAnalysis::
        where('checkup_id', $checkupId)
        ->where('id', $id)
        ->firstOrFail();

      $model->update($data);

      return $this->successResponse(
        data: new CheckupAnalysisResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function old_updateStatus(Request $request, $checkupId, $id)
  {
    $data = $this->validateRequest($request, UpdateCheckupAnalysisStatusRequest::rules());
    $newStatus = $data['status'];

    try {
      match ($newStatus) {
        CheckupAnalysisStatus::PENDING => $this->authorizePermission([PermissionNames::CHECKUP_SERVICES_UPDATE_TO_PENDING, PermissionNames::CHECKUP_RADIOLIGY_UPDATE_TO_PENDING]),
        CheckupAnalysisStatus::IN_PROGRESS => $this->authorizePermission([PermissionNames::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS, PermissionNames::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS]),
        CheckupAnalysisStatus::COMPLETED => $this->authorizePermission([PermissionNames::CHECKUP_SERVICES_UPDATE_TO_COMPLETED, PermissionNames::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED]),
        default => throw new \InvalidArgumentException("Invalid status: {$newStatus}")
      };

      $model = CheckupAnalysis::with(['checkup.patient.insuranceSocietyBranch.insuranceSociety', 'checkup.doctor.user.employee'])
        ->where('checkup_id', $checkupId)
        ->where('id', $id)
        ->firstOrFail();

      $insurance_society = $model->checkup->patient->insuranceSocietyBranch?->insuranceSociety;
      $doctor = $model->checkup->doctor;

      if ($newStatus === CheckupAnalysisStatus::PENDING) {
        DB::transaction(function () use ($model, $insurance_society, $doctor, $newStatus) {
          // update status inside the same transaction
          $model->update(['status' => $newStatus]);

          // Create transactions
          $model?->transactions()->create([
            'amount' => $model->total_price,
            'details' => "Checkup Analysis payment for #{$model->checkup_analysis_number} (Patient: {$model->checkup->patient->fullname})",
            'type' => Type::CREDIT,
            'status' => Status::COMPLETED,
            'user_id' => auth()->id(),
          ]);

          $commission_percentage = (float) data_get($doctor?->commission_percentages, 'analysis', 0);
          if ($commission_percentage > 0 && $model->original_price >= 0) {
            $commission_amount = round(($commission_percentage / 100) * $model->original_price, 2);

            if ($commission_amount > 0) {
              $doctor?->transactions()->create([
                'amount' => $commission_amount,
                'details' => "Doctor commission for checkup analysis #{$model->checkup_analysis_number} (Patient: {$model->checkup->patient->fullname})",
                'type' => Type::DEBIT,
                'status' => Status::PENDING,
                'user_id' => auth()->id(),
              ]);
            }
          }
        });

        $insurance_society?->transactions()->create([
          'amount' => $model->coverage_amount,
          'details' => "Insurance coverage for checkup service #{$model->checkup_analysis_number} (Patient: {$model->checkup->patient->fullname})",
          'type' => Type::CREDIT,
          'status' => Status::PENDING,
          'user_id' => auth()->id(),
        ]);
      } elseif ($newStatus === CheckupAnalysisStatus::COMPLETED) {
        DB::transaction(function () use ($model, $request, $newStatus) {
          // Update analysis status
          $model->update(['status' => $newStatus]);

          if ($request->filled('services')) {
            foreach ($model->services as $service) {
              $input = collect($request->services)
                ->firstWhere('id', $service->id);

              if (!$input) continue;

              $type = $service->medicalService->result_type;

              // if type is 1, 2, or 3 (text, number, selection) is required
              if (in_array($type, [1, 2, 3, 5, 6, 7, 8])) {
                if (empty($input['result'])) {
                  throw ValidationException::withMessages([
                    "services.{$service->id}.result" => "Result is required for this service type."
                  ]);
                }

                // Save the result
                $service->result = $input['result'];
              }

              // if type is 4 (attachment) is required
              if ($type == 4) {
                if (empty($input['result_attachment'])) {
                  throw ValidationException::withMessages([
                    "services.{$service->id}.result_attachment" => "Attachment is required for this service type."
                  ]);
                }

                // Handle file upload
                $service->clearMediaCollection('result_attachment');
                $service->addMedia($input['result_attachment'])
                  ->toMediaCollection('result_attachment');
              }

              $service->save();
            }
          }
        });
      } else {
        $model->update(['status' => $newStatus]);
      }

      $model->load(['paymentAction.user', 'resultAction.user']);

      return $this->successResponse(
        data: new CheckupAnalysisResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function updateStatus(Request $request, $checkupId, $id)
  {
    $data = $this->validateRequest($request, UpdateCheckupAnalysisStatusRequest::rules());
    $newStatus = $data['status'];

    try {
      match ($newStatus) {
        CheckupAnalysisStatus::PENDING => $this->authorizePermission([PermissionNames::CHECKUP_SERVICES_UPDATE_TO_PENDING, PermissionNames::CHECKUP_RADIOLIGY_UPDATE_TO_PENDING]),
        CheckupAnalysisStatus::IN_PROGRESS => $this->authorizePermission([PermissionNames::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS, PermissionNames::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS]),
        CheckupAnalysisStatus::COMPLETED => $this->authorizePermission([PermissionNames::CHECKUP_SERVICES_UPDATE_TO_COMPLETED, PermissionNames::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED]),
        default => throw new \InvalidArgumentException("Invalid status: {$newStatus}")
      };

      $model = CheckupAnalysis::with(['checkup.patient.insuranceSocietyBranch.insuranceSociety', 'checkup.doctor.user.employee'])
        ->where('checkup_id', $checkupId)
        ->where('id', $id)
        ->firstOrFail();

      $insuranceSociety = $model->checkup->patient->insuranceSocietyBranch?->insuranceSociety;
      $doctor = $model->checkup->doctor;

      if ($newStatus === CheckupAnalysisStatus::PENDING) {
        DB::transaction(function () use ($model, $insuranceSociety, $doctor, $newStatus) {
          $model->update(['status' => $newStatus]);

          $model->transactions()->create([
            'amount' => $model->total_price,
            'details' => "Checkup Analysis payment for #{$model->checkup_analysis_number} (Patient: {$model->checkup->patient->fullname})",
            'type' => Type::CREDIT,
            'status' => Status::COMPLETED,
            'user_id' => auth()->id(),
            'accountable_type' => $model->checkup->patient::class,
            'accountable_id' => $model->checkup->patient->id,
          ]);

          if ($insuranceSociety) {
            $model->transactions()->create([
              'amount' => $model->coverage_amount,
              'details' => "Insurance coverage for checkup service #{$model->checkup_analysis_number} (Patient: {$model->checkup->patient->fullname})",
              'type' => Type::CREDIT,
              'status' => Status::PENDING,
              'user_id' => auth()->id(),
              'accountable_type' => $insuranceSociety::class,
              'accountable_id' => $insuranceSociety->id,
            ]);
          }

          $commissionPercentage = (float) data_get($doctor?->commission_percentages, 'analysis', 0);
          if ($commissionPercentage > 0 && $model->original_price >= 0) {
            $commissionAmount = round(($commissionPercentage / 100) * $model->original_price, 2);

            if ($commissionAmount > 0) {
              $model->transactions()->create([
                'amount' => $commissionAmount,
                'details' => "Doctor commission for checkup analysis #{$model->checkup_analysis_number} (Patient: {$model->checkup->patient->fullname})",
                'type' => Type::DEBIT,
                'status' => Status::PENDING,
                'user_id' => auth()->id(),
                'accountable_type' => $doctor::class,
                'accountable_id' => $doctor->id,
              ]);
            }
          }
        });
      } elseif ($newStatus === CheckupAnalysisStatus::COMPLETED) {
        DB::transaction(function () use ($model, $request, $newStatus) {
          $model->update(['status' => $newStatus]);

          if ($request->filled('services')) {
            foreach ($model->services as $service) {
              $input = collect($request->services)
                ->firstWhere('id', $service->id);

              if (!$input) {
                continue;
              }

              $type = $service->medicalService->result_type;

              if (in_array($type, [1, 2, 3, 5, 6, 7, 8])) {
                if (empty($input['result'])) {
                  throw ValidationException::withMessages([
                    "services.{$service->id}.result" => "Result is required for this service type."
                  ]);
                }

                $service->result = $input['result'];
              }

              if ($type == 4) {
                if (empty($input['result_attachment'])) {
                  throw ValidationException::withMessages([
                    "services.{$service->id}.result_attachment" => "Attachment is required for this service type."
                  ]);
                }

                $service->clearMediaCollection('result_attachment');
                $service->addMedia($input['result_attachment'])
                  ->toMediaCollection('result_attachment');
              }

              $service->save();
            }
          }
        });
      } else {
        $model->update(['status' => $newStatus]);
      }

      $model->load(['paymentAction.user', 'resultAction.user']);

      return $this->successResponse(
        data: new CheckupAnalysisResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function addInterpretation(Request $request, $checkupId, $id)
  {
    $this->authorizePermission([
      PermissionNames::CHECKUP_SERVICES_UPDATE,
      PermissionNames::CHECKUP_RADIOLIGY_UPDATE,
      PermissionNames::CHECKUPS_DOCTOR_UPDATE
    ]);

    $data = $this->validateRequest($request, [
      'doctor_interpretation' => ['required', 'string'],
    ]);

    try {
      $model = CheckupAnalysis::where('checkup_id', $checkupId)
        ->where('id', $id)
        ->firstOrFail();

      $model->update($data);

      return $this->successResponse(
        data: new CheckupAnalysisResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($checkupId, $id)
  {
    $this->authorizePermission([
      PermissionNames::CHECKUP_SERVICES_DELETE,
      PermissionNames::CHECKUPS_DOCTOR_UPDATE
    ]);

    try {
      $model = CheckupAnalysis::where('checkup_id', $checkupId)
        ->where('id', $id)
        ->firstOrFail();

      $model->delete();

      return $this->successResponse(
        data: [
          'total' => CheckupAnalysis::count()
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


  public function multiDestroy(Request $request, $checkupId)
  {
    $this->authorizePermission(PermissionNames::CHECKUP_SERVICES_DELETE);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array',
      'ids.*' => 'required|exists:checkup_analyses,id',
    ]);

    try {
      $checkupAnalyses = CheckupAnalysis::
        where('checkup_id', $checkupId)
        ->whereIn('id', $data['ids'])
        ->get();

      DB::transaction(function () use ($checkupAnalyses) {
        foreach ($checkupAnalyses as $checkupAnalysis) {
//          $checkupAnalysis->clearMediaCollection(CheckupAnalysis::RESULT_ATTACHMENT);
          $checkupAnalysis->transactions()->delete();
          $checkupAnalysis->delete();
        }
      });

      return $this->successResponse(
        data: [
          'total' => CheckupAnalysis::count()
        ]
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

}
