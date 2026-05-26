<?php

namespace Modules\HRM\Services;

use Exception;
use Carbon\Carbon;
use Modules\HRM\Constants\ContractStatus;
use Modules\HRM\Models\Contract;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\CareerChange;
use Modules\HRM\Constants\CareerChangeType;
use Illuminate\Http\UploadedFile;

class CareerChangeService
{
  /**
   * Create a career change with all validations and relationships.
   */
  public function create(array $data, ?UploadedFile $file = null): CareerChange
  {
    $employee = Employee::findOrFail($data['employee_id']);
    $employee->load('contract');
    $oldContract = $employee->contract;
    $careerChange = null;

    if ($data['type'] == CareerChangeType::TRANSFER) {
      $this->ensureContractStatusIsValid($employee, ContractStatus::ACTIVE);
      $this->ensureHasNextMonthContract($employee);
      $this->ensureCareerChangeNotExceedsMonthlyLimit($employee);
      $this->ensureEmployeeNotInSameDepartment($employee, $data['department_id']);

      $startDate = now()->addMonth()->startOfMonth();

      $newContract = Contract::create([
        'employee_id' => $employee->id,
        'department_id' => $data['department_id'],
        'designation_id' => $oldContract->designation_id,
        'start_date' => $startDate,
        'end_date' => $employee->contract->end_date,
        'basic_salary' => $oldContract->basic_salary,
        'notes' => $data['notes'] ?? null,
      ]);
    } elseif ($data['type'] == CareerChangeType::RAISE) {
      $this->ensureContractStatusIsValid($employee, ContractStatus::ACTIVE);
      $this->ensureHasNextMonthContract($employee);
      $this->ensureCareerChangeNotExceedsMonthlyLimit($employee);
      $this->ensureRaiseSalaryIncreaseIsValid($employee, $data);

      $startDate = now()->addMonth()->startOfMonth();

      $newContract = Contract::create([
        'employee_id' => $employee->id,
        'department_id' => $oldContract->department_id,
        'designation_id' => $oldContract->designation_id,
        'start_date' => $startDate,
        'end_date' => $employee->contract->end_date,
        'basic_salary' => $data['basic_salary'],
        'notes' => $data['notes'] ?? null,
      ]);
    } elseif ($data['type'] == CareerChangeType::PROMOTION) {
      $this->ensureContractStatusIsValid($employee, ContractStatus::ACTIVE);
      $this->ensureHasNextMonthContract($employee);
      $this->ensureCareerChangeNotExceedsMonthlyLimit($employee);
      $this->ensurePromotionChangesPosition($employee, $data);
      $this->ensurePromotionSalaryIncreaseIsValid($employee, $data);

      $startDate = now()->addMonth()->startOfMonth();

      $newContract = Contract::create([
        'employee_id' => $employee->id,
        'department_id' => $data['department_id'],
        'designation_id' => $data['designation_id'],
        'start_date' => $startDate,
        'end_date' => $employee->contract->end_date,
        'basic_salary' => $data['basic_salary'],
        'notes' => $data['notes'] ?? null,
      ]);
    } elseif ($data['type'] == CareerChangeType::RENEWAL) {
      $startDate = now()->addMonth()->startOfMonth();
      $endDate = isset($data['end_date'])
        ? Carbon::parse($data['end_date'])->endOfMonth()
        : $startDate->copy()->endOfMonth();
      $this->ensureCareerChangeNotExceedsMonthlyLimit($employee);
      $this->ensureValidRenewalDateRange($startDate, $endDate);

      $newContract = Contract::create([
        'employee_id' => $employee->id,
        'department_id' => $data['department_id'],
        'designation_id' => $data['designation_id'],
        'start_date' => $startDate,
        'end_date' => $endDate,
        'basic_salary' => $data['basic_salary'],
        'notes' => $data['notes'] ?? null,
      ]);

    } elseif ($data['type'] == CareerChangeType::TERMINATION) {
      //$terminationDate = Carbon::parse($data['start_date']);
      $this->ensureCareerChangeNotExceedsMonthlyLimit($employee);
      $this->ensureContractStatusIsValid($employee, ContractStatus::ACTIVE);
      //$this->ensureTerminationDateIsValid($employee, $terminationDate);

      //$startDate = $terminationDate->copy()->addMonth()->startOfMonth();

      $startDate = now()->addMonth()->startOfMonth();

      $newContract = null;
    } else {
      throw new Exception('Invalid career change type');
    }

    $oldContract?->update([
      'end_date' => $startDate->subDay(),
    ]);

    $careerChange = CareerChange::create([
      'employee_id' => $employee->id,
      'old_contract_id' => $oldContract?->id,
      'new_contract_id' => $newContract?->id,
      'type' => $data['type'],
      'notes' => $data['notes'] ?? null,
    ]);

    if ($file) {
      $careerChange
        ->addMedia($file)
        ->toMediaCollection(CareerChange::FILE);
    }

    return $careerChange->load([
      'employee',
      'oldContract',
      'newContract',
    ]);
  }

  /**
   * Ensure employee has valid contract status
   */
  private function ensureContractStatusIsValid(Employee $employee, string $status): void
  {
    if ($employee->contract_status !== $status) {
      throw new Exception("Employee contract status must be {$status}");
    }
  }

  /**
   * Ensure employee has an active contract next month
   */
  private function ensureHasNextMonthContract(Employee $employee): void
  {
    if (!$employee->hasContractAt(now()->addMonth()->firstOfMonth())) {
      throw new Exception('Employee has no active contract next month, you should renew first');
    }
  }

  /**
   * Ensure career change doesn't exceed monthly limit
   */
  private function ensureCareerChangeNotExceedsMonthlyLimit(Employee $employee): void
  {
    if (
      $employee->careerChanges()
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->exists()
    ) {
      throw new Exception('You can only make 1 career change per employee per month');
    }
  }

  /**
   * Ensure employee is not already in the same department
   */
  private function ensureEmployeeNotInSameDepartment(Employee $employee, int $departmentId): void
  {
    if ($employee->contract->department_id == $departmentId) {
      throw new Exception('Employee is already in this department');
    }
  }

  /**
   * Ensure salary increase is valid for raises
   */
  private function ensureRaiseSalaryIncreaseIsValid(Employee $employee, array $data): void
  {
    if (!isset($data['basic_salary']) || $employee->contract->basic_salary >= $data['basic_salary']) {
      throw new Exception('New salary must be greater than current salary');
    }
  }

  /**
   * Ensure salary increase is valid for raises
   */
  private function ensurePromotionSalaryIncreaseIsValid(Employee $employee, array $data): void
  {
    if (!isset($data['basic_salary']) || $employee->contract->basic_salary > $data['basic_salary']) {
      throw new Exception('New salary must be greater or equal than current salary');
    }
  }

  /**
   * Ensure promotion actually changes employee's position
   */
  private function ensurePromotionChangesPosition(Employee $employee, array $data): void
  {
    if (
      $employee->contract->designation_id == $data['designation_id'] &&
      $employee->contract->department_id == $data['department_id']
    ) {
      throw new Exception('Employee already has this designation in this department');
    }
  }

  /**
   * Ensure valid date range for renewal contracts
   */
  private function ensureValidRenewalDateRange(Carbon $startDate, Carbon $endDate): void
  {
    if ($startDate->isAfter($endDate)) {
      throw new Exception('End date must be after' . $startDate->format('Y-m-d'));
    }
  }

  /**
   * Ensure termination date is valid
   */
  private function ensureTerminationDateIsValid(Employee $employee, Carbon $terminationDate): void
  {

    if ($terminationDate->isBefore(now()->startOfDay())) {
      throw new Exception('Termination date cannot be in the past');
    }

    if (
      $terminationDate->isBefore($employee->contract->start_date) ||
      $terminationDate->isAfter($employee->contract->end_date)
    ) {
      throw new Exception('Termination date must be within the employee\'s current contract period');
    }
  }

}
