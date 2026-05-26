<?php

namespace Modules\Patients\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MedicalReferences\Http\Resources\MedicalServiceResource;
use Modules\Patients\Models\CheckupAnalysisService;

/** @mixin CheckupAnalysisService */
class CheckupAnalysisServiceResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'service_name' => $this->medicalService->name,
      'service_price' => $this->service_price,
      'result' => $this->result,
      'result_attachment' => $this->getFirstMediaUrl(CheckupAnalysisService::RESULT_ATTACHMENT),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

      'medical_service_id' => $this->medical_service_id,
      'checkup_analysis_id' => $this->checkup_analysis_id,

      'medical_service' => new MedicalServiceResource($this->whenLoaded('medicalService')),
    ];
  }
}
