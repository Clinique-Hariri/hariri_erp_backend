<?php

namespace Modules\MedicalReferences\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Http\Resources\DoctorResource;
use Modules\MedicalReferences\Models\InsuranceSocietyCheckupPricing;

/** @mixin InsuranceSocietyCheckupPricing */
class InsuranceSocietyCheckupPricingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'checkup_price' => $this->checkup_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'insurance_society_id' => $this->insurance_society_id,
            'doctor_id' => $this->doctor_id,

            'insuranceSociety' => new InsuranceSocietyResource($this->whenLoaded('insuranceSociety')),
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
        ];
    }
}
