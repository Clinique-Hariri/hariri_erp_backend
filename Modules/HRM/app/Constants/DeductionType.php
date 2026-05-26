<?php

namespace Modules\HRM\Constants;

use Illuminate\Support\Collection;

class DeductionType
{
    const LOAN = 'loan';
    const ABSENCE = 'absence';

    public static function all($translated = false):array
    {
        return [
          self::LOAN => $translated ? __('hrm::app.loan') : self::LOAN,
          self::ABSENCE => $translated ? __('hrm::app.absence') : self::ABSENCE,
        ];
    }

    public static function colors(): array
    {
        return [
          self::LOAN => 'warning',
          self::ABSENCE => 'danger',
        ];
    }

    public static function collection():Collection
    {
        return collect(array_combine(self::all(), self::all()));
    }

    public static function get(string $type):string
    {
        return self::collection()->get($type);
    }

    public static function get_name(string $type):string
    {
        return self::all(true)[$type];
    }

    public static function get_color(string $type):string
    {
        return self::colors()[$type];
    }

    public static function default():string
    {
        return self::LOAN;
    }

    public static function get_resource(string $type):array
    {
      return [
        'value' => $type,
        'name' => self::get_name($type),
        'color' => self::get_color($type),
      ];
    }
}
