<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;

class HRMDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    if (env('APP_ENV') === 'local') {
      $this->call([
        DesignationSeeder::class,
        DepartmentsSeeder::class,
        EmployeeSeeder::class,
        ContractSeeder::class,
        BonusSeeder::class,
        LoanSeeder::class,
        AttendanceSeeder::class,
      ]);
    }
  }
}
