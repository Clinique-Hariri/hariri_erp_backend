<?php

namespace Modules\Patients\Http\Requests\Checkup;

use App\Support\Enum\UserTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Patients\Constants\CheckupStatus;

class StoreCheckupRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'date' => ['required', 'date', 'after_or_equal:today'],
      'time' => ['nullable', 'date_format:H:i'],
      'reason' => ['nullable', 'string', 'max:1000'],
      'height' => ['nullable', 'numeric', 'min:0'],
      'weight' => ['nullable', 'numeric', 'min:0'],
      'temperature' => ['nullable', 'numeric', 'min:0'],
      'systolic_pressure' => ['nullable', 'numeric', 'min:0'],
      'diastolic_pressure' => ['nullable', 'numeric', 'min:0'],
      'SPO2' => ['nullable', 'numeric', 'min:0', 'max:100'],
      'FC' => ['nullable', 'numeric', 'min:0'],
      'symptoms' => ['nullable', 'string', 'max:1000'],
      'doctor_id' => ['required', 'exists:doctors,id'],
      'patient_id' => ['required', 'exists:patients,id'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
