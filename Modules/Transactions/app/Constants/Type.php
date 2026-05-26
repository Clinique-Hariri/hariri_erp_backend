<?php

namespace Modules\Transactions\Constants;

use Illuminate\Support\Collection;

class Type
{
  const CREDIT     = 'credit';
  const DEBIT      = 'debit';
  const REFUND     = 'refund';

  public static function all(bool $translated = false): array
  {
    return [
      self::CREDIT     => $translated ? __('transactions::app.credit')     : self::CREDIT,
      self::DEBIT      => $translated ? __('transactions::app.debit')      : self::DEBIT,
      self::REFUND     => $translated ? __('transactions::app.refund')     : self::REFUND,
    ];
  }

  public static function collection(): Collection
  {
    return collect(self::all());
  }

  public static function get(string $type): string
  {
    return self::collection()->get($type, $type);
  }

  public static function get_name(string $type): string
  {
    return self::all(true)[$type] ?? $type;
  }

  public static function default(): string
  {
    return self::CREDIT;
  }

  public static  function get_resource(string $type): array
  {
    return [
      'value' => $type,
      'label' => self::get_name($type),
      'color' => 'primary',
    ];
  }
}
