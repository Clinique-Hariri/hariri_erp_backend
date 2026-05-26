<?php

namespace Modules\Inventory\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HRM\Models\Department;
use Modules\Inventory\Models\InventoryItem;
use Modules\Inventory\Models\InventoryTransaction;
use Modules\Inventory\Models\InventoryTransactionItem;
use Modules\Inventory\Models\ItemsCategory;
use Modules\Inventory\Models\SupplyRequest;
use Modules\Inventory\Models\SupplyRequestItem;
use Modules\Supplier\Models\Supplier;

class InventoryDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    if (env('APP_ENV') === 'local') {
      ItemsCategory::create(['name' => 'Gloves']);
      ItemsCategory::create(['name' => 'Gauze']);
      ItemsCategory::create(['name' => 'Antibiotics']);

      // Get created entities for relationships
      $mainWarehouse = Department::firstOrCreate(
        ['name' => 'Main Warehouse'],
        ['name' => 'Main Warehouse']
      );
      $emergencyDept = Department::firstOrCreate(
        ['name' => 'Emergency Department'],
        ['name' => 'Emergency Department']
      );

      $glovesCategory = ItemsCategory::where('name', 'Gloves')->first();
      $gauzeCategory = ItemsCategory::where('name', 'Gauze')->first();
      $antibioticsCategory = ItemsCategory::where('name', 'Antibiotics')->first();
      $pharmaCorp = Supplier::firstOrCreate(
        ['name' => 'PharmaCorp Inc.'],
        ['phone' => '555-0202']
      );
      $globalMed = Supplier::firstOrCreate(
        ['name' => 'Global Med Supplies'],
        ['phone' => '555-0101']
      );

      // ==============================================
      // Create Inventory Items and Initial Stock
      // ==============================================

      // 1. Amoxicillin (some expired, some not)
      $amoxicillin = InventoryItem::firstOrCreate(
        ['barcode' => 'MED-AMX-600'],
        [
          'name' => 'Amoxicillin',
          'category_id' => $antibioticsCategory->id,
          'current_stock' => 100,
        ]
      );

      // 2. Medical Gauze
      $medicalGauze = InventoryItem::firstOrCreate(
        ['barcode' => 'DISP-GAU-001'],
        [
          'name' => 'Medical gauze',
          'category_id' => $gauzeCategory->id,
          'current_stock' => 100,
        ]
      );

      // 3. Medical Gloves
      $medicalGloves = InventoryItem::firstOrCreate(
        [
          'barcode' => 'DISP-GLV-001'
        ],
        [
          'name' => 'DISP-GLV-001',
          'category_id' => $glovesCategory->id,
          'current_stock' => 25,
        ]
      );

      // ==============================================
      // Create Inventory Transactions
      // ==============================================

      // IN Transaction 1: Expired Amoxicillin
      $in_trans_1 = InventoryTransaction::firstOrCreate(
        ['transaction_number' => 'PO-2023-001'],
        [
          'supplier_id' => $pharmaCorp->id,
          'employee_name' => 'Admin',
          'total_amount' => 5 * 15.50,
        ]
      );
      if (!$in_trans_1->wasRecentlyCreated) {
        $existingItem = InventoryTransactionItem::where('transaction_id', $in_trans_1->id)
          ->where('item_id', $amoxicillin->id)
          ->first();
        if (!$existingItem) {
          InventoryTransactionItem::create([
            'transaction_id' => $in_trans_1->id,
            'item_id' => $amoxicillin->id,
            'quantity' => 5,
            'unit_price' => 15.50,
            'total_price' => 5 * 15.50,
          ]);
          $amoxicillin->increment('current_stock', 5);
        }
      }

      // IN Transaction 2: Good Amoxicillin
      $in_trans_2 = InventoryTransaction::firstOrCreate(
        ['transaction_number' => 'PO-2024-002'],
        [
          'supplier_id' => $pharmaCorp->id,
          'employee_name' => 'Admin',
          'total_amount' => 32 * 15.50,
        ]
      );
      if (!$in_trans_2->wasRecentlyCreated) {
        $existingItem = InventoryTransactionItem::where('transaction_id', $in_trans_2->id)
          ->where('item_id', $amoxicillin->id)
          ->first();
        if (!$existingItem) {
          InventoryTransactionItem::create([
            'transaction_id' => $in_trans_2->id,
            'item_id' => $amoxicillin->id,
            'quantity' => 32,
            'unit_price' => 15.50,
            'total_price' => 32 * 15.50,
          ]);
          $amoxicillin->increment('current_stock', 32);
        }
      }

      // IN Transaction 3: Medical Gauze
      $in_trans_3 = InventoryTransaction::firstOrCreate(
        ['transaction_number' => 'PO-2024-003'],
        [
          'supplier_id' => $globalMed->id,
          'employee_name' => 'Admin',
          'total_amount' => 200 * 5.00,
        ]
      );
      if (!$in_trans_3->wasRecentlyCreated) {
        $existingItem = InventoryTransactionItem::where('transaction_id', $in_trans_3->id)
          ->where('item_id', $medicalGauze->id)
          ->first();
        if (!$existingItem) {
          InventoryTransactionItem::create([
            'transaction_id' => $in_trans_3->id,
            'item_id' => $medicalGauze->id,
            'quantity' => 200,
            'unit_price' => 5.00,
            'total_price' => 200 * 5.00,
          ]);
          $medicalGauze->update(['current_stock' => 200]);
        }
      }

      // IN Transaction 4: Medical Gloves (to make stock low)
      $in_trans_4 = InventoryTransaction::firstOrCreate(
        ['transaction_number' => 'PO-2024-004'],
        [
          'supplier_id' => $globalMed->id,
          'employee_name' => 'Admin',
          'total_amount' => 25 * 8.20,
        ]
      );
      if (!$in_trans_4->wasRecentlyCreated) {
        $existingItem = InventoryTransactionItem::where('transaction_id', $in_trans_4->id)
          ->where('item_id', $medicalGloves->id)
          ->first();
        if (!$existingItem) {
          InventoryTransactionItem::create([
            'transaction_id' => $in_trans_4->id,
            'item_id' => $medicalGloves->id,
            'quantity' => 25,
            'unit_price' => 8.20,
            'total_price' => 25 * 8.20,
          ]);
          $medicalGloves->update(['current_stock' => 25]);
        }
      }


      // ==============================================
      // Create Supply Requests
      // ==============================================

      // Create Supply Request 1: Emergency Department
      $supplyRequest1 = SupplyRequest::firstOrCreate(
        ['request_number' => 'SR-2024-001'],
        [
          'department_id' => $emergencyDept->id,
          'status' => 'pending',
          'requested_by' => 'Dr. John Doe',
        ]
      );

      if (!$supplyRequest1->wasRecentlyCreated) {
        // Clear existing items if request already exists
        $supplyRequest1->supplyRequestItems()->delete();
      }

      // Add items to Supply Request 1
      SupplyRequestItem::create([
        'supply_request_id' => $supplyRequest1->id,
        'item_id' => $medicalGloves->id,
        'requested_quantity' => 10,
      ]);

      SupplyRequestItem::create([
        'supply_request_id' => $supplyRequest1->id,
        'item_id' => $medicalGauze->id,
        'requested_quantity' => 20,
      ]);

      // Create Supply Request 2: Main Warehouse
      $supplyRequest2 = SupplyRequest::firstOrCreate(
        ['request_number' => 'SR-2024-002'],
        [
          'department_id' => $mainWarehouse->id,
          'status' => 'approved',
          'requested_by' => 'Admin',
          'approved_by' => 'Store Manager',
        ]
      );

      if (!$supplyRequest2->wasRecentlyCreated) {
        // Clear existing items if request already exists
        $supplyRequest2->supplyRequestItems()->delete();
      }

      // Add items to Supply Request 2
      SupplyRequestItem::create([
        'supply_request_id' => $supplyRequest2->id,
        'item_id' => $amoxicillin->id,
        'requested_quantity' => 50,
      ]);

      SupplyRequestItem::create([
        'supply_request_id' => $supplyRequest2->id,
        'item_id' => $medicalGauze->id,
        'requested_quantity' => 100,
      ]);
    }
  }
}
