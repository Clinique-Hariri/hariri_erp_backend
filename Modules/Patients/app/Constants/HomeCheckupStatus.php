<?php

namespace Modules\Patients\Constants;

use App\Support\Enum\PermissionNames;
use Illuminate\Support\Collection;

class HomeCheckupStatus
{
  public const string SCHEDULED = 'scheduled';
  public const string IN_PROGRESS = 'in_progress';
  public const string COMPLETED = 'completed';
  public const string CANCELLED = 'cancelled';

  public static function all($translated = false): array
  {
    return [
      self::SCHEDULED => $translated ? __('patients::app.home_checkup_status_scheduled') : self::SCHEDULED,
      self::IN_PROGRESS => $translated ? __('patients::app.home_checkup_status_in_progress') : self::IN_PROGRESS,
      self::COMPLETED => $translated ? __('patients::app.home_checkup_status_completed') : self::COMPLETED,
      self::CANCELLED => $translated ? __('patients::app.home_checkup_status_cancelled') : self::CANCELLED,
    ];
  }

  public static function colors(): array
  {
    return [
      self::SCHEDULED => 'blue',
      self::IN_PROGRESS => 'yellow',
      self::COMPLETED => 'green',
      self::CANCELLED => 'red',
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

  public static function get_resource(string $status): array
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
      self::SCHEDULED => [PermissionNames::HOME_CHECKUPS_UPDATE_TO_IN_PROGRESS => self::get_resource(self::IN_PROGRESS)],
      self::IN_PROGRESS => [PermissionNames::HOME_CHECKUPS_UPDATE_TO_COMPLETED => self::get_resource(self::COMPLETED)],
      self::COMPLETED => [],
      self::CANCELLED => [],
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
    return self::SCHEDULED;
  }
}
