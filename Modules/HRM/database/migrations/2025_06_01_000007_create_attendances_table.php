<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\HRM\Constants\AttendanceStatus;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('attendances', function (Blueprint $table) {
      $table->id();
      $table->date('date');
      $table->dateTime('check_in_time')->nullable();
      $table->dateTime('check_out_time')->nullable();
      $table->integer('duration')->nullable();
      $table->enum('status', AttendanceStatus::all())->nullable();
      $table->text('notes')->nullable();
      $table->foreignId('employee_id')->constrained()->restrictOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('attendances');
  }
};
