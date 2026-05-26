<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Patients\Constants\OperationStatus;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('operations', function (Blueprint $table) {
      $table->id();
      $table->string('operation_number')->unique()->nullable();
      $table->timestamp('operation_date');
      $table->decimal('price', 10, 2);
      $table->text('description')->nullable();
      $table->enum('status', OperationStatus::all())->default(OperationStatus::DRAFT);
      $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('operations');
  }
};
