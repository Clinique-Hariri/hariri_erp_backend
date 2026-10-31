<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('home_checkups', function (Blueprint $table) {
      if (Schema::hasColumn('home_checkups', 'requested_patient_number')) {
        $table->renameColumn('requested_patient_number', 'patient_number');
      }

      $table->string('patient_fullname')->nullable()->change();

      $patientForeignKey = collect(Schema::getForeignKeys('home_checkups'))
        ->first(fn (array $foreignKey) => in_array('patient_id', $foreignKey['columns'] ?? [], true));

      if ($patientForeignKey) {
        $table->dropForeign($patientForeignKey['name']);
      }

      $table->unsignedBigInteger('patient_id')->nullable(false)->change();
      $table->foreign('patient_id')->references('id')->on('patients')->cascadeOnDelete();
    });
  }

  public function down(): void
  {
    Schema::table('home_checkups', function (Blueprint $table) {
      $table->renameColumn('patient_number', 'requested_patient_number');
      $table->string('patient_fullname')->nullable(false)->change();

      $patientForeignKey = collect(Schema::getForeignKeys('home_checkups'))
        ->first(fn (array $foreignKey) => in_array('patient_id', $foreignKey['columns'] ?? [], true));

      if ($patientForeignKey) {
        $table->dropForeign($patientForeignKey['name']);
      }

      $table->unsignedBigInteger('patient_id')->nullable()->change();
      $table->foreign('patient_id')->references('id')->on('patients')->nullOnDelete();
    });
  }
};
