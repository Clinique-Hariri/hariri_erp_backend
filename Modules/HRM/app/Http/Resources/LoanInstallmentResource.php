<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Constants\InstallmentStatus;

class LoanInstallmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'loan_id' => $this->loan_id,
            'number' => $this->number,
            'month' => $this->month->format('Y-m-d'),
            'amount' => $this->amount,
            'status' => InstallmentStatus::get_resource($this->status),
            //'notes' => $this->notes,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ];
    }
}
