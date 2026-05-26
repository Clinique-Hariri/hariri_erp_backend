<?php

namespace Modules\Supplier\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Supplier\Models\Supplier;

class SupplierDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    if (env('APP_ENV') === 'local') {
      // Create Suppliers
      $suppliers = [
        ['name' => 'Global Med Supplies', 'phone' => '555-0101'],
        ['name' => 'PharmaCorp Inc.', 'phone' => '555-0202'],
        ['name' => 'Clinic Essentials', 'phone' => '555-0303'],
      ];

      foreach ($suppliers as $supplier) {
        Supplier::create($supplier);
      }
    }
  }
}
