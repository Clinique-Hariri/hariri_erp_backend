<?php

namespace Modules\Patients\Constants;

use App\Support\Enum\PermissionNames;
use Illuminate\Support\Collection;

class HospitalizationStatus
{
  public const string DRAFT = 'draft';
  public const string ACCEPTED = 'accepted';
  public const string ADMITTED = 'admitted';
  public const string DISCHARGED = 'discharged';


  public static function all($translated = false): array
  {
    return [
      self::DRAFT => $translated ? __('patients::app.draft') : self::DRAFT,
      self::ACCEPTED => $translated ? __('patients::app.accepted') : self::ACCEPTED,
      self::ADMITTED => $translated ? __('patients::app.admitted') : self::ADMITTED,
      self::DISCHARGED => $translated ? __('patients::app.discharged') : self::DISCHARGED,
    ];
  }

  public static function colors(): array
  {
    return [
      self::DRAFT => 'gray',
      self::ACCEPTED => 'blue',
      self::ADMITTED => 'green',
      self::DISCHARGED => 'red',
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

  public static function get_name(string $status): string
  {
    return self::all(true)[$status];
  }

  public static function get_color(string $status): string
  {
    return self::colors()[$status];
  }

  public static function get_resource(string $status):array
  {
    return [
      'value' => $status,
      'name' => self::get_name($status),
      'color' => self::get_color($status),
    ];
  }

  public static function get_next_statuses(string $status): array
  {
    $user = auth()->user();

    $map = [
      self::DRAFT => [PermissionNames::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED => self::get_resource(self::ACCEPTED)],
      self::ACCEPTED => [PermissionNames::HOSPITALIZATIONS_UPDATE_TO_ADMITTED => self::get_resource(self::ADMITTED)],
      self::ADMITTED => [PermissionNames::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED => self::get_resource(self::DISCHARGED)],
      self::DISCHARGED => [],
    ];

    return isset($map[$status])
      ? array_values(array_filter(
        $map[$status],
        fn($next, $permission) => $user->hasPermissionTo($permission),
        ARRAY_FILTER_USE_BOTH
      ))
      : [];
  }

  public static function default(): string
  {
    return self::DRAFT;
  }

}
