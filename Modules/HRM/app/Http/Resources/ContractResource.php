<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Request;
use Modules\HRM\Models\Contract;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Http\Resources\DesignationMiniResource;
use Modules\HRM\Http\Resources\DepartmentMiniResource;


/** @mixin Contract */
class ContractResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'employee_id' => $this->employee_id,
      'department_id' => $this->department_id,
      'designation_id' => $this->designation_id,
      'start_date' => $this->start_date,
      'end_date' => $this->end_date,
      'basic_salary' => $this->basic_salary,
      'department' => new DepartmentMiniResource($this->department),
      'designation' => new DesignationMiniResource($this->designation),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
