<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('doctors', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
      $table->foreignId('speciality_id')->nullable()->constrained('specialities')->restrictOnDelete();
      $table->decimal('checkup_price', 10, 2)->nullable();
      $table->decimal('commission_percentage', 10, 2)->default(0.00);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('doctors');
  }
};
