<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRM\Models\Bonus;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\EmployeeBonus;

class BonusSeeder extends Seeder
{
  public function run(): void
  {
    if (env('APP_ENV') === 'local') {
      $bonuses = [
        'مكافأة أداء',
        'حافز شهري',
        'بدل نقل',
        'بدل سكن',
      ];

      Bonus::insert(array_map(function ($name) {
        return [
          'name' => $name,
          'value' => random_int(300, 1000),
          'created_at' => now(),
          'updated_at' => now(),
        ];
      }, $bonuses));

      $employees = Employee::inRandomOrder()->limit(3)->get();
      $allBonuses = Bonus::all();

      foreach ($employees as $employee) {

        $count = rand(1, 2);
        $randomBonuses = $allBonuses->random(min($count, $allBonuses->count()));

        foreach ($randomBonuses as $bonus) {
          EmployeeBonus::updateOrCreate(
            [
              'employee_id' => $employee->id,
              'bonus_id' => $bonus->id,
            ],
            []
          );
        }
      }
    }
  }
}
