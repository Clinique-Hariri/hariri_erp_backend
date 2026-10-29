<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('doctors', function (Blueprint $table) {
      $table->boolean('home_checkup_eligible')->default(false)->after('commission_percentages');
    });
  }

  public function down(): void
  {
    Schema::table('doctors', function (Blueprint $table) {
      $table->dropColumn('home_checkup_eligible');
    });
  }
};
