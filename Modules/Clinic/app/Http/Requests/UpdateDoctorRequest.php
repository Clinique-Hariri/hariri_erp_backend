<?php

namespace Modules\Clinic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'speciality_id' => ['nullable', 'exists:specialities,id'],
      'checkup_price' => ['nullable', 'numeric', 'min:0'],
      'commission_percentages' => ['nullable', 'array'],
      'commission_percentages.checkup' => ['nullable', 'numeric', 'min:0', 'max:100'],
      'commission_percentages.analysis' => ['nullable', 'numeric', 'min:0', 'max:100'],
      'commission_percentages.hospitalization' => ['nullable', 'numeric', 'min:0', 'max:100'],
      'home_checkup_eligible' => ['nullable', 'boolean'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
