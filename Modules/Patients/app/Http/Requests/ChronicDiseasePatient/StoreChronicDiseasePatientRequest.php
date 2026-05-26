<?php

namespace Modules\Patients\Http\Requests\ChronicDiseasePatient;

use Illuminate\Foundation\Http\FormRequest;

class StoreChronicDiseasePatientRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'notes' => ['nullable', 'string', 'max:1000'],
      'chronic_disease_id' => ['required', 'exists:chronic_diseases,id'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
