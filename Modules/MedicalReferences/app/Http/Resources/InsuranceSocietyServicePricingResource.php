<?php

namespace Modules\MedicalReferences\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MedicalReferences\Models\InsuranceSocietyServicePricing;

/** @mixin InsuranceSocietyServicePricing */
class InsuranceSocietyServicePricingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'medical_service_price' => $this->medical_service_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'insurance_society_id' => $this->insurance_society_id,
            'medical_service_id' => $this->medical_service_id,

            'insuranceSociety' => new InsuranceSocietyResource($this->whenLoaded('insuranceSociety')),
            'medicalService' => new MedicalServiceResource($this->whenLoaded('medicalService')),
        ];
    }
}
