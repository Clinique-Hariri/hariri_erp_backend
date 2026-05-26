<?php

namespace Modules\HRM\Constants;

use Illuminate\Support\Collection;

class ContractStatus
{
    const NONE = 'none';
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';

    public static function all($translated = false):array
    {
        return [
          self::NONE => $translated ? __('hrm::app.none') : self::NONE,
          self::ACTIVE => $translated ? __('hrm::app.active') : self::ACTIVE,
          self::INACTIVE => $translated ? __('hrm::app.inactive') : self::INACTIVE,
        ];
    }

    public static function colors(): array
    {
        return [
          self::NONE => 'danger',
          self::ACTIVE => 'success',
          self::INACTIVE => 'warning',
        ];
    }

    public static function collection():Collection
    {
        return collect(array_combine(self::all(), self::all()));
    }

    public static function get(string $status):string
    {
        return self::collection()->get($status);
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
        return self::NONE;
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
