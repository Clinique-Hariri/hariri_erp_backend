<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\MedicalReferences\Constants\MedicalServiceTypes;
use Modules\Patients\Constants\CheckupAnalysisStatus;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('checkup_service', function (Blueprint $table) {
      $table->id();
      $table->string('checkup_service_number')->unique()->nullable();
      $table->string('service_name');
      $table->decimal('service_price', 10, 2);
      $table->decimal('coverage_amount', 10, 2)->default(0.00);
      $table->enum('service_type', MedicalServiceTypes::all());
      $table->text('notes')->nullable();
      $table->enum('status', CheckupAnalysisStatus::all())->default(CheckupAnalysisStatus::DRAFT);
      $table->foreignId('checkup_id')->constrained('checkups')->cascadeOnDelete();
      $table->foreignId('medical_service_id')->nullable()->constrained('medical_services')->nullOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('checkup_service');
  }
};
