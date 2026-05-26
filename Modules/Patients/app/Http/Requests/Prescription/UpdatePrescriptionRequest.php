<?php

namespace Modules\Patients\Http\Requests\Prescription;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrescriptionRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'medicines' => ['required', 'array'],
      'medicines.*.medicine_name' => ['required', 'string', 'max:255'],
      'medicines.*.dosage' => ['required', 'string', 'max:255'],
      'medicines.*.instructions' => ['nullable', 'string', 'max:1000'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
