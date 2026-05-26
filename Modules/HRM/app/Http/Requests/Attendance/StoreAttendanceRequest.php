<?php

namespace Modules\HRM\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;
use Modules\HRM\Constants\AttendanceStatus;

class StoreAttendanceRequest extends FormRequest
{
  public function rules()
  {
    return [
      'employee_id' => ['required', 'exists:employees,id'],
      'date' => ['required', 'date'],
      'check_in_time' => ['nullable', 'required_with:check_out_time', 'date_format:H:i'],
      'check_out_time' => ['nullable', 'date_format:H:i'],
      //'status' => ['nullable', 'string', 'in:' . implode(',', AttendanceStatus::all())],
      'notes' => ['nullable', 'string'],
    ];
  }
}
