<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Patients\Constants\CheckupStatus;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('checkups', function (Blueprint $table) {
      $table->id();
      $table->string('checkup_number')->unique()->nullable();
      $table->decimal('initial_price', 10, 2);
      $table->decimal('total_price',10, 2);
      $table->decimal('original_price', 10, 2);
      $table->decimal('coverage_amount', 10, 2)->default(0.00);
      $table->date('date');
      $table->time('time')->nullable();
      $table->enum('status', CheckupStatus::all())->default(CheckupStatus::default());
      $table->text('reason')->nullable();
      $table->decimal('weight', 8, 2)->nullable();
      $table->decimal('height', 8, 2)->nullable();
      $table->decimal('temperature', 8, 2)->nullable();
      $table->decimal('systolic_pressure', 8, 2)->nullable();
      $table->decimal('diastolic_pressure', 8, 2)->nullable();
      $table->decimal('SPO2', 8, 2)->nullable();
      $table->decimal('FC', 8, 2)->nullable();
      $table->text('symptoms')->nullable();
      $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
      $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('checkups');
  }
};
