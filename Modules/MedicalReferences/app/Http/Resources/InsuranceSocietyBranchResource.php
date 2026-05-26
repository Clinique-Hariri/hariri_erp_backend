<?php

namespace Modules\MedicalReferences\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MedicalReferences\Models\InsuranceSocietyBranch;

/** @mixin InsuranceSocietyBranch */
class InsuranceSocietyBranchResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'coverage_percentage' => $this->coverage_percentage,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

      'insurance_society_id' => $this->insurance_society_id,

      'insuranceSociety' => new InsuranceSocietyResource($this->insuranceSociety),
    ];
  }
}
