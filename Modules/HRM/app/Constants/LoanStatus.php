<?php

namespace Modules\HRM\Constants;

use Illuminate\Support\Collection;

class LoanStatus
{
    const UNPAID = 'unpaid';
    const IN_PROGRESS = 'in_progress';
    const PAID = 'paid';

    public static function all($translated = false):array
    {
        return [
          self::UNPAID => $translated ? __('hrm::app.unpaid') : self::UNPAID,
          self::IN_PROGRESS => $translated ? __('hrm::app.in_progress') : self::IN_PROGRESS,
          self::PAID => $translated ? __('hrm::app.paid') : self::PAID,
        ];
    }

    public static function colors(): array
    {
        return [
          self::UNPAID => 'danger',
          self::IN_PROGRESS => 'warning',
          self::PAID => 'success',
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
        return self::UNPAID;
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
