<?php

namespace Modules\Patients\Http\Requests\Hospitalization;

use Illuminate\Foundation\Http\FormRequest;

class StoreHospitalizationRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
//      'admission_date' => ['required', 'date', 'before_or_equal:today'],
      'stay_length' => ['required', 'integer', 'min:1'],
      'room_number' => ['nullable', 'string', 'max:255'],
      'patient_attendant' => ['nullable', 'string', 'max:255'],
      'patient_id' => ['required', 'exists:patients,id'],
      'doctor_id' => ['nullable', 'exists:doctors,id' ],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
