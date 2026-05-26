<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('insurance_society_service_pricings', function (Blueprint $table) {
            $table->id();
            $table->decimal('medical_service_price');
            $table->foreignId('insurance_society_id')->constrained('insurance_societies')->cascadeOnDelete();
            $table->foreignId('medical_service_id')->constrained('medical_services')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['insurance_society_id', 'medical_service_id'], 'unique_society_service');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_society_service_pricings');
    }
};
