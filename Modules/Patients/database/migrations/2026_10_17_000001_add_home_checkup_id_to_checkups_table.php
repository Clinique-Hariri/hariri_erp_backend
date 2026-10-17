<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('checkups', function (Blueprint $table) {
      $table->foreignId('home_checkup_id')->nullable()->after('checkup_number')->constrained('home_checkups')->nullOnDelete();
    });
  }

  public function down(): void
  {
    Schema::table('checkups', function (Blueprint $table) {
      $table->dropConstrainedForeignId('home_checkup_id');
    });
  }
};
