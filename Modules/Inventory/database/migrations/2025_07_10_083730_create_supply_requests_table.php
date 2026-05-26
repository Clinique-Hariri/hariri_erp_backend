<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Inventory\Constants\SupplyRequest\Status;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('supply_requests', function (Blueprint $table) {
      $table->id();
      $table->string('request_number', 50)->unique();
      $table->enum('status', Status::all())->default(Status::PENDING);
      $table->string('requested_by', 100)->nullable();
      $table->string('approved_by', 100)->nullable();
      $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
      $table->timestamps();

      $table->index(['department_id', 'status']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('supply_requests');
  }
};
