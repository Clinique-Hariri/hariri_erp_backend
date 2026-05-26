<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('checkup_tickets', function (Blueprint $table) {
      $table->id();
      $table->unsignedInteger('ticket_number');
      $table->date('date');
      //2 statuses one for pending patients and one for served patients
      $table->enum('status', ['pending', 'served'])->default('pending');
      $table->foreignId('checkup_id')->constrained('checkups')->cascadeOnDelete();
      $table->timestamps();
    });
  }


  public function down(): void
  {
    Schema::dropIfExists('checkup_tickets');
  }
};
