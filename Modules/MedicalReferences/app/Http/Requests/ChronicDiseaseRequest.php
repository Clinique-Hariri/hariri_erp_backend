<?php

namespace Modules\MedicalReferences\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChronicDiseaseRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string', 'max:1000'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
