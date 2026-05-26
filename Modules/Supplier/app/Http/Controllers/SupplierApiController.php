<?php

namespace Modules\Supplier\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Modules\Supplier\Http\Requests\StoreSupplierRequest;
use Modules\Supplier\Http\Requests\UpdateSupplierRequest;
use Modules\Supplier\Http\Resources\SupplierResource;
use Modules\Supplier\Models\Supplier;
use Throwable;

class SupplierApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::SUPPLIERS_VIEW);
    }

    try {
      $query = Supplier::query();

      // Apply search filter
      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
          $q->where('name', 'like', "%{$searchTerm}%")
            ->orWhere('phone', 'like', "%{$searchTerm}%");
        });
      }

      if ($request->filled('created_at_from')) {
        $query->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $query->whereDate('created_at', '<=', $request->created_at_to);
      }

      // Apply sorting
      $sortBy = $request->get('sort_by', 'name');
      $sortOrder = $request->get('sort_order', 'asc');

      $allowedSortFields = [
        'name', 'phone', 'created_at', 'updated_at'
      ];

      if (in_array($sortBy, $allowedSortFields)) {
        $query->orderBy($sortBy, $sortOrder);
      } else {
        $query->orderBy('created_at', 'asc');
      }

      if ($request->boolean('paginate')) {
        $perPage = $request->get('per_page', 15);
        $suppliers = $query->paginate($perPage);
      } else {
        $suppliers = $query->get();
      }

      return $this->successResponse(
        data: SupplierResource::collection($suppliers)
      );

    } catch (\Exception $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission(PermissionNames::SUPPLIERS_VIEW);

    try {
      $model = Supplier::findOrFail($id);

      return $this->successResponse(
        data: new SupplierResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {

    $this->authorizePermission(PermissionNames::SUPPLIERS_CREATE);

    $data = $this->validateRequest($request, StoreSupplierRequest::rules());

    try {
      $model = Supplier::create($data);

      if ($request->hasFile(Supplier::IMAGE)) {
        storeWebPWithSpatie($model, $request->file(Supplier::IMAGE), Supplier::IMAGE);
      }

      return $this->successResponse(
        data: new SupplierResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {

    $this->authorizePermission(PermissionNames::SUPPLIERS_UPDATE);

    $data = $this->validateRequest($request, UpdateSupplierRequest::rules());

    try {
      $model = Supplier::findOrFail($id);
      $model->update($data);

      if ($request->hasFile(Supplier::IMAGE)) {
        storeWebPWithSpatie($model, $request->file(Supplier::IMAGE), Supplier::IMAGE);
      }

      return $this->successResponse(
        data: new SupplierResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::SUPPLIERS_DELETE);

    try {
      $model = Supplier::findOrFail($id);
      $model->delete();

      return $this->successResponse(
        data: [
          'total' => Supplier::count()
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
    $this->authorizePermission(PermissionNames::SUPPLIERS_DELETE);

    $data = $this->validateRequest($request, [
      'ids' => 'required|array',
      'ids.*' => 'required|exists:suppliers,id'
    ]);

    try {

      // Only delete suppliers that are in pending status
      $pendingSuppliers = Supplier::whereIn('id', $data['ids'])
        ->pluck('id')
        ->toArray();

      Supplier::whereIn('id', $pendingSuppliers)->delete();

      return $this->successResponse(
        message: count($pendingSuppliers) . ' suppliers deleted successfully',
        data: [
          'total' => Supplier::count()
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
