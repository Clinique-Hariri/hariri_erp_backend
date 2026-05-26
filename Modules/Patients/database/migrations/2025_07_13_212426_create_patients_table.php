<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Patients\Constants\PatientStatus;
use App\Constants\Gender;
use App\Constants\BloodType;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('patients', function (Blueprint $table) {
      $table->id();
      $table->string('patient_number')->unique()->nullable();
      $table->string('fullname');
      $table->enum('gender', Gender::all());
      $table->enum('blood_type', BloodType::all());
      $table->date('birthdate')->nullable();
      $table->integer('age')->nullable();
      $table->string('birth_place')->nullable();
      $table->string('full_address')->nullable();
      $table->string('phone')->nullable();
      $table->string('avatar')->nullable();
      $table->string('insurance_number')->unique()->nullable();
      $table->string('passport_number')->nullable();
      $table->enum('status', PatientStatus::all())->default(PatientStatus::ACTIVE);
      $table->unsignedBigInteger('external_patient_id')->nullable();
      $table->foreignId('insurance_society_branch_id')->nullable()->constrained('insurance_society_branches')->nullOnDelete();
      $table->timestamps();
    });
  }
  public function down(): void
  {
    Schema::dropIfExists('patients');
  }
};
