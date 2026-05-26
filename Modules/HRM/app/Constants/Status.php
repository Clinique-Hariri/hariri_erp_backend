<?php

namespace Modules\HRM\Constants;

use Illuminate\Support\Collection;

class Status
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    public static function all($translated = false):array
    {
        return [
          self::ACTIVE => $translated ? __('hrm::app.active') : self::ACTIVE,
          self::INACTIVE => $translated ? __('hrm::app.inactive') : self::INACTIVE,
        ];
    }

    public static function colors(): array
    {
        return [
          self::ACTIVE => 'success',
          self::INACTIVE => 'danger',
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

    public static function get_resource(string $status):array
    {
      return [
        'value' => $status,
        'name' => self::get_name($status),
        'color' => self::get_color($status),
      ];
    }

}

namespace Modules\Supplier\Constants;

use Illuminate\Support\Collection;

class Status
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    public static function all($translated = false):array
    {
        return [
          self::ACTIVE => $translated ? __('suppliers::app.active') : self::ACTIVE,
          self::INACTIVE => $translated ? __('suppliers::app.inactive') : self::INACTIVE,
        ];
    }

    public static function colors(): array
    {
        return [
          self::ACTIVE => 'success',
          self::INACTIVE => 'danger',
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
            self::ACTIVE => [self::get_resource(self::INACTIVE)],
            self::INACTIVE => [self::get_resource(self::ACTIVE)],
            default => [],
        };
    }
}
