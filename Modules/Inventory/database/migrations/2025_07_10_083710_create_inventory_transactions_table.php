<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('inventory_transactions', function (Blueprint $table) {
      $table->id();
      $table->string('transaction_number', 50)->unique();
      $table->string('employee_name', 100)->nullable();
      $table->decimal('total_amount', 10, 2)->default(0.00);
      $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
      $table->timestamps();

      $table->index('supplier_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('inventory_transactions');
  }
};
