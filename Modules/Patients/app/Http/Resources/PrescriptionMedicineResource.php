<?php

namespace Modules\Patients\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Patients\Models\PrescriptionMedicine;

/** @mixin PrescriptionMedicine */
class PrescriptionMedicineResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'medicine_name' => $this->medicine_name,
      'dosage' => $this->dosage,
      'instructions' => $this->instructions,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

      'prescription_id' => $this->prescription_id,
    ];
  }
}
