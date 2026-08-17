<?php

namespace Modules\Transactions\Constants;

use Illuminate\Support\Collection;

class TransactionCategory
{
  const CHECKUP                  = 'checkup';
  const ANALYSIS                 = 'analysis';
  const RADIOLOGY                = 'radiology';
  const OPERATION                = 'operation';
  const HOSPITALIZATION          = 'hospitalization';
  const HOSPITALIZATION_EXTENSION = 'hospitalization_extension';
  const COMMISSION               = 'commission';
  const SALARY                   = 'salary';
  const INSURANCE_COVERAGE       = 'insurance_coverage';
  const MISC                     = 'misc';

  public static function all(bool $translated = false): array
  {
    return [
      self::CHECKUP                  => $translated ? __('transactions::app.category_checkup')                   : self::CHECKUP,
      self::ANALYSIS                 => $translated ? __('transactions::app.category_analysis')                  : self::ANALYSIS,
      self::RADIOLOGY                => $translated ? __('transactions::app.category_radiology')                 : self::RADIOLOGY,
      self::OPERATION                => $translated ? __('transactions::app.category_operation')                 : self::OPERATION,
      self::HOSPITALIZATION          => $translated ? __('transactions::app.category_hospitalization')           : self::HOSPITALIZATION,
      self::HOSPITALIZATION_EXTENSION => $translated ? __('transactions::app.category_hospitalization_extension') : self::HOSPITALIZATION_EXTENSION,
      self::COMMISSION               => $translated ? __('transactions::app.category_commission')                : self::COMMISSION,
      self::SALARY                   => $translated ? __('transactions::app.category_salary')                    : self::SALARY,
      self::INSURANCE_COVERAGE       => $translated ? __('transactions::app.category_insurance_coverage')        : self::INSURANCE_COVERAGE,
      self::MISC                     => $translated ? __('transactions::app.category_misc')                      : self::MISC,
    ];
  }

  public static function collection(): Collection
  {
    return collect(self::all());
  }

  public static function get(string $category): string
  {
    return self::collection()->get($category, $category);
  }

  public static function get_name(string $category): string
  {
    return self::all(true)[$category] ?? $category;
  }

  public static function default(): string
  {
    return self::MISC;
  }

  public static function colors(): array
  {
    return [
      self::CHECKUP                  => 'primary',
      self::ANALYSIS                 => 'info',
      self::RADIOLOGY                => 'purple',
      self::OPERATION                => 'danger',
      self::HOSPITALIZATION          => 'warning',
      self::HOSPITALIZATION_EXTENSION => 'orange',
      self::COMMISSION               => 'success',
      self::SALARY                   => 'teal',
      self::INSURANCE_COVERAGE       => 'cyan',
      self::MISC                     => 'secondary',
    ];
  }

  public static function get_color(string $category): string
  {
    return self::colors()[$category] ?? 'secondary';
  }

  public static function external(): array
  {
    return [
      self::ANALYSIS,
      self::RADIOLOGY,
      self::MISC,
    ];
  }

  public static function get_resource(string $category): array
  {
    return [
      'value' => $category,
      'label' => self::get_name($category),
      'color' => self::get_color($category),
    ];
  }
}
