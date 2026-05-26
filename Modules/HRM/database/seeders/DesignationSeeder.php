<?php

namespace Modules\HRM\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HRM\Models\Designation;

class DesignationSeeder extends Seeder
{
  public function run(): void
  {
    if (env('APP_ENV') === 'local') {
      $designations = [
        'طبيب مقيم',
        'طبيب اختصاصي',
        'طبيب استشاري',
        'رئيس القسم الطبي',
        'ممرض',
        'ممرض رئيسي',
        'فني مختبر',
        'أخصائي أشعة',
        'مدير إداري',
        'مسؤول الموارد البشرية',
        'محاسب',
        'سكرتير',
        'مسؤول الاستقبال',
        'مسؤول المخازن',
        'أمن',
        'عامل نظافة'
      ];

      Designation::insert(array_map(function ($name) {
        return [
          'name' => $name,
          'base_salary' => random_int(8000, 25000),
          'created_at' => now(),
          'updated_at' => now()
        ];
      }, $designations));
    }
  }
}
