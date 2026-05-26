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
    // Create the pivot table for many-to-many relationship
    Schema::create('insurance_society_managers', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
      $table->foreignId('insurance_society_id')->constrained('insurance_societies')->onDelete('cascade');
      $table->timestamps();

      // Composite unique constraint to prevent duplicate entries
      $table->unique(['user_id', 'insurance_society_id']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    // Drop the pivot table
    Schema::dropIfExists('insurance_society_managers');

    // Re-add the insurance_society_id column to users table
    Schema::table('users', function (Blueprint $table) {
      $table->foreignId('insurance_society_id')
        ->nullable()
        ->after('device_token')
        ->constrained('insurance_societies')
        ->onDelete('set null');
      
      $table->index('insurance_society_id');
    });
  }
};
