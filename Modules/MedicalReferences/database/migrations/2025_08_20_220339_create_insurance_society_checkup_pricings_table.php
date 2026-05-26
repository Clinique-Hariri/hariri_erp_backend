<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('insurance_society_checkup_pricings', function (Blueprint $table) {
      $table->id();
      $table->decimal('checkup_price');
      $table->foreignId('insurance_society_id')->constrained('insurance_societies')->cascadeOnDelete();
      $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
      $table->timestamps();

      $table->unique(['insurance_society_id', 'doctor_id'], 'unique_society_doctor');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('insurance_society_checkup_pricings');
  }
};
