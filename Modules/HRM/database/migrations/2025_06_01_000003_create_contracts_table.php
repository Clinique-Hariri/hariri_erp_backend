<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('contracts', function (Blueprint $table) {
      $table->id();
      $table->date('start_date');
      $table->date('end_date')->nullable();
      $table->decimal('basic_salary', 10, 2)->nullable();
      $table->foreignId('employee_id')->constrained()->restrictOnDelete();
      $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
      $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('contracts');
  }
};
