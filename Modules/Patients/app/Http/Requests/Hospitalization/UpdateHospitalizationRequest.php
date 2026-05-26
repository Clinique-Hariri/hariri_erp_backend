<?php

namespace Modules\Patients\Http\Requests\Hospitalization;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHospitalizationRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
//      'admission_date' => ['required', 'date', 'before_or_equal:today'],
      'stay_length' => ['required', 'integer', 'min:1'],
      'room_number' => ['nullable', 'string', 'max:10'],
      'patient_attendant' => ['nullable', 'string', 'max:100'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
