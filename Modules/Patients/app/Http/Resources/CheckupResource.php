<?php

namespace Modules\Patients\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Actions\Http\Resources\ActionResource;
use Modules\Clinic\Http\Resources\DoctorResource;
use Modules\MedicalReferences\Http\Resources\InsuranceSocietyBranchResource;
use Modules\Patients\Constants\CheckupStatus;
use Modules\Patients\Models\Checkup;

/** @mixin Checkup */
class CheckupResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'checkup_number' => $this->checkup_number,
      'initial_price' => $this->initial_price,
      'total_price' => $this->total_price,
      'original_price' => $this->original_price,
      'coverage_amount' => $this->coverage_amount,
      'date' => $this->date?->format('Y-m-d'),
      'time' => $this->time?->format('H:i'),
      'status' => CheckupStatus::get_resource($this->status),
      'next_statuses' => CheckupStatus::get_next_statuses($this->status),
      'reason' => $this->reason,
      'weight' => $this->weight,
      'height' => $this->height,
      'temperature' => $this->temperature,
      'systolic_pressure' => $this->systolic_pressure,
      'diastolic_pressure' => $this->diastolic_pressure,
      'SPO2' => $this->SPO2,
      'FC' => $this->FC,
      'symptoms' => $this->symptoms,
      'patient' => new PatientResource($this->whenLoaded('patient')),
      'doctor' =>  new DoctorResource($this->whenLoaded('doctor')),
      'ticket' => new CheckupTicketResource($this->whenLoaded('ticket')),
      'unpaidCheckupAnalyses' => CheckupAnalysisResource::collection($this->whenLoaded('unpaidCheckupAnalyses')),
      'paidCheckupAnalyses' => CheckupAnalysisResource::collection($this->whenLoaded('paidCheckupAnalyses')),
      'payment_action' => new ActionResource($this->whenLoaded('paymentAction')),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
