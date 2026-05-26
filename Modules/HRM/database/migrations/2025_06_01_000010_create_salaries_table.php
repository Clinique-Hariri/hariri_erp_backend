<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\HRM\Constants\SalaryStatus;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('salaries', function (Blueprint $table) {
      $table->id();
      $table->date('month');
      $table->decimal('basic_salary', 10, 2);
      $table->decimal('daily_wage', 10, 2);
      $table->decimal('total_bonuses', 10, 2)->default(0);
      $table->decimal('total_deduction', 10, 2)->default(0);
      $table->integer('work_days');
      $table->integer('absent_days')->default(0);
      $table->decimal('net_salary', 10, 2);
      $table->enum('status', SalaryStatus::all())->default(SalaryStatus::default());
      $table->date('pay_date')->nullable();
      $table->foreignId('employee_id')->constrained()->restrictOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('salaries');
  }
};
