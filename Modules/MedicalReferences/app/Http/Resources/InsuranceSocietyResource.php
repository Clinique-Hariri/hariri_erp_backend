<?php

namespace Modules\MedicalReferences\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MedicalReferences\Models\InsuranceSociety;

/** @mixin InsuranceSociety */
class InsuranceSocietyResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
