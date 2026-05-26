<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRM\Models\Department;
use Symfony\Component\HttpFoundation\File\File;

class DepartmentsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    if (env('APP_ENV') === 'local') {

      $names = [
        'قسم الطوارئ',
        'المستودع الرئيسي',
        'قسم الجراحة',
        'طب الأطفال',
        'العناية المركزة',
      ];

      Department::insert(array_map(function($name){
        return [
          'name' => $name,
          'created_at' => now(),
          'updated_at' => now(),
        ];
      }, $names));
    }
  }
}
