<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\HRM\Constants\DeductionType;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('salary_deductions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('loan_installment_id')->nullable()->constrained()->onDelete('set null');
      $table->enum('type', DeductionType::all())->default(DeductionType::default());
      $table->decimal('amount', 10, 2);
      $table->foreignId('salary_id')->constrained()->cascadeOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('salary_deductions');
  }
};
