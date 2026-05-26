<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\HRM\Constants\InstallmentStatus;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('loan_installments', function (Blueprint $table) {
      $table->id();
      $table->integer('number');
      $table->date('month');
      $table->decimal('amount', 10, 2);
      $table->enum('status', InstallmentStatus::all())->default(InstallmentStatus::default());
      $table->text('notes')->nullable();
      $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('loan_installments');
  }
};
