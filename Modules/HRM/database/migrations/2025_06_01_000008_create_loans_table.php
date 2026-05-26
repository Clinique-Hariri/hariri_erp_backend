<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('loans', function (Blueprint $table) {
      $table->id();
      $table->decimal('amount', 10, 2);
      $table->decimal('installment_amount', 10, 2);
      $table->integer('total_installments');
      $table->date('deduction_date')->nullable();
      $table->foreignId('employee_id')->constrained()->restrictOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('loans');
  }
};
