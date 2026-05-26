<?php

namespace Modules\HRM\Constants;

use Illuminate\Support\Collection;

class InstallmentStatus
{
    const PENDING = 'pending';
    const PAID = 'paid';
    const OVERDUE = 'overdue';
    
    public static function all($translated = false):array
    {
        return [
          self::PENDING => $translated ? __('hrm::app.pending') : self::PENDING,
          self::PAID => $translated ? __('hrm::app.paid') : self::PAID,
          self::OVERDUE => $translated ? __('hrm::app.overdue') : self::OVERDUE,
        ];
    }

    public static function colors(): array
    {
        return [
          self::PENDING => 'warning',
          self::PAID => 'success',
          self::OVERDUE => 'danger',
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
        return self::PENDING;
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