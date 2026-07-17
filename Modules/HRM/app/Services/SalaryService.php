<?php

namespace Modules\HRM\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Salary;
use Modules\HRM\Models\SalaryBonus;
use Modules\HRM\Models\SalaryDeduction;
use Modules\HRM\Models\Attendance;
use Modules\HRM\Models\LoanInstallment;
use Modules\HRM\Constants\AttendanceStatus;
use Modules\HRM\Constants\InstallmentStatus;
use Modules\HRM\Constants\DeductionType;
use Modules\HRM\Constants\SalaryStatus;

class SalaryService
{
    /**
     * Generate or update salary for a single employee for the specified month.
     * Returns the Salary model or throws exception on error.
     */
    public function generate(string $month, Employee $employee): Salary
    {
        $targetStart = Carbon::parse($month)->startOfMonth();
        $targetEnd = $targetStart->copy()->endOfMonth();

        // Skip if salary is not DRAFT (already PROCESSED or PAID)
        $existing = Salary::where('employee_id', $employee->id)
            ->whereDate('month', $targetStart->toDateString())
            ->first();

        if ($existing && $existing->status !== SalaryStatus::DRAFT) {
            return $existing;
        }

        // Get the contract effective during the month
        $contract = $employee->contract($targetStart)->first();
        if (!$contract) {
            throw new \Exception('Employee has no active contract for the specified month.');
        }

        // Basic salary logic
        $basicSalary = (float) ($contract->basic_salary ?? 0);
        $daysInMonth = $targetStart->daysInMonth;
        $dailyWage = round($basicSalary / $daysInMonth, 2);

        // Attendance counts
        $attendanceQuery = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$targetStart->toDateString(), $targetEnd->toDateString()]);

        $workDays = (clone $attendanceQuery)
            ->whereIn('status', [AttendanceStatus::PRESENT, AttendanceStatus::LATE])
            ->count();

        $absentDays = (clone $attendanceQuery)
            ->where('status', AttendanceStatus::ABSENT)
            ->count();

        // Compute bonuses
        $employee->loadMissing('bonuses');
        $bonuses = $employee->bonuses;
        $totalBonuses = 0.0;

        return DB::transaction(function () use (
            $employee,
            $contract,
            $targetStart,
            $basicSalary,
            $dailyWage,
            $workDays,
            $absentDays,
            $bonuses,
            &$totalBonuses
        ) {
            $salary = Salary::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month' => $targetStart->toDateString(),
                ],
                [
                    'basic_salary' => $basicSalary,
                    'daily_wage' => $dailyWage,
                    'total_bonuses' => 0,
                    'total_deduction' => 0,
                    'work_days' => $workDays,
                    'absent_days' => $absentDays,
                    'net_salary' => 0,
                    'status' => SalaryStatus::DRAFT,
                    'pay_date' => null,
                ]
            );

            // Clear existing bonuses and deductions when updating
            $salary->bonuses()->delete();
            $salary->deductions()->delete();

            // Bonuses
            foreach ($bonuses as $bonus) {
                $amount = round((float)$bonus->value, 2);
                $totalBonuses += $amount;

                SalaryBonus::create([
                    'salary_id' => $salary->id,
                    'bonus_id' => $bonus->id,
                    'name' => $bonus->name,
                    'amount' => $amount,
                ]);
            }

            // Current balance before loan deductions
            $netBalance = round($basicSalary + $totalBonuses, 2);

            // Deductions
            $totalDeductions = 0.0;

            // Loan installments for the month
            $installments = LoanInstallment::whereDate('month', $targetStart->toDateString())
                ->where('status', '!=', InstallmentStatus::PAID)
                ->whereHas('loan', function ($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })
                ->get();

            foreach ($installments as $inst) {
                $amount = round((float)$inst->amount, 2);

                if ($amount > 0 && $netBalance >= $amount) {
                    $totalDeductions += $amount;

                    SalaryDeduction::create([
                        'salary_id' => $salary->id,
                        'loan_installment_id' => $inst->id,
                        'type' => DeductionType::LOAN,
                        'amount' => $amount,
                    ]);

                    $inst->update(['status' => InstallmentStatus::PAID]);

                    $netBalance = round($netBalance - $amount, 2);
                } else {
                    $inst->update(['status' => InstallmentStatus::OVERDUE]);
                }
            }

            $net = round(($basicSalary + $totalBonuses) - $totalDeductions, 2);

            $salary->update([
                'total_bonuses' => $totalBonuses,
                'total_deduction' => $totalDeductions,
                'net_salary' => $net,
            ]);

            return $salary;
        });
    }

    /**
     * Generate salaries for the specified month (YYYY-MM or full date), defaulting to last month.
     * Returns a summary array with month, created count, and skipped count.
     */
    public function generateAll(?string $month = null): array
    {
        $targetStart = $month
            ? Carbon::parse($month)->startOfMonth()
            : now()->startOfMonth();

        $createdCount = 0;
        $skippedCount = 0;

        // Get employees with an active contract at any point in the target month
        $employees = Employee::whereHasContractAt($targetStart)->get();

        foreach ($employees as $employee) {
            try {
                $this->generate($targetStart->toDateString(), $employee);
                $createdCount++;
            } catch (\Exception $e) {
                $skippedCount++;
            }
        }

        return [
            'month' => $targetStart->toDateString(),
            'created' => $createdCount,
            'skipped' => $skippedCount,
        ];
    }
}
