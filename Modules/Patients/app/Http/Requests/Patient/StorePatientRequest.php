<?php

namespace Modules\Patients\Http\Requests\Patient;

use App\Constants\Gender;
use App\Constants\BloodType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Modules\Patients\Constants\PatientStatus;

class StorePatientRequest extends FormRequest
{
  public static function rules(): array
  {
    return [
      'fullname' => ['required', 'string', 'max:255'],
      'gender' => ['required', 'string', 'in:' . implode(',', Gender::all())],
      'blood_type' => ['nullable', 'string', 'in:' . implode(',', BloodType::all())],
      'birthdate' => ['nullable', 'date'],
      'age' => ['required', 'numeric', 'min:0', 'max:150'],
      'full_address' => ['nullable', 'string', 'max:500'],
      'passport_number' => ['nullable', 'string', 'max:50'],
      'birth_place' => ['nullable', 'string', 'max:255'],
      'phone' => ['nullable', 'string', 'max:20', 'regex:/^(?:\+|00)?\d{7,15}$/'],
      'external_patient_id' => ['nullable', 'numeric', 'min:1'],
      'insurance_society_branch_id' => ['nullable', 'exists:insurance_society_branches,id'],
      'insurance_number' => ['nullable', 'string', 'max:255', 'unique:patients,insurance_number'],
      'insured_name' => ['nullable', 'string', 'max:255'],
      'whatsapp_number' => ['nullable', 'string', 'max:20', 'regex:/^(?:\+|00)?\d{7,15}$/', 'unique:patients,whatsapp_number'],
      'email' => ['nullable', 'email', 'max:255', 'unique:patients,email'],
      'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:10240'],
    ];
  }
}
