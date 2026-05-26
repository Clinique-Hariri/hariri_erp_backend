<?php

namespace Modules\Inventory\Http\Controllers\SupplyRequest;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Inventory\Http\Requests\SupplyRequest\StoreSupplyRequestRequest;
use Modules\Inventory\Http\Requests\SupplyRequest\UpdateSupplyRequestRequest;
use Modules\Inventory\Http\Requests\SupplyRequest\UpdateSupplyRequestStatusRequest;
use Modules\Inventory\Http\Resources\SupplyRequest\SupplyRequestResource;
use Modules\Inventory\Models\SupplyRequest;
use Throwable;

class SupplyRequestController extends Controller
{
  use ApiResponseTrait;

  public function kpiStatistics()
  {
    $this->authorizePermission(PermissionNames::SUPPLYREQUEST_VIEW);

    try {
      $total = SupplyRequest::count();
      $pending = SupplyRequest::where('status', 'pending')->count();
      $completed = SupplyRequest::where('status', 'completed')->count();
      $approved = SupplyRequest::where('status', 'approved')->count();

      return $this->successResponse(
        data: [
          'total' => $total,
          'pending' => $pending,
          'completed' => $completed,
          'approved' => $approved,
        ]
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::SUPPLYREQUEST_VIEW);
    }

    try {
      $query = SupplyRequest::with('supplyRequestItems.item', 'department')->latest();

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
          $q->where('request_number', 'like', "%{$searchTerm}%")
            ->orWhere('requested_by', 'like', "%{$searchTerm}%")
            ->orWhereHas('department', function ($query) use ($searchTerm) {
              $query->where('name', 'like', "%{$searchTerm}%");
            });
        });
      }

      if ($request->filled('department_id')) {
        $departmentIds = is_array($request->department_id) ? $request->department_id : [$request->department_id];
        $query->whereIn('department_id', $departmentIds);
      }

      if ($request->filled('status')) {
        $query->where('status', $request->status);
      }

      if ($request->filled('created_at_from')) {
        $query->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $query->whereDate('created_at', '<=', $request->created_at_to);
      }

      $sortBy = $request->get('sort_by', 'created_at');
      $sortOrder = $request->get('sort_order', 'asc');

      $allowedSortFields = ['request_number', 'status', 'created_at', 'updated_at'];

      if (in_array($sortBy, $allowedSortFields)) {
        $query->orderBy($sortBy, $sortOrder);
      } else {
        $query->orderBy('created_at', 'asc');
      }

      if ($request->boolean('paginate')) {
        $perPage = $request->get('per_page', 15);
        $model = $query->paginate($perPage);
      } else {
        $model = $query->get();
      }

      return $this->successResponse(
        data: SupplyRequestResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }

  }

  public function show($id)
  {
    $this->authorizePermission(PermissionNames::SUPPLYREQUEST_VIEW);

    try {
      $model = SupplyRequest::findOrFail($id);

      return $this->successResponse(
        data: new SupplyRequestResource($model->load(['department', 'supplyRequestItems.item']))
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }


  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNames::SUPPLYREQUEST_CREATE);

    $data = $this->validateRequest($request, StoreSupplyRequestRequest::rules());

    try {
      $requestData = [
        'request_number' => 'REQ-' . strtoupper(Str::random(8)),
        'department_id' => $data['department_id'],
        'requested_by' => $data['requested_by'],
      ];

      $model = SupplyRequest::create($requestData);

      foreach ($data['items'] as $itemData) {
        $model->supplyRequestItems()->create([
          'item_id' => $itemData['item_id'],
          'requested_quantity' => $itemData['requested_quantity'],
        ]);
      }

      return $this->successResponse(
        data: new SupplyRequestResource($model->load(['department', 'supplyRequestItems.item']))
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(UpdateSupplyRequestRequest $request, $id)
  {
    $this->authorizePermission(PermissionNames::SUPPLYREQUEST_UPDATE);

    $data = $this->validateRequest($request, UpdateSupplyRequestRequest::rules());

    try {
      $model = SupplyRequest::findOrFail($id);
      $model->update($data);

      $model->supplyRequestItems()->delete();

      if (!empty($data['items'])) {
        foreach ($data['items'] as $itemData) {
          $model->supplyRequestItems()->create([
            'item_id' => $itemData['item_id'],
            'requested_quantity' => $itemData['requested_quantity'],
          ]);
        }
      }

      return $this->successResponse(
        data: new SupplyRequestResource($model->load(['department', 'supplyRequestItems.item']))
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function updateStatus(UpdateSupplyRequestStatusRequest $request, $id)
  {
    $this->authorizePermission(PermissionNames::SUPPLYREQUEST_UPDATE);

    $data = $this->validateRequest($request, UpdateSupplyRequestStatusRequest::rules());

    try {
      $model = SupplyRequest::findOrFail($id);
      $model->update([
        'status' => $data['status'],
        'approved_by' => auth()->user()->fullname,
      ]);

      return $this->successResponse(
        data: new SupplyRequestResource($model->load(['department', 'supplyRequestItems.item']))
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::SUPPLYREQUEST_DELETE);

    try {

      $model = SupplyRequest::findOrFail($id);

      $model->delete();
      return $this->successResponse(
        data: [
          'total' => SupplyRequest::count()
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
