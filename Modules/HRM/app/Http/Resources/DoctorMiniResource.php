<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Request;
use Modules\HRM\Models\Employee;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Http\Resources\SpecialityMiniResource;

/** @mixin Employee */
class DoctorMiniResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'checkup_price' => $this->checkup_price,
      'commission_percentages' => $this->commission_percentages,
      'speciality' => $this->whenLoaded('speciality', new SpecialityMiniResource($this->speciality)),
    ];
  }
}
