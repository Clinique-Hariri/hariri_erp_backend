<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Constants\SettingsKeys;
use Modules\Settings\Models\Setting;

class SettingsDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    //create many settings in one to time
    foreach (SettingsKeys::all() as $key) {
      Setting::firstOrCreate(
        ['key' => $key],
        [
          'key' => $key,
          'value' => null,
          'created_at' => now(),
          'updated_at' => now()
        ]
      );
    }
  }
}
