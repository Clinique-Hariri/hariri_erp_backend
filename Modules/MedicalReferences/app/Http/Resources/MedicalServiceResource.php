<?php

namespace Modules\MedicalReferences\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MedicalReferences\Constants\MedicalServiceTypes;
use Modules\MedicalReferences\Models\MedicalService;

/** @mixin MedicalService */
class MedicalServiceResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'type' => MedicalServiceTypes::get_resource($this->type),
      'price' => $this->price,
      'group_id' => $this->group_id,
      'group' => new MedicalServiceGroupResource($this->whenLoaded('group')),

      'result_type' => $this->result_type,
      'min_normal_value' => $this->min_normal_value,
      'max_normal_value' => $this->max_normal_value,
      'normal_values' => $this->normal_values,
      'unit' => $this->unit,

      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

    ];
  }
}
