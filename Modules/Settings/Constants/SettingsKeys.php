<?php
namespace Modules\Settings\Constants;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\select;

class SettingsKeys
{
  const string HOSPITALIZATION_HOURLY_COST = 'hospitalization_hourly_cost';
  const string HOSPITALIZATION_DAILY_COST = 'hospitalization_daily_cost';

  public static function all($lang = null):array
  {
    return [
      self::HOSPITALIZATION_HOURLY_COST => $lang ? __('settings::app.'.self::HOSPITALIZATION_HOURLY_COST) : self::HOSPITALIZATION_HOURLY_COST,
      self::HOSPITALIZATION_DAILY_COST => $lang ? __('settings::app.'.self::HOSPITALIZATION_DAILY_COST) : self::HOSPITALIZATION_DAILY_COST
    ];
  }

  public static function get_name(string $key):string
  {
    return self::all(true)[$key];
  }

  public static function get_resource(string $key):array
  {
    return [
      'key' => $key,
      'name' => self::get_name($key),
    ];
  }
}
