<?php

namespace Modules\Inventory\Http\Resources\InventoryItem;

use Modules\Inventory\Http\Resources\ItemsCategory\ItemsCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'barcode' => $this->barcode,
            'category' => $this->whenLoaded('category', function(){
              return [
                'id' => $this->category->id,
                'name' => $this->category->name,
              ];
            }),
            'current_stock' => $this->current_stock,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
