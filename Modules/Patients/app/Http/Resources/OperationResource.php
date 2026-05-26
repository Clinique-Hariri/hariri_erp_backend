<?php

namespace Modules\Patients\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Actions\Http\Resources\ActionResource;
use Modules\Patients\Constants\OperationStatus;
use Modules\Patients\Http\Resources\SurgeonResource;
use Modules\Patients\Http\Resources\PatientResource;
use Modules\Patients\Models\Operation;

/** @mixin Operation */
class OperationResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'operation_number' => $this->operation_number,
      'operation_date' => $this->operation_date,
      'price' => $this->price,
      'description' => $this->description,
      'status' => OperationStatus::get_resource($this->status),
      'next_statuses' => OperationStatus::get_next_statuses($this->status),
      'patient' => [
        'id' => $this->patient->id,
        'fullname' => $this->patient->fullname,
      ],
      'surgeons' => SurgeonResource::collection($this->whenLoaded('surgeons')),
      'payment_action' => new ActionResource($this->whenLoaded('paymentAction')),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}
