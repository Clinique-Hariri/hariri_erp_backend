<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Constants\LoanStatus;

class LoanResource extends JsonResource
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
            'employee_id' => $this->employee_id,
            'amount' => $this->amount,
            'installment_amount' => $this->installment_amount,
            'total_installments' => $this->total_installments,
            'deduction_date' => $this->deduction_date,
            'paid_amount' => $this->paid_amount,
            'remaining_amount' => $this->remaining_amount,
            'paid_installments' => $this->paid_installments,
            'remaining_installments' => $this->remaining_installments,
            'status' => LoanStatus::get_resource($this->status),
            'employee' => new EmployeeMiniResource($this->employee),
            'installments' => $this->when($request->routeIs('api.loans.show') ,
            LoanInstallmentResource::collection($this->installments)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
