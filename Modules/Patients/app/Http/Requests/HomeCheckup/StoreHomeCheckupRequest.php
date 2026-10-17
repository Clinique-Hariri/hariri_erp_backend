<?php

namespace Modules\Patients\Http\Requests\HomeCheckup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Patients\Constants\HomeCheckupVisitType;

class StoreHomeCheckupRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'mobile_request_id' => ['required', 'string', 'max:255', 'unique:home_checkups,mobile_request_id'],
      'patient_fullname' => ['required', 'string', 'max:255'],
      'patient_phone' => ['nullable', 'string', 'max:255'],
      'patient_birthdate' => ['nullable', 'date'],
      'requested_patient_number' => ['nullable', 'string', 'max:255'],
      'patient_id' => ['nullable', 'exists:patients,id'],
      'requested_visit_type' => ['required', Rule::in(HomeCheckupVisitType::all())],
      'visit_type' => ['required', Rule::in(HomeCheckupVisitType::all())],
      'doctor_id' => ['nullable', 'exists:doctors,id'],
      'address' => ['nullable', 'string', 'max:1000'],
      'latitude' => ['nullable', 'numeric'],
      'longitude' => ['nullable', 'numeric'],
      'preferred_date' => ['nullable', 'date'],
      'preferred_time' => ['nullable', 'date_format:H:i'],
      'scheduled_at' => ['nullable', 'date'],
      'base_price' => ['nullable', 'numeric', 'min:0'],
      'services_total' => ['nullable', 'numeric', 'min:0'],
      'total_price' => ['nullable', 'numeric', 'min:0'],
      'coverage_amount' => ['nullable', 'numeric', 'min:0'],
      'notes' => ['nullable', 'string', 'max:1000'],
      'services' => ['nullable', 'array'],
      'services.*.service_name' => ['required_with:services', 'string', 'max:255'],
      'services.*.service_price' => ['required_with:services', 'numeric', 'min:0'],
      'nurse_ids' => ['nullable', 'array'],
      'nurse_ids.*' => ['exists:employees,id'],
    ];
  }

  public function authorize(): bool
  {
    return true;
  }
}
