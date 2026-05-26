<?php

namespace App\Constants;

use Illuminate\Support\Collection;

class BloodType
{
  const A_POSITIVE = 'A+';
  const A_NEGATIVE = 'A-';
  const B_POSITIVE = 'B+';
  const B_NEGATIVE = 'B-';
  const AB_POSITIVE = 'AB+';
  const AB_NEGATIVE = 'AB-';
  const O_POSITIVE = 'O+';
  const O_NEGATIVE = 'O-';

  public static function all($translated = false): array
  {
    return [
      self::A_POSITIVE => $translated ? __('app.blood_types.a_positive') : self::A_POSITIVE,
      self::A_NEGATIVE => $translated ? __('app.blood_types.a_negative') : self::A_NEGATIVE,
      self::B_POSITIVE => $translated ? __('app.blood_types.b_positive') : self::B_POSITIVE,
      self::B_NEGATIVE => $translated ? __('app.blood_types.b_negative') : self::B_NEGATIVE,
      self::AB_POSITIVE => $translated ? __('app.blood_types.ab_positive') : self::AB_POSITIVE,
      self::AB_NEGATIVE => $translated ? __('app.blood_types.ab_negative') : self::AB_NEGATIVE,
      self::O_POSITIVE => $translated ? __('app.blood_types.o_positive') : self::O_POSITIVE,
      self::O_NEGATIVE => $translated ? __('app.blood_types.o_negative') : self::O_NEGATIVE,
    ];
  }

  public static function colors()
  {
    return [
      self::A_POSITIVE => 'primary',
      self::A_NEGATIVE => 'primary',
      self::B_POSITIVE => 'info',
      self::B_NEGATIVE => 'info',
      self::AB_POSITIVE => 'success',
      self::AB_NEGATIVE => 'success',
      self::O_POSITIVE => 'warning',
      self::O_NEGATIVE => 'warning',
    ];
  }

  public static function collection(): Collection
  {
    return collect(array_combine(self::all(), self::all()));
  }

  public static function get(string $value): string
  {
    return self::collection()->get($value);
  }

  public static function get_name(string $value): string
  {
    return self::all(true)[$value];
  }

  public static function get_color(string $value): string
  {
    return self::colors()[$value];
  }

  public static function get_resource(string $value):array
  {
    return [
      'value' => $value,
      'name' => self::get_name($value),
      'color' => self::get_color($value),
    ];
  }

  public static function default(): string
  {
    return self::A_NEGATIVE;
  }

}
