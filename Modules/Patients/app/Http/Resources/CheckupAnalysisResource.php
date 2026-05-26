<?php

namespace Modules\Patients\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Actions\Http\Resources\ActionResource;
use Modules\Clinic\Http\Resources\DoctorResource;
use Modules\MedicalReferences\Constants\MedicalServiceTypes;
use Modules\MedicalReferences\Http\Resources\InsuranceSocietyBranchResource;
use Modules\Patients\Constants\CheckupAnalysisStatus;
use Modules\Patients\Models\CheckupAnalysis;
use Modules\Patients\Models\CheckupAnalysisService;

/** @mixin CheckupAnalysis */
class CheckupAnalysisResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'checkup_analysis_number' => $this->checkup_analysis_number,
      'type' => MedicalServiceTypes::get_resource($this->type),
      'coverage_amount' => $this->coverage_amount,
      'total_price' => $this->total_price,
      'original_price' => $this->original_price,
      'notes' => $this->notes,
      'orientation' => $this->orientation,
      'doctor_interpretation' => $this->doctor_interpretation,
      'status' => CheckupAnalysisStatus::get_resource($this->status),
      'next_statuses' => CheckupAnalysisStatus::get_next_statuses($this->status),
//      'result_attachment' => $this->getFirstMediaUrl(CheckupAnalysis::RESULT_ATTACHMENT),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

      'checkup_id' => $this->checkup_id,
      'patient' => $this->when(
        $this->relationLoaded('checkup') && $this->checkup->relationLoaded('patient'),
        new PatientResource($this->checkup->patient)
      ),

      'doctor' => $this->when(
        $this->relationLoaded('checkup') && $this->checkup->relationLoaded('doctor'),
        new DoctorResource($this->checkup->doctor)
      ),
      'services' => CheckupAnalysisServiceResource::collection($this->whenLoaded('services')),
      'completedServices' => CheckupAnalysisServiceResource::collection(
        $this->whenLoaded('services', fn() => $this->services->filter(fn($s) => !empty($s->result) || !empty($s->getFirstMediaUrl(CheckupAnalysisService::RESULT_ATTACHMENT))))
      ),
      'uncompletedServices' => CheckupAnalysisServiceResource::collection(
        $this->whenLoaded('services', fn() => $this->services->filter(fn($s) => empty($s->result) && empty($s->getFirstMediaUrl(CheckupAnalysisService::RESULT_ATTACHMENT))))
      ),
      'payment_action' => new ActionResource($this->whenLoaded('paymentAction')),
      'result_action' => new ActionResource($this->whenLoaded('resultAction')),
    ];
  }
}
