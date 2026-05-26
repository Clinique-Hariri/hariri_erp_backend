<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Patients\Constants\HospitalizationStatus;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('hospitalizations', function (Blueprint $table) {
      $table->id();
      $table->string('hospitalization_number')->unique()->nullable();
      $table->dateTime('admission_date')->nullable();
      $table->dateTime('discharge_date')->nullable();
      $table->unsignedInteger('stay_length');
      $table->string('room_number')->nullable();
      $table->string('patient_attendant')->nullable();
      $table->decimal('initial_price', 10, 2);
      $table->decimal('extension_fees', 10, 2)->default(0);
      $table->decimal('total_price', 10, 2);
      $table->enum('status', HospitalizationStatus::all())->default(HospitalizationStatus::DRAFT);
      $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
      $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('hospitalizations');
  }
};
