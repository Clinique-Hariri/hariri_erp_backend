<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HRM\Models\SalaryDeduction;
use Modules\HRM\Constants\DeductionType;

/** @mixin SalaryDeduction */
class SalaryDeductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'salary_id' => $this->salary_id,
            'loan_installment_id' => $this->loan_installment_id,
            'type' => DeductionType::get_resource($this->type),
            'amount' => $this->amount,
            'loan_installment' => $this->when(
                $this->loanInstallment,
                new LoanInstallmentResource($this->loanInstallment)
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
