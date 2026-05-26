<?php

namespace Modules\Clinic\Http\Resources;

use App\Support\Enum\WeekDays;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Models\DoctorSchedule;

/** @mixin DoctorSchedule */
class DoctorScheduleResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'day' => WeekDays::get_resource($this->day_of_week),
      'working_periods' => DoctorWorkingPeriodResource::collection($this->workingPeriods),

      'doctor_id' => $this->doctor_id,

      'doctor' => new DoctorResource($this->whenLoaded('doctor')),
    ];
  }
}
