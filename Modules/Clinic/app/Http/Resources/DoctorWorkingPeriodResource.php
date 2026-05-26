<?php

namespace Modules\Clinic\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Models\DoctorWorkingPeriod;

/** @mixin DoctorWorkingPeriod */
class DoctorWorkingPeriodResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'start_time' => $this->start_time->format('H:i'),
      'end_time' => $this->end_time->format('H:i'),
    ];
  }
}
