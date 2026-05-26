<?php
namespace App\Support\Enum;
class UserTypes
{
  const ADMIN = 'admin';
  const CLINIC = 'clinic';
  const HUMAN_RESOURCE = 'human_resource';
  const INVENTORY = 'inventory';
  const INSURANCE_SOCIETY = 'insurance_society';

  public static function all($translated = false):array
  {
    return [
      self::ADMIN => $translated ? __('user.types.admin') : self::ADMIN,
      self::CLINIC => $translated ? __('user.types.clinic') : self::CLINIC,
      self::HUMAN_RESOURCE => $translated ? __('user.types.human_resource') : self::HUMAN_RESOURCE,
      self::INVENTORY => $translated ? __('user.types.inventory') : self::INVENTORY,
      self::INSURANCE_SOCIETY => $translated ? __('user.types.insurance_society') : self::INSURANCE_SOCIETY,
    ];
  }

  public static function colors():array
  {
    return [
      self::ADMIN => 'primary',
      self::CLINIC => 'success',
      self::HUMAN_RESOURCE => 'info',
      self::INVENTORY => 'warning',
      self::INSURANCE_SOCIETY => 'danger',
    ];
  }

  public static function get_name(string $type):string
  {
    return self::all(true)[$type];
  }

  public static function get_color(string $type):string
  {
    return self::colors()[$type];
  }

  public static function get_resource(string $value):array
  {
    return [
      'value' => $value,
      'name' => self::get_name($value),
      'color' => self::get_color($value),
    ];
  }
}
