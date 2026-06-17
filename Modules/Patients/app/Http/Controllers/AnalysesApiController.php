<?php

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Notifications\AnalysisResultNotification;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\Actions\Constants\ActionType;
use Modules\Patients\Constants\CheckupAnalysisStatus;
use Modules\Patients\Http\Requests\CheckupAnalysis\SendAnalysisResultNotificationRequest;
use Modules\Patients\Http\Requests\CheckupAnalysis\UpdateCheckupAnalysisStatusRequest;
use Modules\Patients\Http\Resources\CheckupAnalysisResource;
use Modules\Patients\Models\CheckupAnalysis;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\Type;
use Throwable;

class AnalysesApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission([
        PermissionNames::CHECKUP_SERVICES_VIEW,
        PermissionNames::CHECKUP_RADIOLIGY_VIEW,
        PermissionNames::CHECKUPS_DOCTOR_VIEW
      ]);
    }

    try {
      $model = CheckupAnalysis::with(['services.medicalService', 'checkup.patient', 'checkup.doctor', 'paymentAction.user', 'resultAction.user'])
        ->whereHas('checkup.patient', function ($query) {
          $query->filterByInsuranceSociety();
        });
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

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $model->where(function($q) use ($searchTerm) {
          $q->where('checkup_analysis_number', 'like', "%{$searchTerm}%")
          ->orWhere('notes', 'like', "%{$searchTerm}%")
          ->orWhereHas('checkup.patient', function($q) use ($searchTerm) {
            $q->where('patient_number', 'like', "%{$searchTerm}%")
            ->orWhere('fullname', 'like', "%{$searchTerm}%")
            ->orWhere('phone', 'like', "%{$searchTerm}%");
          });
        });
      }

      if ($request->filled('has_insurance')){
        if ($request->boolean('has_insurance')) {
          $model->whereHas('checkup.patient.insuranceSocietyBranch');
        } else {
          $model->whereDoesntHave('checkup.patient.insuranceSocietyBranch');
        }
      }

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      $allowedSorts = ['id', 'status', 'total_price', 'created_at'];

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
        data: CheckupAnalysisResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission([
      PermissionNames::CHECKUP_SERVICES_VIEW,
      PermissionNames::CHECKUP_RADIOLIGY_VIEW,
      PermissionNames::CHECKUPS_DOCTOR_VIEW
    ]);
    try {
      $model = CheckupAnalysis::with(['services.medicalService', 'checkup.patient', 'checkup.doctor', 'paymentAction.user', 'resultAction.user'])
        ->where('id', $id)
        ->firstOrFail();

      return $this->successResponse(
        data: new CheckupAnalysisResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

//  public function updateAttachment(Request $request, $id)
//  {
//    $this->authorizePermission([
//      PermissionNames::CHECKUP_SERVICES_UPDATE,
//    ]);
//
//    $data = $this->validateRequest($request, [
//      'result_attachment' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx', 'max:10240'], // 2MB max size
//    ]);
//
//    try {
//      $model = CheckupAnalysis::findOrFail($id);
//
//      if($model->status !== CheckupAnalysisStatus::COMPLETED) {
//        return $this->errorResponse("Can only update attachments for COMPLETED analyses.", 400);
//      }
//
//      if ($request->hasFile(CheckupAnalysis::RESULT_ATTACHMENT)) {
//        $model->clearMediaCollection(CheckupAnalysis::RESULT_ATTACHMENT);
//        $model->addMediaFromRequest('result_attachment')
//          ->toMediaCollection(CheckupAnalysis::RESULT_ATTACHMENT);
//      }
//
//      return $this->successResponse(
//        data: new CheckupAnalysisResource($model)
//      );
//    } catch (Throwable $e) {
//      return $this->errorResponse($e->getMessage(), 500);
//    }
//  }

  public function old_updateStatus(Request $request, $id)
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

      $model = CheckupAnalysis::with(['checkup.patient.insuranceSocietyBranch.insuranceSociety'])
        ->where('id', $id)
        ->firstOrFail();

      $insurance_society = $model->checkup->patient->insuranceSocietyBranch?->insuranceSociety;

      if ($newStatus === CheckupAnalysisStatus::PENDING) {
        DB::transaction(function () use ($model, $insurance_society, $newStatus) {
          // update status inside the same transaction
          $model->update(['status' => $newStatus]);

          // Record action
          $model->actions()->create([
            'action_type' => ActionType::ANALYSIS_PAYMENT_ACTION,
            'from_status' => CheckupAnalysisStatus::DRAFT,
            'to_status'   => CheckupAnalysisStatus::PENDING,
            'user_id'     => auth()->id(),
          ]);

          // Create transactions
          $model?->transactions()->create([
            'amount' => $model->total_price,
            'details' => "Checkup Analysis payment for #{$model->checkup_analysis_number} (Patient: {$model->checkup->patient->fullname})",
            'type' => Type::CREDIT,
            'status' => Status::COMPLETED,
            'user_id' => auth()->id(),
          ]);
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

          // Record action
          $model->actions()->create([
            'action_type' => ActionType::ANALYSIS_RESULT_ACTION,
            'from_status' => CheckupAnalysisStatus::IN_PROGRESS,
            'to_status'   => CheckupAnalysisStatus::COMPLETED,
            'user_id'     => auth()->id(),
          ]);

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
                    "services.{$service->id}.result" => "Result is required for this service type ({$service->id})."
                  ]);
                }

                // Save the result
                $service->result = $input['result'];
              }

              // if type is 4 (attachment) is required
              if ($type == 4) {
                if (empty($input['result_attachment'])) {
                  throw ValidationException::withMessages([
                    "services.{$service->id}.result_attachment" => "Attachment is required for this service type ({$service->id})."
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

  public function updateStatus(Request $request, $id)
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

      $model = CheckupAnalysis::with(['checkup.patient.insuranceSocietyBranch.insuranceSociety'])
        ->where('id', $id)
        ->firstOrFail();

      $insuranceSociety = $model->checkup->patient->insuranceSocietyBranch?->insuranceSociety;

      if ($newStatus === CheckupAnalysisStatus::PENDING) {
        DB::transaction(function () use ($model, $insuranceSociety, $newStatus) {
          $model->update(['status' => $newStatus]);

          $model->actions()->create([
            'action_type' => ActionType::ANALYSIS_PAYMENT_ACTION,
            'from_status' => CheckupAnalysisStatus::DRAFT,
            'to_status'   => CheckupAnalysisStatus::PENDING,
            'user_id'     => auth()->id(),
          ]);

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
        });
      } elseif ($newStatus === CheckupAnalysisStatus::COMPLETED) {
        DB::transaction(function () use ($model, $request, $newStatus) {
          $model->update(['status' => $newStatus]);

          $model->actions()->create([
            'action_type' => ActionType::ANALYSIS_RESULT_ACTION,
            'from_status' => CheckupAnalysisStatus::IN_PROGRESS,
            'to_status'   => CheckupAnalysisStatus::COMPLETED,
            'user_id'     => auth()->id(),
          ]);

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
                    "services.{$service->id}.result" => "Result is required for this service type ({$service->id})."
                  ]);
                }

                $service->result = $input['result'];
              }

              if ($type == 4) {
                if (empty($input['result_attachment'])) {
                  throw ValidationException::withMessages([
                    "services.{$service->id}.result_attachment" => "Attachment is required for this service type ({$service->id})."
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

  public function destroy($id)
  {
    $this->authorizePermission([
      PermissionNames::CHECKUP_SERVICES_DELETE,
      PermissionNames::CHECKUPS_DOCTOR_UPDATE
    ]);

    try {
      /** @var CheckupAnalysis $model */
      $model = CheckupAnalysis::where('id', $id)
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


  public function multiDestroy(Request $request)
  {
    $this->authorizePermission(PermissionNames::CHECKUP_SERVICES_DELETE);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array',
      'ids.*' => 'required|exists:checkup_analyses,id',
    ]);

    try {
      $checkupAnalyses = CheckupAnalysis::whereIn('id', $data['ids'])
        ->get();

      DB::transaction(function () use ($checkupAnalyses) {
        foreach ($checkupAnalyses as $checkupAnalysis) {
          /** @var CheckupAnalysis $checkupAnalysis */
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

  public function sendResultNotification(Request $request)
  {
    $this->authorizePermission(PermissionNames::ANALYSES_SEND_RESULT_NOTIFICATION);

    $this->validateRequest($request, SendAnalysisResultNotificationRequest::rules());

    try {
      $analysis = CheckupAnalysis::with(['checkup.patient', 'resultAction'])
        ->findOrFail($request->integer('checkup_analysis_id'));

      $patient = $analysis->checkup?->patient;

      if (!$patient) {
        return $this->errorResponse('Patient not found for this analysis.', 404);
      }

      $reportFilename = $analysis->checkup_analysis_number . '.pdf';

      if ($request->hasFile('report')) {
        $ftp = Storage::disk('ftp')->putFileAs('analyses', $request->file('report'), $reportFilename);
      }

      $pdfUrl = Storage::disk('ftp')->url('analyses/' . $reportFilename);

      $date = optional($analysis->resultAction?->created_at)->format('Y-m-d') ?? now()->format('Y-m-d');
      $reference = $analysis->checkup_analysis_number;
      $contact = config('services.clinic.contact', '');

      // Determine available channels for the patient
      $availableChannels = [];
      if (filled($patient->routeNotificationForMail())) {
        $availableChannels[] = 'mail';
      }
      if (filled($patient->routeNotificationForWhatsApp())) {
        $availableChannels[] = 'whatsapp';
      }
      if (filled($patient->routeNotificationForSms())) {
        $availableChannels[] = 'sms';
      }

      if (empty($availableChannels)) {
        return $this->errorResponse('Patient has no email, WhatsApp number, or SMS number.', 422);
      }

      $errors = [];
      $successCount = 0;

      foreach ($availableChannels as $channel) {
        try {
          $patient->notify(new AnalysisResultNotification(
            pdfUrl: $pdfUrl,
            date: $date,
            reference: $reference,
            contact: $contact,
            channels: [$channel],
          ));

          $successCount++;
        } catch (Throwable $e) {
          $errors[$channel] = $e->getMessage();
        }
      }

      $totalChannels = count($availableChannels);
      $message = match (true) {
        $successCount === $totalChannels => 'success',
        $successCount > 0 => 'partial success',
        default => 'total failure',
      };

      return response()->json([
        'status' => 1,
        'message' => $message,
        'errors' => $errors,
        'data' => [
          'checkup_analysis_id' => $analysis->id,
          'patient_id' => $patient->id,
          'report_url' => $pdfUrl,
        ],
      ]);
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }


}
