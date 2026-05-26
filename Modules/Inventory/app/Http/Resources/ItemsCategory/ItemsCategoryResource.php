<?php

namespace Modules\Inventory\Http\Resources\ItemsCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemsCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_items' => $this->inventoryItems->count(),
            'created_at' => $this->created_at,
        ];
    }
}
