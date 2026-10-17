<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Patients\Constants\HomeCheckupStatus;
use Modules\Patients\Constants\HomeCheckupVisitType;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('home_checkups', function (Blueprint $table) {
      $table->id();
      $table->string('home_checkup_number')->unique()->nullable();
      $table->string('mobile_request_id')->unique();

      // Patient (snapshot until resolved)
      $table->string('patient_fullname');
      $table->string('patient_phone')->nullable();
      $table->date('patient_birthdate')->nullable();
      $table->string('requested_patient_number')->nullable();
      $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();

      // Visit type
      $table->enum('requested_visit_type', HomeCheckupVisitType::all());
      $table->enum('visit_type', HomeCheckupVisitType::all());

      // Staff / spawned checkup
      $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
      $table->foreignId('checkup_id')->nullable()->constrained('checkups')->nullOnDelete();

      // Location
      $table->text('address')->nullable();
      $table->decimal('latitude', 10, 8)->nullable();
      $table->decimal('longitude', 11, 8)->nullable();

      // Scheduling
      $table->date('preferred_date')->nullable();
      $table->time('preferred_time')->nullable();
      $table->dateTime('scheduled_at')->nullable();

      // Lifecycle
      $table->enum('status', HomeCheckupStatus::all())->default(HomeCheckupStatus::default());

      // Pricing
      $table->decimal('base_price', 10, 2)->default(0.00);
      $table->decimal('services_total', 10, 2)->default(0.00);
      $table->decimal('total_price', 10, 2)->default(0.00);
      $table->decimal('coverage_amount', 10, 2)->default(0.00);

      $table->text('notes')->nullable();

      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('home_checkups');
  }
};
