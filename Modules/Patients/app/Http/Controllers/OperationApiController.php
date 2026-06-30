<?php

namespace Modules\Patients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Actions\Constants\ActionType;
use Modules\Patients\Constants\OperationStatus;
use Modules\Patients\Http\Requests\Operation\StoreOperationRequest;
use Modules\Patients\Http\Requests\Operation\UpdateOperationRequest;
use Modules\Patients\Http\Requests\Operation\UpdateOperationStatusRequest;
use Modules\Patients\Http\Resources\OperationResource;
use Modules\Patients\Models\Operation;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\Type;
use Throwable;

class OperationApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission([
        PermissionNames::OPERATIONS_VIEW,
        PermissionNames::CHECKUPS_DOCTOR_VIEW
      ]);
    }

    try {
      $model = Operation::with(['patient', 'surgeons.doctor.user.employee', 'paymentAction.user'])
        ->whereHas('patient', function ($query) {
          $query->filterByInsuranceSociety();
        });

      if ($request->has('search') && $request->get('search') !== null) {
        $search = $request->get('search');
        $model = $model->whereHas('patient', function ($query) use ($search) {
          $query->where('fullname', 'like', '%' . $search . '%');
        })
          ->orWhere('id', 'like', '%' . $search . '%')
          ->orWhere('operation_number', 'like', '%' . $search . '%')
          ->orWhere('price', 'like', '%' . $search . '%')
          ->orWhere('description', 'like', '%' . $search . '%');
      }

      if ($request->filled('status')) {
        $status = is_array($request->status) ? $request->status : [$request->status];
        $model = $model->whereIn('status', $status);
      }

      if ($request->filled('patient_id')) {
        $model->where('patient_id', $request->get('patient_id'));
      }

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      $allowedSorts = ['id', 'operation_number', 'price', 'status', 'created_at'];

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
        data: OperationResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission([
      PermissionNames::OPERATIONS_VIEW,
      PermissionNames::CHECKUPS_DOCTOR_VIEW
    ]);

    try {
      $model = Operation::with(['patient', 'surgeons.doctor.user.employee', 'paymentAction.user'])->findOrFail($id);

      return $this->successResponse(
        data: new OperationResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNames::OPERATIONS_CREATE);

    $data = $this->validateRequest($request, StoreOperationRequest::rules());

    $data['status'] = OperationStatus::DRAFT;
    try {
      $operation = Operation::create($data);

      if ($request->has('surgeons')) {
        $operation->surgeons()->createMany($data['surgeons']);
      }

      return $this->successResponse(
        data: new OperationResource($operation->load(['patient', 'surgeons.doctor', 'paymentAction.user']))
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::OPERATIONS_UPDATE);

    $data = $this->validateRequest($request, UpdateOperationRequest::rules());

    try {
      $operation = Operation::findOrFail($id);

      if($operation->status != OperationStatus::DRAFT) {
        throw new \InvalidArgumentException("Can update operation just when status is draft");
      }

      $operation->update($data);

      if ($request->has('surgeons')) {
        $operation->surgeons()->delete();
        $operation->surgeons()->createMany($data['surgeons']);
      }

      return $this->successResponse(
        data: new OperationResource($operation->load(['patient', 'surgeons.doctor', 'paymentAction.user']))
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function updateStatus(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::OPERATIONS_UPDATE);

    $data = $this->validateRequest($request, UpdateOperationStatusRequest::rules());
    $newStatus = $data['status'];

    try {
      match ($newStatus) {
        OperationStatus::PENDING => $this->authorizePermission(PermissionNames::OPERATIONS_UPDATE_TO_PENDING),
        OperationStatus::SCHEDULED => $this->authorizePermission(PermissionNames::OPERATIONS_UPDATE_TO_SCHEDULED),
        OperationStatus::COMPLETED => $this->authorizePermission(PermissionNames::OPERATIONS_UPDATE_TO_COMPLETED),
        default => throw new \InvalidArgumentException("Invalid status: {$newStatus}")
      };

      $model = Operation::with(['patient', 'surgeons.doctor.user.employee', 'paymentAction.user'])->findOrFail($id);

      DB::transaction(function () use ($model, $newStatus) {
        $model->update(['status' => $newStatus]);

        if ($newStatus === OperationStatus::PENDING) {
          $model->actions()->create([
            'action_type' => ActionType::OPERATION_PAYMENT_ACTION,
            'from_status' => OperationStatus::DRAFT,
            'to_status'   => OperationStatus::PENDING,
            'user_id'     => auth()->id(),
          ]);

          $model->transactions()->create([
            'amount' => $model->price,
            'details' => "Operation payment for #{$model->operation_number} (Patient: {$model->patient->fullname})",
            'type' => Type::CREDIT,
            'status' => Status::COMPLETED,
            'user_id' => auth()->id(),
            'accountable_type' => $model->patient::class,
            'accountable_id' => $model->patient->id,
          ]);

          foreach ($model->surgeons as $surgeon) {
            $commission = ($surgeon->doctor_commission_percentage / 100) * $model->price;
            $doctor = $surgeon->doctor;

            if ($doctor && $commission > 0) {
              $doctorName = $doctor->user?->employee?->fullname ?? "Doctor #{$doctor->id}";

              $model->transactions()->create([
                'amount' => $commission,
                'details' => "Doctor commission for operation #{$model->operation_number} (Doctor: {$doctorName})",
                'type' => Type::DEBIT,
                'status' => Status::PENDING,
                'user_id' => auth()->id(),
                'accountable_type' => $doctor::class,
                'accountable_id' => $doctor->id,
              ]);
            }
          }
        }
      });

      $model->load('paymentAction.user');

      return $this->successResponse(
        data: new OperationResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }


  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::OPERATIONS_DELETE);

    try {
      $operation = Operation::findOrFail($id);
      $operation->delete();

      return $this->successResponse(
        data: [
          'total' => Operation::count()
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
    $this->authorizePermission(PermissionNames::OPERATIONS_DELETE);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array',
      'ids.*' => 'required|exists:operations,id'
    ]);

    try {
      Operation::whereIn('id', $data['ids'])->delete();

      return $this->successResponse(
        data: [
          'total' => Operation::count()
        ]
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

}
