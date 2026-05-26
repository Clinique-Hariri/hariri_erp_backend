<?php

namespace Modules\HRM\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;
use Modules\HRM\Constants\AttendanceStatus;

class UpdateAttendanceRequest extends FormRequest
{
  public function rules()
  {
    return [
      'check_in_time' => ['nullable', 'date_format:H:i'],
      'check_out_time' => ['nullable', 'date_format:H:i'],
      'status' => ['nullable', 'string', 'in:' . implode(',', AttendanceStatus::all())],
      'notes' => ['nullable', 'string'],
    ];
  }
}
