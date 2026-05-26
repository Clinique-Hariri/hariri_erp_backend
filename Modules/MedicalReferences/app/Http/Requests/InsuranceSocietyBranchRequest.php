<?php

namespace Modules\MedicalReferences\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsuranceSocietyBranchRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'coverage_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
