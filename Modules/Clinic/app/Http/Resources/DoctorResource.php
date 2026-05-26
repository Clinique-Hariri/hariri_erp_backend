<?php

namespace Modules\Clinic\Http\Resources;

use App\Constants\Gender;
use Illuminate\Http\Request;
use Modules\Clinic\Http\Resources\SpecialityMiniResource;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Attendance;
use Modules\Clinic\Models\Doctor;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Doctor */
class DoctorResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'fullname' => $this->user?->employee?->fullname,
      'phone' => $this->user?->employee?->phone,
      'avatar' => $this->user?->avatar_url,
      'checkup_price' => $this->checkup_price,
      'commission_percentages' => $this->commission_percentages,

      'speciality' => new SpecialityMiniResource($this->whenLoaded('speciality')),

      'user_id' => $this->user_id,
      'speciality_id' => $this->speciality_id,

      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
