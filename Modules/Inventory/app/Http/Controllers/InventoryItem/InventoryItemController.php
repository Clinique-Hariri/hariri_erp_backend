<?php

namespace Modules\Inventory\Http\Controllers\InventoryItem;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Inventory\Http\Requests\InventoryItem\StoreInventoryItemRequest;
use Modules\Inventory\Http\Requests\InventoryItem\UpdateInventoryItemRequest;
use Modules\Inventory\Http\Resources\InventoryItem\InventoryItemResource;
use Modules\Inventory\Http\Resources\InventoryItem\InventoryItemSelectResource;
use Modules\Inventory\Models\InventoryItem;

class InventoryItemController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request): JsonResponse
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::INVENTORYITEM_VIEW);
    }

    try {
      $query = InventoryItem::with(['category']);

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
          $q->where('name', 'like', "%{$searchTerm}%")
            ->orWhere('barcode', 'like', "%{$searchTerm}%");
        });
      }

      if ($request->filled('category_id')) {
        $categoryIds = is_array($request->category_id) ? $request->category_id : [$request->category_id];
        $query->whereIn('category_id', $categoryIds);
      }

      if ($request->filled('created_at_from')) {
        $query->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $query->whereDate('created_at', '<=', $request->created_at_to);
      }

      $sortBy = $request->get('sort_by', 'created_at');
      $sortOrder = $request->get('sort_order', 'desc');

      $allowedSortFields = ['id', 'name', 'barcode', 'current_stock', 'created_at'];

      if (in_array($sortBy, $allowedSortFields)) {
        $query->orderBy($sortBy, $sortOrder);
      } else {
        $query->latest();
      }

      if ($request->boolean('paginate')) {
        $perPage = $request->get('per_page', 15);
        $items = $query->paginate($perPage);
      } else {
        $items = $query->get();
      }

      return $this->successResponse(
        data: InventoryItemResource::collection($items)
      );

    } catch (\Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function all(Request $request): JsonResponse
  {
    $this->authorizePermission(PermissionNames::INVENTORYITEM_VIEW);

    try {
      $items = InventoryItem::select('id', 'name', 'barcode')->with(['category'])->get();
      return $this->successResponse(
        data: InventoryItemSelectResource::collection($items)
      );
    } catch (\Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Store a newly created inventory item
   */
  public function store(Request $request): JsonResponse
  {
    $this->authorizePermission(PermissionNames::INVENTORYITEM_CREATE);

    $data = $this->validateRequest($request, StoreInventoryItemRequest::rules());

    try {

      $item = InventoryItem::create($data);

      return $this->successResponse(
        data: new InventoryItemResource(
          $item->load('category')
        )
      );

    } catch (\Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Display the specified inventory item
   */
  public function show(string $id): JsonResponse
  {
    $this->authorizePermission(PermissionNames::INVENTORYITEM_VIEW);

    try {
      $inventoryItem = InventoryItem::with(['category'])->findOrFail($id);

      return $this->successResponse(
        data: new InventoryItemResource($inventoryItem)
      );

    } catch (\Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  /**
   * Update the specified inventory item
   */
  public function update(Request $request, string $id): JsonResponse
  {
    $this->authorizePermission(PermissionNames::INVENTORYITEM_UPDATE);

    $data = $this->validateRequest($request, UpdateInventoryItemRequest::rules($id));

    try {
      $inventoryItem = InventoryItem::findOrFail($id);
      $inventoryItem->update($data);

      return $this->successResponse(
        data: new InventoryItemResource(
          $inventoryItem->load('category')
        )
      );
    } catch (\Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }

  }

  /**
   * Remove the specified inventory item
   */
  public function destroy(string $id): JsonResponse
  {
    $this->authorizePermission(PermissionNames::INVENTORYITEM_DELETE);

    try {
      $inventoryItem = InventoryItem::findOrFail($id);

      $inventoryItem->delete();
      return $this->successResponse(
        data: [
          'total' => InventoryItem::count()
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
    } catch (ModelNotFoundException $e) {
      return $this->errorResponse('Inventory item not found.', 404);
    } catch (\Exception $e) {
      return $this->errorResponse('Failed to delete inventory item.', 500);
    }
  }
}
