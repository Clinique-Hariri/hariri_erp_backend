<?php

namespace App\Constants;

use Illuminate\Support\Collection;

class Gender
{
    const MALE = 'male';
    const FEMALE = 'female';

    public static function all($translated = false):array
    {
        return [
          self::MALE => $translated ? __('user.genders.male') : self::MALE,
          self::FEMALE => $translated ? __('user.genders.female') : self::FEMALE
        ];
    }

    public static function colors()
    {
        return [
          self::MALE => 'success',
          self::FEMALE => 'danger',
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

    public static function get_name(string $gender):string
    {
        return self::all(true)[$gender];
    }

    public static function get_color(string $gender):string
    {
        return self::colors()[$gender];
    }

    public static function default():string
    {
        return self::MALE;
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
