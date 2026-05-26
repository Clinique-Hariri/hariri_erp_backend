<?php

namespace App\Constants;

use Illuminate\Support\Collection;

class VisitType
{
  const APPOINTMENT = 'api.appointments.index';
  const ORDER = 'api.orders.index';

  public static function all(): array
  {
    return [
      self::APPOINTMENT => self::APPOINTMENT,
      self::ORDER => self::ORDER,
    ];
  }


  public static function collection(): Collection
  {
    return collect(array_combine(self::all(), self::all()));
  }

  public static function get(string $value): string
  {
    return self::collection()->get($value);
  }

}
