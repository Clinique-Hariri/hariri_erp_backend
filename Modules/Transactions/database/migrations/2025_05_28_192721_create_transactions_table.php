<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\Type;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('transactions', function (Blueprint $table) {
      $table->id();
      $table->string('transaction_number')->unique();
      $table->decimal('amount', 10, 2);
      $table->string('details')->nullable();
      $table->enum('type', Type::all());
      $table->enum('status', Status::all())->default(Status::PENDING);
      $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
      $table->nullableMorphs('transactionable');
      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('transactions');
  }
};
