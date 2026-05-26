<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\MedicalReferences\Constants\MedicalServiceTypes;
use Modules\Patients\Constants\CheckupAnalysisStatus;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('checkup_analyses', function (Blueprint $table) {
      $table->id();
      $table->string('checkup_analysis_number')->unique()->nullable();
      $table->enum('type', MedicalServiceTypes::all());
      $table->decimal('coverage_amount')->default(0.00);
      $table->decimal('total_price', 10, 2);
      $table->decimal('original_price', 10, 2);
      $table->text('notes')->nullable();
      $table->text('orientation')->nullable();
      $table->text('doctor_interpretation')->nullable();
      $table->enum('status', CheckupAnalysisStatus::all())->default(CheckupAnalysisStatus::DRAFT);
      $table->foreignId('checkup_id')->constrained('checkups')->cascadeOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('checkup_analyses');
  }
};
