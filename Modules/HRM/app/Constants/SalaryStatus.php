<?php

namespace Modules\HRM\Constants;

use App\Support\Enum\PermissionNames;
use Illuminate\Support\Collection;

class SalaryStatus
{
    const DRAFT = 'draft';
    const PROCESSED = 'processed';
    const PAID = 'paid';

    public static function all($translated = false):array
    {
        return [
          self::DRAFT => $translated ? __('hrm::app.draft') : self::DRAFT,
          self::PROCESSED => $translated ? __('hrm::app.processed') : self::PROCESSED,
          self::PAID => $translated ? __('hrm::app.paid') : self::PAID,
        ];
    }

    public static function colors(): array
    {
        return [
          self::DRAFT => 'warning',
          self::PROCESSED => 'info',
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
        return self::DRAFT;
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
            self::DRAFT => [PermissionNames::SALARIES_UPDATE_TO_PROCESSED => self::get_resource(self::PROCESSED)],
            self::PROCESSED => [PermissionNames::SALARIES_UPDATE_TO_PAID => self::get_resource(self::PAID)],
            self::PAID => [],
        ];

        return isset($map[$status])
            ? array_values(array_filter(
                $map[$status],
                fn($next, $permission) => $user->hasPermissionTo($permission),
                ARRAY_FILTER_USE_BOTH
            ))
            : [];
    }
}
