<?php

namespace App\Constants;

use Illuminate\Support\Collection;

class Gender
{
    const MALE = 'male';
    const FEMALE = 'female';
    const NOT_SPECIFIED = 'not_specified';

    public static function all($translated = false):array
    {
        return [
          self::MALE => $translated ? __('user.genders.male') : self::MALE,
          self::FEMALE => $translated ? __('user.genders.female') : self::FEMALE,
          self::NOT_SPECIFIED => $translated ? __('user.genders.not_specified') : self::NOT_SPECIFIED
        ];
    }

    public static function colors()
    {
        return [
          self::MALE => 'success',
          self::FEMALE => 'danger',
          self::NOT_SPECIFIED => 'secondary',
        ];
    }

    public static function collection():Collection
    {
        return collect(array_combine(self::all(), self::all()));
    }

    public static function get(string $gender):string
    {
        return self::collection()->get($gender);
    }

  public static function get_name(string|null $value): string
  {
    $value = $value ?? self::NOT_SPECIFIED;
    return self::all(true)[$value];
  }

  public static function get_color(string|null $value): string
  {
    $value = $value ?? self::NOT_SPECIFIED;
    return self::colors()[$value];
  }

    public static function default():string
    {
        return self::NOT_SPECIFIED;
    }

    public static function get_resource(string $gender):array
    {
      return [
        'value' => $gender,
        'name' => self::get_name($gender),
        'color' => self::get_color($gender),
      ];
    }
}
