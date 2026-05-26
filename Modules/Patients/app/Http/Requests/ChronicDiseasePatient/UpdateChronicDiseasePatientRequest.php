<?php

namespace Modules\Patients\Http\Requests\ChronicDiseasePatient;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChronicDiseasePatientRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'notes' => ['nullable', 'string', 'max:1000'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
