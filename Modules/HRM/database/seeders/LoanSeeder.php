<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Loan;
use Modules\HRM\Models\LoanInstallment;
use Modules\HRM\Constants\InstallmentStatus;

class LoanSeeder extends Seeder
{
    public function run(): void
    {

        $employees = Employee::inRandomOrder()->limit(3)->get();

        $startDate = '2025-07-01';

        $configs = [
            [
                'amount' => 3000,
                'installment_amount' => 500,
                'total_installments' => 6,
                'deduction_date' => $startDate,
            ],
            [
                'amount' => 5000,
                'installment_amount' => 1000,
                'total_installments' => 5,
                'deduction_date' => $startDate,
            ],
            [
                'amount' => 2400,
                'installment_amount' => 400,
                'total_installments' => 6,
                'deduction_date' => $startDate,
            ],
        ];

        $employees = $employees->values();
        foreach ($employees as $idx => $employee) {
            $config = $configs[$idx];

            $loan = Loan::create([
                'employee_id' => $employee->id,
                'amount' => $config['amount'],
                'installment_amount' => $config['installment_amount'],
                'total_installments' => $config['total_installments'],
                'deduction_date' => $config['deduction_date'],
            ]);

            $installments = [];
            $loanDate = Carbon::parse($config['deduction_date'])->startOfMonth();

            for ($i = 1; $i <= $config['total_installments']; $i++) {
                $installments[] = [
                    'loan_id' => $loan->id,
                    'number' => $i,
                    'month' => $loanDate->addMonth()->toDateString(),
                    'amount' => $config['installment_amount'],
                    'status' => InstallmentStatus::PENDING,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            LoanInstallment::insert($installments);
        }
    }
}
