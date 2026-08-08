<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('checkup_analyses', function (Blueprint $table) {
      $table->decimal('initial_price', 10, 2)->after('coverage_amount');
    });
  }

  public function down(): void
  {
    Schema::table('checkup_analyses', function (Blueprint $table) {
      $table->dropColumn('initial_price');
    });
  }
};
