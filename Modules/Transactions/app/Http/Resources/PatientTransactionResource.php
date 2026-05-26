<?php

namespace Modules\Transactions\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Patients\Http\Resources\PatientResource;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\Type;
use Modules\Transactions\Models\Transaction;

/** @mixin Transaction */
class PatientTransactionResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    $transactionable = $this->relationLoaded('transactionable') ? $this->transactionable : null;

    $patient = null;
    if ($transactionable) {
      $patient = $transactionable->patient ?? null;
    }

    return [
      'id' => $this->id,
      'transaction_number' => $this->transaction_number,
      'amount' => $this->amount,
      'details' => $this->details,
      'type' => Type::get_resource($this->type),
      'status' => Status::get_resource($this->status),
      'next_statuses' => Status::get_next_statuses($this->status),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

      'user_id' => $this->user_id,
      'user' => $this->whenLoaded('user', function () {
        return [
          'fullname' => $this->user->fullname,
        ];
      }),

      'transactionable' => $this->whenLoaded('transactionable', function () {
        return [
          'type' => class_basename($this->transactionable_type),
          'id' => $this->transactionable_id,
        ];
      }),

      'patient' => $patient ? new PatientResource($patient) : null,
    ];
  }
}
