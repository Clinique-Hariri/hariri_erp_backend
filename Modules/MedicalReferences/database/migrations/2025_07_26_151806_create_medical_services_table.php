<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\MedicalReferences\Constants\MedicalServiceTypes;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('medical_services', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->enum('type', MedicalServiceTypes::all());
      $table->decimal('price', 10, 2);
      $table->foreignId('group_id')->constrained('medical_service_groups')->restrictOnDelete();
      $table->unsignedTinyInteger('result_type')->default(1)
        ->comment('1: Positive/Negative, 2: Integer, 3: Key-Value, 4: Attachment');
      $table->string('min_normal_value')->nullable();
      $table->string('max_normal_value')->nullable();
      $table->string('normal_values')->nullable();
      $table->string('unit')->nullable();

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('medical_services');
  }
};
