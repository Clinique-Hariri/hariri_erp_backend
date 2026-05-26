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
    Schema::create('supply_request_items', function (Blueprint $table) {
      $table->id();
      $table->integer('requested_quantity');
      $table->foreignId('supply_request_id')->constrained('supply_requests')->cascadeOnDelete();
      $table->foreignId('item_id')->constrained('inventory_items')->restrictOnDelete();
      $table->timestamps();

      $table->index('supply_request_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('supply_request_items');
  }
};
