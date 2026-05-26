<?php

namespace Modules\Supplier\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Supplier\Models\Supplier;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'last_transaction_date' => $this->inventoryTransactions()->latest()->first()->created_at ?? null,
            'image' => $this->getFirstMediaUrl(Supplier::IMAGE),
        ];
    }
}
