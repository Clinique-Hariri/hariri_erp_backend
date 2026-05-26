<?php

namespace Modules\Patients\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Http\Resources\DoctorResource;
use Modules\Patients\Http\Resources\CheckupResource;
use Modules\Patients\Models\Prescription;

/** @mixin Prescription */
class PrescriptionResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

      'doctor_id' => $this->doctor_id,
      'checkup_id' => $this->checkup_id,

      'medicines' => PrescriptionMedicineResource::collection($this->whenLoaded('medicines')),
      'checkup' => new CheckupResource($this->whenLoaded('checkup')),
      'doctor' => new DoctorResource($this->whenLoaded('doctor')),
      'patient' => $this->when(
        $this->relationLoaded('checkup') && $this->checkup->relationLoaded('patient'),
        new PatientResource($this->checkup->patient)
      ),
    ];
  }
}
