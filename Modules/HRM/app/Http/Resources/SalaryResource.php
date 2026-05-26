<?php

namespace Modules\HRM\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Actions\Http\Resources\ActionResource;
use Modules\HRM\Constants\SalaryStatus;
use Modules\HRM\Models\Salary;

/** @mixin Salary */
class SalaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'month' => $this->month,
            'basic_salary' => $this->basic_salary,
            'daily_wage' => $this->daily_wage,
            'total_bonuses' => $this->total_bonuses,
            'total_deduction' => $this->total_deduction,
            'work_days' => $this->work_days,
            'absent_days' => $this->absent_days,
            'net_salary' => $this->net_salary,
            'status' => SalaryStatus::get_resource($this->status),
            'next_statuses' => SalaryStatus::get_next_statuses($this->status),
            'pay_date' => $this->pay_date,
            'report' => $this->getFirstMediaUrl(Salary::REPORT),
            'employee' => new EmployeeMiniResource($this->employee),
            'bonuses' => $this->when(
                $this->whenLoaded('bonuses'),
                SalaryBonusResource::collection($this->bonuses)
            ),
            'deductions' => $this->when(
                $this->whenLoaded('deductions'),
                SalaryDeductionResource::collection($this->deductions)
            ),
            'payment_action' => new ActionResource($this->whenLoaded('paymentAction')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
