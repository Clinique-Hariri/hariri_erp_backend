<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\Gender;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('employees', function (Blueprint $table) {
      $table->id();
      $table->string('employee_code')->unique();
      $table->string('fullname');
      $table->string('phone')->nullable();
      $table->string('email')->nullable();
      $table->enum('gender', Gender::all())->default(Gender::default());
      $table->string('address')->nullable();
      $table->date('birth_date')->nullable();
      $table->date('hire_date');
      $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('employees');
  }
};
