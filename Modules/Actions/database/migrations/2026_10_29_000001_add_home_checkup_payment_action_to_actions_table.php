<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Actions\Constants\ActionType;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('actions', function (Blueprint $table) {
      $table->enum('action_type', ActionType::all())->change();
    });
  }

  public function down(): void
  {
    Schema::table('actions', function (Blueprint $table) {
      $table->enum('action_type', [
        ActionType::CHECKUP_PAYMENT_ACTION,
        ActionType::ANALYSIS_PAYMENT_ACTION,
        ActionType::ANALYSIS_RESULT_ACTION,
        ActionType::SALARY_PAYMENT_ACTION,
        ActionType::OPERATION_PAYMENT_ACTION,
        ActionType::HOSPITALIZATION_PAYMENT_ACTION,
        ActionType::HOSPITALIZATION_EXTENSION_ACTION,
      ])->change();
    });
  }
};
