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
     * Generate salaries for the specified month (YYYY-MM or full date), defaulting to last month.
     * Returns a summary array with month, created count, and skipped count.
     */
    public function generate(?string $month = null): array
    {
        $targetStart = $month
            ? Carbon::parse($month)->startOfMonth()
            : now()->subMonth()->startOfMonth();
        $targetEnd = $targetStart->copy()->endOfMonth();

        $createdCount = 0;
        $skippedCount = 0;

        // Get employees with an active contract at any point in the target month
        $employees = Employee::whereHasContractAt($targetStart)->get();

        foreach ($employees as $employee) {
            // Skip when salary already exists for this month
            $exists = Salary::where('employee_id', $employee->id)
                ->whereYear('month', $targetStart->year)
                ->whereMonth('month', $targetStart->month)
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            // Get the contract effective during the month
            $contract = $employee->contract($targetStart)->first();
            if (!$contract) {
                $skippedCount++;
                continue;
            }

            // Basic salary logic: full-time uses basic salary, commission may not have fixed base
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

            // Compute bonuses based on assigned employee bonuses
            $employee->loadMissing('bonuses'); // through relation
            $bonuses = $employee->bonuses;
            $totalBonuses = 0.0;

            // Create salary within transaction with bonuses and deductions
            DB::transaction(function () use (
                $employee,
                $contract,
                $targetStart,
                $basicSalary,
                $dailyWage,
                $workDays,
                $absentDays,
                $bonuses,
                &$totalBonuses,
                &$createdCount
            ) {
                $salary = Salary::create([
                    'employee_id' => $employee->id,
                    'month' => $targetStart->toDateString(),
                    'basic_salary' => $basicSalary,
                    'daily_wage' => $dailyWage,
                    'total_bonuses' => 0,
                    'total_deduction' => 0,
                    'work_days' => $workDays,
                    'absent_days' => $absentDays,
                    'net_salary' => 0,
                    'status' => SalaryStatus::DRAFT,
                    'pay_date' => null,
                ]);

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

                // Loan installments for the month; apply only if balance covers them, otherwise mark OVERDUE
                $installments = LoanInstallment::whereDate('month', $targetStart->toDateString())
                    ->whedailyWagere('status', '!=', InstallmentStatus::PAID)
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

                $createdCount++;
            });
        }

        return [
            'month' => $targetStart->toDateString(),
            'created' => $createdCount,
            'skipped' => $skippedCount,
        ];
    }
}
