<?php

namespace Modules\Inventory\Http\Resources\SupplyRequest;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Http\Resources\DepartmentResource;
use Modules\Inventory\Http\Resources\SupplyRequestItem\SupplyRequestItemResource;

class SupplyRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_number' => $this->request_number,
            'department' => $this->whenLoaded('department', function () {
                return [
                    'id' => $this->department->id,
                    'name' => $this->department->name,
                ];
            }),
            'status' => $this->status,
            'requested_by' => $this->requested_by,
            'items' => SupplyRequestItemResource::collection($this->whenLoaded('supplyRequestItems')),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
