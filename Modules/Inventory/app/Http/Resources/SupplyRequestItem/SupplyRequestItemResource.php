<?php

namespace Modules\Inventory\Http\Resources\SupplyRequestItem;

use Modules\Inventory\Http\Resources\InventoryItem\InventoryItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplyRequestItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item' => $this->whenLoaded('item', function () {
                return [
                    'id' => $this->item->id,
                    'name' => $this->item->name,
                    'barcode' => $this->item->barcode,
                    'current_stock' => $this->item->current_stock,
                ];
            }),
            'requested_quantity' => $this->requested_quantity,
        ];
    }
}
