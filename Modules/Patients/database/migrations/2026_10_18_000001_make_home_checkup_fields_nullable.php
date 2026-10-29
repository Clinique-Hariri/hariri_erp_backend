<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Patients\Constants\HomeCheckupVisitType;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('home_checkups', function (Blueprint $table) {
      $table->string('mobile_request_id')->nullable()->change();
      $table->enum('requested_visit_type', HomeCheckupVisitType::all())->nullable()->change();
    });
  }

  public function down(): void
  {
    Schema::table('home_checkups', function (Blueprint $table) {
      $table->string('mobile_request_id')->nullable(false)->change();
      $table->enum('requested_visit_type', HomeCheckupVisitType::all())->nullable(false)->change();
    });
  }
};
