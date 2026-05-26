<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('insurance_society_branches', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->unsignedInteger('coverage_percentage');
      $table->foreignId('insurance_society_id')->constrained('insurance_societies')->restrictOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('insurance_society_branches');
  }
};
