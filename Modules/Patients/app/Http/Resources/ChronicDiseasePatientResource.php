<?php

namespace Modules\Patients\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MedicalReferences\Http\Resources\ChronicDiseaseResource;
use Modules\Patients\Models\ChronicDiseasePatient;

/** @mixin ChronicDiseasePatient */
class ChronicDiseasePatientResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'notes' => $this->notes,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

      'patient_id' => $this->patient_id,
      'chronic_disease_id' => $this->chronic_disease_id,

      'chronicDisease' => new ChronicDiseaseResource($this->whenLoaded('chronicDisease')),
    ];
  }
}
