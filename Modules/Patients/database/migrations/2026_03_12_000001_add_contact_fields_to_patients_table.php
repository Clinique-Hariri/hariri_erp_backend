<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('patients', function (Blueprint $table) {
      $table->string('insured_name')->nullable()->after('fullname');
      $table->string('whatsapp_number')->unique()->nullable()->after('phone');
      $table->string('email')->unique()->nullable()->after('whatsapp_number');
    });
  }

  public function down(): void
  {
    Schema::table('patients', function (Blueprint $table) {
      $table->dropUnique(['whatsapp_number']);
      $table->dropUnique(['email']);
      $table->dropColumn(['insured_name', 'whatsapp_number', 'email']);
    });
  }
};
