<?php

namespace App\Constants\Statuses;

use Illuminate\Support\Collection;

class UserStatus
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const BANNED = 'banned';

    public static function all($translated = false):array
    {
        return [
          self::ACTIVE => $translated? __(key: 'user.statuses.active') : self::ACTIVE,
          self::INACTIVE => $translated? __(key: 'user.statuses.inactive') : self::INACTIVE,
          self::BANNED => $translated? __(key: 'user.statuses.banned') : self::BANNED,
        ];
    }

    public static function colors(): array
    {
        return [
          self::ACTIVE => 'success',
          self::INACTIVE => 'danger',
          self::BANNED => 'warning',
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

    public static function get_name(string $status):string
    {
        return self::all(true)[$status];
    }

    public static function get_color(string $status):string
    {
        return self::colors()[$status];
    }

    public static function default():string
    {
        return self::ACTIVE;
    }

  public static function get_resource(string $value):array
  {
    return [
      'value' => $value,
      'name' => self::get_name($value),
      'color' => self::get_color($value),
    ];
  }

  public static function get_next_statuses(string $value): array
  {
    return match ($value) {
      self::ACTIVE => [self::get_resource(self::INACTIVE), self::get_resource(self::BANNED)],
      self::INACTIVE => [self::get_resource(self::ACTIVE)],
      self::BANNED => [self::get_resource(self::ACTIVE)],
      default => [],
    };
  }

}
