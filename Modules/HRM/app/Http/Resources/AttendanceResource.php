<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Models\Attendance;
use Modules\HRM\Constants\AttendanceStatus;

/** @mixin Attendance */
class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            //'employee_id' => $this->employee_id,
            'date' => $this->date,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'duration' => $this->duration,
            'status' => $this->status ? AttendanceStatus::get_resource($this->status) : null,
            'next_statuses' => $this->status ? AttendanceStatus::get_next_statuses($this->status) : null,
            'notes' => $this->notes,
            //'employee' => new EmployeeMiniResource($this->employee),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
