<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Request;
use Modules\HRM\Models\CareerChange;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Constants\CareerChangeType;

/** @mixin CareerChange */
class CareerChangeResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'employee_id' => $this->employee_id,
      'old_contract_id' => $this->old_contract_id,
      'new_contract_id' => $this->new_contract_id,
      'type' => CareerChangeType::get_resource($this->type),
      'notes' => $this->notes,
      'file' => $this->getLastMediaUrl(CareerChange::FILE),

      'employee' => new EmployeeMiniResource($this->whenLoaded('employee')),
      'old_contract' => new ContractResource($this->whenLoaded('oldContract')),
      'new_contract' => new ContractResource($this->whenLoaded('newContract')),

      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

    ];
  }
}
