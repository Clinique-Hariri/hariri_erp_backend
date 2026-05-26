<?php

namespace Modules\Patients\Constants;

use App\Support\Enum\PermissionNames;
use Illuminate\Support\Collection;

class PatientStatus
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const ARCHIVED = 'archived';

    public static function all($translated = false):array
    {
        return [
          self::ACTIVE => $translated? __(key: 'user.statuses.active') : self::ACTIVE,
          self::INACTIVE => $translated? __(key: 'user.statuses.inactive') : self::INACTIVE,
          self::ARCHIVED => $translated? __(key: 'user.statuses.archived') : self::ARCHIVED,
        ];
    }

    public static function all2():array
    {
        return [
          self::ACTIVE => __('app.active'),
          self::INACTIVE => __('app.inactive'),
          self::ARCHIVED => __('app.archived'),
        ];
    }

    public static function colors(): array
    {
        return [
          self::ACTIVE => 'success',
          self::INACTIVE => 'danger',
          self::ARCHIVED => 'warning',
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

  public static function get_resource(string $status):array
  {
    return [
      'value' => $status,
      'name' => self::get_name($status),
      'color' => self::get_color($status),
    ];
  }

  public static function get_next_statuses(string $status): array
  {
    return match ($status) {
      self::ACTIVE => [self::get_resource(self::INACTIVE), self::get_resource(self::ARCHIVED)],
      self::INACTIVE => [self::get_resource(self::ACTIVE), self::get_resource(self::ARCHIVED)],
      self::ARCHIVED => [self::get_resource(self::ACTIVE)],
      default => [],
    };
  }

    public static function default():string
    {
        return self::ACTIVE;
    }

}
