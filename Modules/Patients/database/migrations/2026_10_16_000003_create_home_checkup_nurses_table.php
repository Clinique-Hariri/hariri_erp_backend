<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('home_checkup_nurses', function (Blueprint $table) {
      $table->id();
      $table->foreignId('home_checkup_id')->constrained('home_checkups')->cascadeOnDelete();
      $table->foreignId('nurse_id')->constrained('employees')->cascadeOnDelete();
      $table->timestamps();

      $table->unique(['home_checkup_id', 'nurse_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('home_checkup_nurses');
  }
};
