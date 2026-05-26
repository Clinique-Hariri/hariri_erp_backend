<?php

namespace Modules\Patients\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Actions\Http\Resources\ActionResource;
use Modules\Clinic\Http\Resources\DoctorResource;
use Modules\MedicalReferences\Http\Resources\InsuranceSocietyBranchResource;
use Modules\Patients\Constants\HospitalizationStatus;
use Modules\Patients\Models\Hospitalization;

/** @mixin Hospitalization */
class HospitalizationResource extends JsonResource
{
  private function calculateRemainingAmount(Hospitalization $model)
  {
    if ($model->status == HospitalizationStatus::DRAFT){
      return $model->initial_price;
    } elseif ($model->status == HospitalizationStatus::ADMITTED) {
      $now = Carbon::now();
      $scheduledDischarge = Carbon::parse($model->discharge_date);
      $extendedStayLength = max(0, $scheduledDischarge->diffInHours($now, false));
      return Hospitalization::calculateHospitalizationPrice($extendedStayLength);
    } else {
      return 0;
    }
  }

  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'hospitalization_number' => $this->hospitalization_number,
      'admission_date' => $this->admission_date?->format('Y-m-d H:i'),
      'discharge_date' => $this->discharge_date?->format('Y-m-d H:i'),
      'stay_length' => $this->stay_length,
      'room_number' => $this->room_number,
      'patient_attendant' => $this->patient_attendant,
      'initial_price' => $this->initial_price,
      'total_price' => $this->total_price,
      'remaining_amount' => $this->calculateRemainingAmount($this->resource),
      'status' => HospitalizationStatus::get_resource($this->status),
      'next_statuses' => HospitalizationStatus::get_next_statuses($this->status),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

      'patient_id' => $this->patient_id,
      'doctor_id' => $this->doctor_id,
      'patient' => new PatientResource($this->whenLoaded('patient')),
      'doctor' => new DoctorResource($this->whenLoaded('doctor')),
      'insurance_society_branch' => new InsuranceSocietyBranchResource($this->whenLoaded('patient.insuranceSocietyBranch')),
      'payment_action' => new ActionResource($this->whenLoaded('paymentAction')),
      'extension_action' => new ActionResource($this->whenLoaded('extensionAction')),
    ];
  }
}
