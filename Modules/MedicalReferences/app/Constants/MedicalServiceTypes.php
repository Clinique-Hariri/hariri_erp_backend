<?php

namespace Modules\MedicalReferences\Constants;

use Illuminate\Support\Collection;

class MedicalServiceTypes
{
  const ANALYSIS = 'analysis';
  const RADIOLOGY = 'radiology';

  public static function all($translated = false): array
  {
    return [
      self::ANALYSIS => $translated ? __('medicalreferences::app.analysis') : self::ANALYSIS,
      self::RADIOLOGY => $translated ? __('medicalreferences::app.radiology') : self::RADIOLOGY,
    ];
  }

  public static function colors(): array
  {
    return [
      self::ANALYSIS => 'primary',
      self::RADIOLOGY => 'secondary',
    ];
  }

  public static function collection(): Collection
  {
    return collect(array_combine(self::all(), self::all()));
  }

  public static function get(string $gender): string
  {
    return self::collection()->get($gender);
  }

  public static function get_name(string $status): string
  {
    return self::all(true)[$status];
  }

  public static function get_color(string $status): string
  {
    return self::colors()[$status];
  }

  public static function get_resource(string $status): array
  {
    return [
      'value' => $status,
      'name' => self::get_name($status),
      'color' => self::get_color($status),
    ];
  }

  public static function default(): string
  {
    return self::ANALYSIS;
  }

}
