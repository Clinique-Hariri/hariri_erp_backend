<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('checkup_analysis_services', function (Blueprint $table) {
      $table->id();
      $table->decimal('service_price', 10, 2);
      $table->json('result')->nullable();
      $table->foreignId('checkup_analysis_id')->constrained('checkup_analyses')->cascadeOnDelete();
      $table->foreignId('medical_service_id')->constrained('medical_services')->restrictOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('checkup_analysis_services');
  }
};
