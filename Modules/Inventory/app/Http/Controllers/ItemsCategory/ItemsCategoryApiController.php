<?php

namespace Modules\Inventory\Http\Controllers\ItemsCategory;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\ItemsCategory\StoreItemsCategoryRequest;
use Modules\Inventory\Http\Requests\ItemsCategory\UpdateItemsCategoryRequest;
use Modules\Inventory\Http\Resources\ItemsCategory\ItemsCategoryResource;
use Modules\Inventory\Models\ItemsCategory;
use Throwable;

class ItemsCategoryApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::ITEMSCATEGORY_VIEW);
    }

    try {
      $query = ItemsCategory::query();

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
          $q->where('name', 'like', "%{$searchTerm}%");
        });
      }

      if ($request->filled('created_at_from')) {
        $query->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $query->whereDate('created_at', '<=', $request->created_at_to);
      }

      $sortBy = $request->get('sort_by', 'created_at');
      $sortOrder = $request->get('sort_order', 'asc');

      $allowedSortFields = ['name', 'created_at'];

      if (in_array($sortBy, $allowedSortFields)) {
        $query->orderBy($sortBy, $sortOrder);
      } else {
        $query->orderBy('created_at', 'asc');
      }

      if ($request->boolean('paginate')) {
        $perPage = $request->get('per_page', 15);
        $categories = $query->paginate($perPage);
      } else {
        $categories = $query->get();
      }

      return $this->successResponse(
        data: ItemsCategoryResource::collection($categories)
      );

    } catch (\Exception $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission(PermissionNames::ITEMSCATEGORY_VIEW);

    try {
      $model = ItemsCategory::findOrFail($id);

      return $this->successResponse(
        data: new ItemsCategoryResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {

    $this->authorizePermission(PermissionNames::ITEMSCATEGORY_CREATE);

    $data = $this->validateRequest($request, StoreItemsCategoryRequest::rules());

    try {
      $model = ItemsCategory::create($data);

      return $this->successResponse(
        data: new ItemsCategoryResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {

    $this->authorizePermission(PermissionNames::ITEMSCATEGORY_UPDATE);

    $data = $this->validateRequest($request, UpdateItemsCategoryRequest::rules());

    try {
      $model = ItemsCategory::findOrFail($id);
      $model->update($data);

      return $this->successResponse(
        data: new ItemsCategoryResource($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::ITEMSCATEGORY_DELETE);

    try {
      $model = ItemsCategory::findOrFail($id);
      $model->delete();

      return $this->successResponse(
        data: [
          'total' => ItemsCategory::count()
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
