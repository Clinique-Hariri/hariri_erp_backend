<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('chronic_disease_patient', function (Blueprint $table) {
      $table->id();
      $table->longText('notes')->nullable();
      $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
      $table->foreignId('chronic_disease_id')->nullable()->constrained('chronic_diseases')->restrictOnDelete();
      $table->timestamps();

      $table->unique(['patient_id', 'chronic_disease_id'], 'unique_patient_chronic_disease');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('chronic_disease_patient');
  }
};
