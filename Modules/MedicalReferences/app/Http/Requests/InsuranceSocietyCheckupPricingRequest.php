<?php

namespace Modules\MedicalReferences\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsuranceSocietyCheckupPricingRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'checkup_price' => ['required', 'numeric', 'min:0'],
      'insurance_society_id' => ['required', 'exists:insurance_societies,id'],
      'doctor_id' => ['required', 'exists:doctors,id'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
