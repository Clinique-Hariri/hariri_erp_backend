<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('doctors', function (Blueprint $table) {
      $table->json('commission_percentages')->nullable()->after('checkup_price');
    });

    DB::table('doctors')
      ->select(['id', 'commission_percentage'])
      ->orderBy('id')
      ->chunkById(100, function ($doctors) {
        foreach ($doctors as $doctor) {
          DB::table('doctors')
            ->where('id', $doctor->id)
            ->update([
              'commission_percentages' => json_encode([
                'checkup' => (float) ($doctor->commission_percentage ?? 0),
                'analysis' => 0,
                'hospitalization' => 0,
                'operation' => 0,
              ]),
            ]);
        }
      });

    /* Schema::table('doctors', function (Blueprint $table) {
      $table->dropColumn('commission_percentage');
    }); */
  }

  public function down(): void
  {
    Schema::table('doctors', function (Blueprint $table) {
      $table->decimal('commission_percentage', 10, 2)->default(0.00)->after('checkup_price');
    });

    DB::table('doctors')
      ->select(['id', 'commission_percentages'])
      ->orderBy('id')
      ->chunkById(100, function ($doctors) {
        foreach ($doctors as $doctor) {
          $commissionPercentages = json_decode($doctor->commission_percentages ?? '{}', true);

          DB::table('doctors')
            ->where('id', $doctor->id)
            ->update([
              'commission_percentage' => (float) ($commissionPercentages['checkup'] ?? 0),
            ]);
        }
      });

    Schema::table('doctors', function (Blueprint $table) {
      $table->dropColumn('commission_percentages');
    });
  }
};
