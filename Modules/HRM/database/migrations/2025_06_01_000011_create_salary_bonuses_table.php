<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('salary_bonuses', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->decimal('amount', 10, 2);
      $table->foreignId('salary_id')->constrained()->cascadeOnDelete();
      $table->foreignId('bonus_id')->nullable()->constrained()->nullOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('salary_bonuses');
  }
};
