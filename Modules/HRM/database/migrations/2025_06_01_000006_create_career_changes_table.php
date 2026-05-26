<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\HRM\Constants\CareerChangeType;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('career_changes', function (Blueprint $table) {
      $table->id();
      $table->string('file')->nullable();
      $table->enum('type', CareerChangeType::all())->default(CareerChangeType::default());
      $table->text('notes')->nullable();
      $table->foreignId('employee_id')->constrained()->restrictOnDelete();
      $table->foreignId('old_contract_id')->nullable()->constrained('contracts')->nullOnDelete();
      $table->foreignId('new_contract_id')->nullable()->constrained('contracts')->nullOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('career_changes');
  }
};
