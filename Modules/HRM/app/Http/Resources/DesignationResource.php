<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignationResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'base_salary' => $this->base_salary,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
