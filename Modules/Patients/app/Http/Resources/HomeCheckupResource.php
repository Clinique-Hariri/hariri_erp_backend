<?php

namespace Modules\Patients\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Http\Resources\DoctorResource;
use Modules\HRM\Http\Resources\EmployeeMiniResource;
use Modules\Patients\Constants\HomeCheckupStatus;
use Modules\Patients\Constants\HomeCheckupVisitType;
use Modules\Patients\Models\HomeCheckup;

/** @mixin HomeCheckup */
class HomeCheckupResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'home_checkup_number' => $this->home_checkup_number,
      'mobile_request_id' => $this->mobile_request_id,

      'patient_fullname' => $this->patient_fullname,
      'patient_phone' => $this->patient_phone,
      'patient_birthdate' => $this->patient_birthdate?->format('Y-m-d'),
      'requested_patient_number' => $this->requested_patient_number,
      'patient' => new PatientResource($this->whenLoaded('patient')),

      'requested_visit_type' => HomeCheckupVisitType::get_resource($this->requested_visit_type),
      'visit_type' => HomeCheckupVisitType::get_resource($this->visit_type),

      'doctor' => new DoctorResource($this->whenLoaded('doctor')),
      'checkup' => new CheckupResource($this->whenLoaded('checkup')),

      'address' => $this->address,
      'latitude' => $this->latitude,
      'longitude' => $this->longitude,

      'preferred_date' => $this->preferred_date?->format('Y-m-d'),
      'preferred_time' => $this->preferred_time?->format('H:i'),
      'scheduled_at' => $this->scheduled_at?->format('Y-m-d H:i:s'),

      'status' => HomeCheckupStatus::get_resource($this->status),
      'next_statuses' => HomeCheckupStatus::get_next_statuses($this->status),

      'base_price' => $this->base_price,
      'services_total' => $this->services_total,
      'total_price' => $this->total_price,
      'coverage_amount' => $this->coverage_amount,

      'notes' => $this->notes,

      'services' => HomeCheckupServiceResource::collection($this->whenLoaded('services')),
      'nurses' => EmployeeMiniResource::collection($this->whenLoaded('nurses')),

      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
