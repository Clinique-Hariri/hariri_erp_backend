<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Models\Employee;

/** @mixin Employee */
class EmployeeMiniResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'employee_code' => $this->employee_code,
      'fullname' => $this->fullname,
      'image_url' => $this->getFirstMediaUrl(Employee::IMAGE),
    ];
  }
}