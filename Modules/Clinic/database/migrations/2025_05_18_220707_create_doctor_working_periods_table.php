<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('doctor_working_periods', function (Blueprint $table) {
      $table->id();
      $table->time('start_time');
      $table->time('end_time');
      $table->foreignId('doctor_schedule_id')->constrained('doctor_schedules')->cascadeOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('doctor_working_periods');
  }
};
