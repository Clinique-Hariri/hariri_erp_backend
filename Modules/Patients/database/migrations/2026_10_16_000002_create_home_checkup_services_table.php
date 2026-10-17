<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('home_checkup_services', function (Blueprint $table) {
      $table->id();
      $table->string('service_name');
      $table->decimal('service_price', 10, 2)->default(0.00);
      $table->foreignId('home_checkup_id')->constrained('home_checkups')->cascadeOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('home_checkup_services');
  }
};
