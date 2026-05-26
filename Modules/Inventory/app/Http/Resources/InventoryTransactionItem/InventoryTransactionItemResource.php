<?php
namespace Modules\Inventory\Http\Resources\InventoryTransactionItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Inventory\Models\InventoryTransactionItem;

/** @mixin InventoryTransactionItem */

class InventoryTransactionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item' => $this->whenLoaded('item', function() {
              return [
                'id' => $this->item->id,
                'name' => $this->item->name,
                'barcode' => $this->item->barcode,
                'current_stock' => $this->item->current_stock,
              ];
            }),
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
        ];
    }
}
