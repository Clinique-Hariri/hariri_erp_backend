<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Contract;

class ContractSeeder extends Seeder
{
  public function run(): void
  {
    if (env('APP_ENV') === 'local') {
      $contracts = [
        [
          'department_id' => 1,
          'designation_id' => 1,
          'basic_salary' => 16000,
        ],
        [
          'department_id' => 2,
          'designation_id' => 14,
          'basic_salary' => 6500,
        ],
        [
          'department_id' => 3,
          'designation_id' => 3,
          'basic_salary' => 8000,
        ],
        [
          'department_id' => 4,
          'designation_id' => 2,
          'basic_salary' => 18000,
        ],
        [
          'department_id' => 5,
          'designation_id' => 6,
          'basic_salary' => 10000,
        ],
      ];

      $employees = Employee::orderBy('id', 'DESC')->take(count($contracts))->get();

      foreach ($employees as $index => $employee) {
        $config = $contracts[$index];

        Contract::create([
          'employee_id' => $employee->id,
          'department_id' => $config['department_id'],
          'designation_id' => $config['designation_id'],
          'start_date' => '2025-07-01',
          'end_date' => '2030-06-30',
          'basic_salary' => $config['basic_salary'],
        ]);
      }
    }
  }
}
