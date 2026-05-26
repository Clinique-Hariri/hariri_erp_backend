<?php

namespace Modules\MedicalReferences\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\MedicalReferences\Http\Resources\InsuranceSocietyServicePricingResource;
use Modules\MedicalReferences\Models\InsuranceSociety;
use Modules\MedicalReferences\Models\InsuranceSocietyServicePricing;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Http\Resources\TransactionResource;
use Throwable;

class InsuranceSocietyTransactionsApiController extends Controller
{
  use ApiResponseTrait;
  public function index(Request $request, $societyId)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_VIEW);
    }

    try {
      $model = InsuranceSociety::findOrFail($societyId)->transactions()
        ->with(['user'])->orderBy('created_at', 'desc');

      if ($request->filled('status')) {
        $model->where('status', $request->status);
      }

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $model->where(function($q) use ($searchTerm) {
          $q->where('details', 'like', "%{$searchTerm}%")
            ->orWhere('transaction_number', 'like', "%{$searchTerm}%");
        });
      }

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      if ($request->boolean('paginate')) {
        $model = $model->paginate($request->get('per_page', 10));
      } else {
        $model = $model->get();
      }

      return $this->successResponse(
        data: TransactionResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($societyId, $id)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_VIEW);

    try {
      $model = InsuranceSociety::findOrFail($societyId);
      $model = $model->transactions()->with(['user'])->findOrFail($id);

      return $this->successResponse(
        data: new TransactionResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function updateStatus(Request $request, $societyId, $id)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_UPDATE);

    $request->validate([
      'status' => ['required', Rule::in(Status::all())]
    ]);

    try {
      $model = InsuranceSociety::findOrFail($societyId);
      $model = $model->transactions()->findOrFail($id);

      if (!in_array($request->status, array_column(Status::get_next_statuses($model->status), 'value'))) {
        return $this->errorResponse('Invalid status value', 422);
      }

      $model->update([
        'status' => $request->status
      ]);
      $model->refresh();

      return $this->successResponse(
        data: new TransactionResource($model),
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function multiUpdateStatus(Request $request, $societyId)
  {
    $this->authorizePermission(PermissionNames::INSURANCE_SOCIETIES_UPDATE);

    $request->validate([
      'ids' => ['required', 'array'],
      'ids.*' => ['integer', 'exists:transactions,id'],
      'status' => ['required', Rule::in(Status::all())],
    ]);

    try {
      DB::transaction(function () use ($request, $societyId) {
        $model = InsuranceSociety::findOrFail($societyId);
        $transactions = $model->transactions()->whereIn('id', $request->ids)->get();

        foreach ($transactions as $transaction) {
          $nextStatuses = array_column(Status::get_next_statuses($transaction->status), 'value');

          if (in_array($request->status, $nextStatuses)) {
            $transaction->update(['status' => $request->status]);
          }
        }

        return true;
      });

      return $this->successResponse();
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
