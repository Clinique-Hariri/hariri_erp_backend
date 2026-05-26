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
    Schema::create('inventory_transaction_items', function (Blueprint $table) {
      $table->id();
      $table->integer('quantity');
      $table->decimal('unit_price', 10, 2);
      $table->decimal('total_price', 10, 2);
      $table->foreignId('transaction_id')->constrained('inventory_transactions')->cascadeOnDelete();
      $table->foreignId('item_id')->constrained('inventory_items')->restrictOnDelete();
      $table->timestamps();

      $table->index('transaction_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('inventory_transaction_items');
  }
};
