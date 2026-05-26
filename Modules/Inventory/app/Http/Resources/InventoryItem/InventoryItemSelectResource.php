<?php
namespace Modules\Inventory\Http\Resources\InventoryItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array for Select2 dropdown
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'barcode' => $this->barcode,
            'current_stock' => $this->current_stock,
        ];
    }
}
