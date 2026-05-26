<?php

namespace Modules\MedicalReferences\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\MedicalReferences\Constants\MedicalServiceTypes;

class MedicalServiceRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'type' => ['required', 'in:' . implode(',', MedicalServiceTypes::all())],
//      'group_id' => ['required', 'exists:medical_service_groups,id'],
      'price' => ['required', 'numeric', 'min:0'],
      'result_type' => ['required', 'integer', 'in:1,2,3,4,5,6,7,8'],
      'min_normal_value' => ['required_if:result_type,2', 'string'],
      'max_normal_value' => ['required_if:result_type,2', 'string'],
      'normal_values' => ['nullable', 'string'],
      'unit' => ['required_if:result_type,2,7', 'string', 'max:50'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
