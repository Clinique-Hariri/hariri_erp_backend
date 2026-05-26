<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Departments\Database\Seeders\DepartmentsDatabaseSeeder;
use Modules\Patients\Database\Seeders\PatientsDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $this->call(RoleSeeder::class);
    $this->call(PermissionSeeder::class);
    $this->call(UserSeeder::class);

    if (env('APP_ENV') === 'local') {
//      $this->call(DepartmentsDatabaseSeeder::class);
//      $this->call(PatientsDatabaseSeeder::class);
    }
  }
}
