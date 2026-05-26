<?php

namespace Modules\HRM\Constants;

use Illuminate\Support\Collection;

class CareerChangeType
{
    const RAISE = 'raise';
    const PROMOTION = 'promotion';
    const TRANSFER = 'transfer';
    const RENEWAL = 'renewal';
    const TERMINATION = 'termination';
    
    public static function all($translated = false):array
    {
        return [
          self::RAISE => $translated ? __('hrm::app.raise') : self::RAISE,
          self::PROMOTION => $translated ? __('hrm::app.promotion') : self::PROMOTION,
          self::TRANSFER => $translated ? __('hrm::app.transfer') : self::TRANSFER,
          self::RENEWAL => $translated ? __('hrm::app.renewal') : self::RENEWAL,
          self::TERMINATION => $translated ? __('hrm::app.termination') : self::TERMINATION,
        ];
    }

    public static function colors(): array
    {
        return [
          self::RAISE => 'success',
          self::PROMOTION => 'primary',
          self::TRANSFER => 'info',
          self::RENEWAL => 'warning',
          self::TERMINATION => 'danger',
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
        return self::RAISE;
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