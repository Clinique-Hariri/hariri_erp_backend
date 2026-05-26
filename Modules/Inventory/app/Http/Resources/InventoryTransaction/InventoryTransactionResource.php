<?php

namespace Modules\Inventory\Http\Resources\InventoryTransaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Http\Resources\DepartmentResource;
use Modules\Inventory\Http\Resources\InventoryTransactionItem\InventoryTransactionItemResource;
use Modules\Inventory\Models\InventoryTransaction;
use Modules\Supplier\Http\Resources\SupplierResource;
/** @mixin InventoryTransaction */

class InventoryTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_number' => $this->transaction_number,
            'supplier' => $this->whenLoaded('supplier', function() {
              return [
                'id' => $this->supplier->id,
                'name' => $this->supplier->name,
              ];
            }),
            'total_amount' => $this->total_amount,
            'created_at' => $this->created_at,
            'total_items' => $this->inventoryTransactionItems->count(),
            'items' => InventoryTransactionItemResource::collection($this->whenLoaded('inventoryTransactionItems')),
        ];
    }
}
