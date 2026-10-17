<?php

namespace Modules\Patients\Http\Requests\HomeCheckup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Patients\Constants\HomeCheckupVisitType;

class UpdateHomeCheckupRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'patient_fullname' => ['sometimes', 'string', 'max:255'],
      'patient_phone' => ['sometimes', 'nullable', 'string', 'max:255'],
      'patient_birthdate' => ['sometimes', 'nullable', 'date'],
      'requested_patient_number' => ['sometimes', 'nullable', 'string', 'max:255'],
      'patient_id' => ['sometimes', 'nullable', 'exists:patients,id'],
      'visit_type' => ['sometimes', Rule::in(HomeCheckupVisitType::all())],
      'doctor_id' => ['sometimes', 'nullable', 'exists:doctors,id'],
      'address' => ['sometimes', 'nullable', 'string', 'max:1000'],
      'latitude' => ['sometimes', 'nullable', 'numeric'],
      'longitude' => ['sometimes', 'nullable', 'numeric'],
      'preferred_date' => ['sometimes', 'nullable', 'date'],
      'preferred_time' => ['sometimes', 'nullable', 'date_format:H:i'],
      'scheduled_at' => ['sometimes', 'nullable', 'date'],
      'base_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
      'services_total' => ['sometimes', 'nullable', 'numeric', 'min:0'],
      'total_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
      'coverage_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
      'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
      'services' => ['sometimes', 'nullable', 'array'],
      'services.*.service_name' => ['required_with:services', 'string', 'max:255'],
      'services.*.service_price' => ['required_with:services', 'numeric', 'min:0'],
      'nurse_ids' => ['sometimes', 'nullable', 'array'],
      'nurse_ids.*' => ['exists:employees,id'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
