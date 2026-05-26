<?php

namespace Modules\Patients\Constants;

use App\Support\Enum\PermissionNames;
use Illuminate\Support\Collection;

class OperationStatus
{
  public const DRAFT = 'draft';
  public const PENDING = 'pending';
  public const SCHEDULED = 'scheduled';
  public const COMPLETED = 'completed';

  public static function all($translated = false): array
  {
    return [
      self::DRAFT => $translated ? __('patients::app.draft') : self::DRAFT,
      self::PENDING => $translated ? __('patients::app.pending') : self::PENDING,
      self::SCHEDULED => $translated ? __('patients::app.scheduled') : self::SCHEDULED,
      self::COMPLETED => $translated ? __('patients::app.completed') : self::COMPLETED,
    ];
  }

  public static function colors(): array
  {
    return [
      self::DRAFT => 'gray',
      self::PENDING => 'yellow',
      self::SCHEDULED => 'blue',
      self::COMPLETED => 'green',
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
      self::DRAFT => [PermissionNames::OPERATIONS_UPDATE_TO_PENDING => self::get_resource(self::PENDING)],
      self::PENDING => [
        PermissionNames::OPERATIONS_UPDATE_TO_SCHEDULED => self::get_resource(self::SCHEDULED),
        PermissionNames::OPERATIONS_UPDATE_TO_COMPLETED => self::get_resource(self::COMPLETED),
      ],
      self::SCHEDULED => [PermissionNames::OPERATIONS_UPDATE_TO_COMPLETED => self::get_resource(self::COMPLETED)],
      self::COMPLETED => [],
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

