<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Transactions\Constants\TransactionCategory;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('transactions', function (Blueprint $table) {
      $table->enum('category', TransactionCategory::all())
        ->default(TransactionCategory::MISC)
        ->change();
    });
  }

  public function down(): void
  {
    Schema::table('transactions', function (Blueprint $table) {
      $table->enum('category', [
        TransactionCategory::CHECKUP,
        TransactionCategory::ANALYSIS,
        TransactionCategory::RADIOLOGY,
        TransactionCategory::OPERATION,
        TransactionCategory::HOSPITALIZATION,
        TransactionCategory::HOSPITALIZATION_EXTENSION,
        TransactionCategory::COMMISSION,
        TransactionCategory::SALARY,
        TransactionCategory::INSURANCE_COVERAGE,
        TransactionCategory::MISC,
      ])
        ->default(TransactionCategory::MISC)
        ->change();
    });
  }
};
