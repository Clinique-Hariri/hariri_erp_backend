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
  const NOT_SPECIFIED = 'Not Specified';

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

  public static function get(string|null $value): string
  {
    return $value ? self::collection()->get($value) : self::NOT_SPECIFIED;
  }

  public static function get_name(string|null $value): string
  {
    return $value ? self::all(true)[$value] : __('app.blood_types.not_specified');
  }

  public static function get_color(string|null $value): string
  {
    return $value ? self::colors()[$value] : 'secondary';
  }

  public static function get_resource(string|null $value):array
  {
    return [
      'value' => $value,
      'name' => self::get_name($value),
      'color' => self::get_color($value),
    ];
  }

  public static function default(): string
  {
    return self::NOT_SPECIFIED;
  }

}
