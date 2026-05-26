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
    Schema::create('inventory_items', function (Blueprint $table) {
      $table->id();
      $table->string('name', 100);
      $table->string('barcode', 50)->unique();
      $table->integer('current_stock')->default(0);
      $table->foreignId('category_id')->constrained('items_categories')->restrictOnDelete();
      $table->timestamps();

      $table->index(['category_id']);
      $table->index('current_stock');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('inventory_items');
  }
};
