<?php

namespace Modules\Inventory\Http\Controllers\InventoryTransaction;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Inventory\Http\Requests\InventoryTransaction\StoreInventoryTransactionRequest;
use Modules\Inventory\Http\Requests\InventoryTransaction\UpdateInventoryTransactionRequest;
use Modules\Inventory\Http\Resources\InventoryTransaction\InventoryTransactionResource;
use Modules\Inventory\Models\InventoryItem;
use Modules\Inventory\Models\InventoryTransaction;
use Throwable;

class InventoryTransactionController extends Controller
{

  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::INVENTORYTRANSACTIONS_VIEW);
    }

    try {
      $query = InventoryTransaction::with('supplier', 'inventoryTransactionItems.item');

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
          $q->where('transaction_number', 'like', "%{$searchTerm}%")
            ->orWhereHas('supplier', function ($subQuery) use ($searchTerm) {
              $subQuery->where('name', 'like', "%{$searchTerm}%");
            });
        });
      }

      if ($request->filled('created_at_from')) {
        $query->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $query->whereDate('created_at', '<=', $request->created_at_to);
      }

      $sortBy = $request->get('sort_by', 'created_at');
      $sortOrder = $request->get('sort_order', 'desc');

      $allowedSortFields = ['transaction_number', 'total_amount', 'created_at'];

      if (in_array($sortBy, $allowedSortFields)) {
        $query->orderBy($sortBy, $sortOrder);
      } else {
        $query->latest();
      }

      if ($request->boolean('paginate')) {
        $perPage = $request->get('per_page', 15);
        $transactions = $query->paginate($perPage);
      } else {
        $transactions = $query->get();
      }

      return $this->successResponse(
        data: InventoryTransactionResource::collection($transactions)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }

  }

  public function store(Request $request): JsonResponse
  {

    $this->authorizePermission(PermissionNames::INVENTORYTRANSACTIONS_CREATE);

    $data = $this->validateRequest($request, StoreInventoryTransactionRequest::rules());

    try {
      $totalAmount = 0;

      $transaction = InventoryTransaction::create([
        'transaction_number' => 'TR-' . strtoupper(Str::random(8)),
        'employee_name' => auth()->user()->fullname,
        'supplier_id' => $data['supplier_id'] ?? null,
      ]);

      foreach ($data['items'] as $itemData) {
        $item = InventoryItem::findOrFail($itemData['item_id']);
        $quantity = $itemData['quantity'];

        $item->increment('current_stock', $quantity);
        $unitPrice = $itemData['unit_price'];

        $totalPrice = $quantity * $unitPrice;
        $totalAmount += $totalPrice;

        $transaction->inventoryTransactionItems()->create([
          'item_id' => $item->id,
          'quantity' => $quantity,
          'unit_price' => $unitPrice,
          'total_price' => $totalPrice,
        ]);
      }

      $transaction->total_amount = $totalAmount;
      $transaction->save();

      return $this->successResponse(
        data: new InventoryTransactionResource(
          $transaction->load('supplier', 'inventoryTransactionItems.item')
        ),
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show(string $id): JsonResponse
  {
    $this->authorizePermission(PermissionNames::INVENTORYTRANSACTIONS_VIEW);

    try {

      $transaction = InventoryTransaction::with(['supplier', 'inventoryTransactionItems.item'])->findOrFail($id);

      return $this->successResponse(
        data: new InventoryTransactionResource($transaction)
      );

    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, string $id): JsonResponse
  {
    $this->authorizePermission(PermissionNames::INVENTORYTRANSACTIONS_UPDATE);

    $data = $this->validateRequest($request, UpdateInventoryTransactionRequest::rules());

    try {
      $transaction = InventoryTransaction::with('inventoryTransactionItems.item')->findOrFail($id);

      $transaction->update([
        'employee_name' => auth()->user()->fullname,
        'supplier_id' => $data['supplier_id'],
      ]);

      // Remove old items and re-insert new ones
      $transaction->inventoryTransactionItems()->delete();

      $totalAmount = 0;

      foreach ($request['items'] as $itemData) {
        $item = InventoryItem::findOrFail($itemData['item_id']);
        $quantity = $itemData['quantity'];

        $unitPrice = $itemData['unit_price'];

        $totalPrice = $unitPrice * $quantity;
        $totalAmount += $totalPrice;

        $transaction->inventoryTransactionItems()->create([
          'item_id' => $item->id,
          'quantity' => $quantity,
          'unit_price' => $unitPrice,
          'total_price' => $totalPrice,
        ]);
      }

      $transaction->update(['total_amount' => $totalAmount]);

      return $this->successResponse(
        data: new InventoryTransactionResource(
          $transaction->load('supplier', 'inventoryTransactionItems.item')
        )
      );

    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy(string $id): JsonResponse
  {
    $this->authorizePermission(PermissionNames::INVENTORYTRANSACTIONS_DELETE);

    try {
      $transaction = InventoryTransaction::with('inventoryTransactionItems.item')->findOrFail($id);

      foreach ($transaction->inventoryTransactionItems as $itemRecord) {
        $item = $itemRecord->item;
        $item->decrement('current_stock', $itemRecord->quantity);
      }

      $transaction->inventoryTransactionItems()->delete();
      $transaction->delete();

      return $this->successResponse(
        data: [
          'total' => InventoryTransaction::count()
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
