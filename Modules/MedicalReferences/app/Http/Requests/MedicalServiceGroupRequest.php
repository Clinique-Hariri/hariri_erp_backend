<?php

namespace Modules\MedicalReferences\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalServiceGroupRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
