<?php

namespace Modules\HRM\Http\Resources;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Constants\ContractStatus;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Attendance;
use App\Constants\Gender;

/** @mixin Employee */
class EmployeeResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'user_id' => $this->user_id,
      'employee_code' => $this->employee_code,
      'fullname' => $this->fullname,
      'phone' => $this->phone,
      'email' => $this->email,
      'gender' => Gender::get_resource($this->gender),
      'address' => $this->address,
      'birth_date' => $this->birth_date,
      'hire_date' => $this->hire_date,
      'image_url' => $this->getFirstMediaUrl(Employee::IMAGE),
      'work_months' => $this->work_months,
      'loans_count' => $this->loans_count,
      'contract_status' => ContractStatus::get_resource($this->contract_status),
      'user' => $this->when(
        $request->routeIs('api.employees.*'),
        new UserResource($this->user)
      ),
      'doctor' => $this->when(
        $request->routeIs('api.employees.*'),
        new DoctorMiniResource($this->doctor)
      ),
      'contract' => $this->when(
        $request->routeIs('api.employees.*'),
        new ContractResource($this->contract)
      ),
      'attendance' => $this->when(
        $request->routeIs('api.attendances.*'),
        $this->getJoinedAttendance($request)
      ),

      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }

  private function getJoinedAttendance(Request $request)
  {

      $attendance = new Attendance([
        'employee_id' => $this->attendance_employee_id,
        'date' => $this->attendance_date,
        'check_in_time' => $this->check_in_time,
        'check_out_time' => $this->check_out_time,
        'duration' => $this->duration,
        'status' => $this->status,
        'notes' => $this->notes,
      ]);

      $attendance->id = $this->attendance_id;
      $attendance->created_at = $this->attendance_created_at;
      $attendance->updated_at = $this->attendance_updated_at;
      $attendance->exists = true;

      return new AttendanceResource($attendance);

  }
}
