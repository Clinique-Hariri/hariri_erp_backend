<?php

namespace Modules\Patients\Constants;

use Illuminate\Support\Collection;

class HomeCheckupVisitType
{
  public const string NURSE_ONLY = 'nurse_only';
  public const string WITH_DOCTOR = 'with_doctor';

  public static function all($translated = false): array
  {
    return [
      self::NURSE_ONLY => $translated ? __('patients::app.home_checkup_visit_type_nurse_only') : self::NURSE_ONLY,
      self::WITH_DOCTOR => $translated ? __('patients::app.home_checkup_visit_type_with_doctor') : self::WITH_DOCTOR,
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

  public static function get_name(string $type): string
  {
    return self::all(true)[$type];
  }

  public static function get_resource(string $type): array
  {
    return [
      'value' => $type,
      'name' => self::get_name($type),
    ];
  }
}
