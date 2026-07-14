<?php

namespace Modules\HRM\Services;

use Exception;
use Carbon\Carbon;
use Modules\HRM\Models\Contract;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\CareerChange;
use Modules\HRM\Models\Salary;
use Modules\HRM\Constants\SalaryStatus;

class ContractService
{
    protected SalaryService $salaryService;

    public function __construct(SalaryService $salaryService)
    {
        $this->salaryService = $salaryService;
    }

    /**
     * Create a new contract for an employee with overlap validation.
     *
     * @param array $data
     * @return Contract
     * @throws Exception
     */
    public function create(array $data): Contract
    {
        $this->validateDateOverlap($data['employee_id'], $data['start_date'], $data['end_date']);

        return Contract::create($data);
    }

    /**
     * Update a contract with guardrails.
     *
     * @param Contract $contract
     * @param array $data
     * @return Contract
     * @throws Exception
     */
    public function update(Contract $contract, array $data): Contract
    {
        // Check if any dates are changing
        $datesChanged = (
            (isset($data['start_date']) && $contract->start_date->ne($data['start_date'])) ||
            (isset($data['end_date']) && !$contract->end_date && $data['end_date'] !== null) ||
            (isset($data['end_date']) && $contract->end_date && $contract->end_date->ne($data['end_date']))
        );

        // Check if basic_salary is changing
        $basicSalaryChanged = (
            isset($data['basic_salary']) &&
            $contract->basic_salary != $data['basic_salary']
        );

        // Guard 1: If dates changed, validate no overlap
        if ($datesChanged) {
            $startDate = $data['start_date'] ?? $contract->start_date;
            $endDate = $data['end_date'] ?? $contract->end_date;
            $this->validateDateOverlap($contract->employee_id, $startDate, $endDate, $contract->id);
        }

        // Guard 2: Block if pending CareerChange exists referencing this contract as old_contract_id
        $hasPendingCareerChange = CareerChange::where('old_contract_id', $contract->id)
            ->whereHas('newContract', function ($q) {
                $q->whereDate('start_date', '>', now());
            })
            ->exists();

        if ($hasPendingCareerChange) {
            throw new Exception('Cannot edit contract: a pending career change is scheduled. Cancel or revert it first.');
        }

        // Guard 3: Block basic_salary change if current month salary is already PAID
        if ($basicSalaryChanged) {
            $currentSalary = Salary::where('employee_id', $contract->employee_id)
                ->whereMonth('month', now()->month)
                ->whereYear('month', now()->year)
                ->first();

            if ($currentSalary && $currentSalary->status === SalaryStatus::PAID) {
                throw new Exception('Cannot change basic salary: the current month salary is already paid.');
            }
        }

        // Update the contract
        $contract->update($data);

        // Side effect: Regenerate current month salary on basic_salary change
        if ($basicSalaryChanged && (!$currentSalary || $currentSalary->status !== SalaryStatus::PAID)) {
            $this->salaryService->generate(now()->toDateString(), $contract->employee);
        }

        return $contract->fresh();
    }

    /**
     * Validate that given dates don't overlap with any existing contracts for the employee.
     *
     * @param int $employeeId
     * @param mixed $startDate
     * @param mixed $endDate
     * @param int|null $excludeContractId
     * @return void
     * @throws Exception
     */
    protected function validateDateOverlap(int $employeeId, $startDate, $endDate, ?int $excludeContractId = null): void
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $query = Contract::where('employee_id', $employeeId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            });

        if ($excludeContractId) {
            $query->where('id', '!=', $excludeContractId);
        }

        if ($query->exists()) {
            throw new Exception('Contract dates overlap with an existing contract.');
        }
    }
}
