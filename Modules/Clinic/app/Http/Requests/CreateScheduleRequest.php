<?php

namespace Modules\Clinic\Http\Requests;

use App\Support\Enum\WeekDays;
use Illuminate\Foundation\Http\FormRequest;

class CreateScheduleRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'start_time' => ['required', 'date_format:H:i'],
      'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
      'days_of_week' => ['required', 'array', 'min:1', 'max:7'],
      'days_of_week.*' => ['in:' . implode(',', WeekDays::lists())],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
