<?php

namespace Modules\Patients\Http\Resources;

use App\Constants\BloodType;
use App\Constants\Gender;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MedicalReferences\Http\Resources\InsuranceSocietyBranchResource;
use Modules\MedicalReferences\Http\Resources\InsuranceSocietyResource;
use Modules\Patients\Constants\PatientStatus;
use Modules\Patients\Models\Patient;

/** @mixin Patient */
class PatientResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'patient_number' => $this->patient_number,
      'fullname' => $this->fullname,
      'gender' => Gender::get_resource($this->gender),
      'blood_type' => BloodType::get_resource($this->blood_type),
      'birthdate' => $this->birthdate?->format('Y-m-d'),
      'age' => $this->age,
      'birth_place' => $this->birth_place,
      'full_address' => $this->full_address,
      'avatar' => $this->avatar_url,
      'insurance_number' => $this->insurance_number,
      'passport_number' => $this->passport_number,
      'phone' => $this->phone,
      'whatsapp_number' => $this->whatsapp_number,
      'email' => $this->email,
      'insured_name' => $this->insured_name ?? $this->fullname,
      'status' => PatientStatus::get_resource($this->status),
      'next_statuses' => PatientStatus::get_next_statuses($this->status),
      'external_patient_id' => $this->external_patient_id,
      'insurance_society_branch_id' => $this->insurance_society_branch_id,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

      'insurance_society_branch' => new InsuranceSocietyBranchResource($this->insuranceSocietyBranch),
    ];
  }
}

