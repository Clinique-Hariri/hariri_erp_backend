<?php

namespace Modules\Patients\Http\Resources;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Http\Resources\DoctorResource;
use Modules\Patients\Models\Surgeon;

/** @mixin Surgeon */
class SurgeonResource extends JsonResource
{
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'doctor_commission_percentage' => $this->doctor_commission_percentage,
      'doctor' => new DoctorResource($this->whenLoaded('doctor'))
    ];
  }
}
