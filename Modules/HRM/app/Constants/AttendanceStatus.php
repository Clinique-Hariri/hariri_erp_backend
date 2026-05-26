<?php

namespace Modules\HRM\Constants;

use Illuminate\Support\Collection;

class AttendanceStatus
{
    const PRESENT = 'present';
    const LATE = 'late';
    const ABSENT = 'absent';
    
    public static function all($translated = false):array
    {
        return [
          self::PRESENT => $translated ? __('hrm::app.present') : self::PRESENT,
          self::LATE => $translated ? __('hrm::app.late') : self::LATE,
          self::ABSENT => $translated ? __('hrm::app.absent') : self::ABSENT,
        ];
    }

    public static function colors(): array
    {
        return [
          self::PRESENT => 'success',
          self::LATE => 'warning',
          self::ABSENT => 'danger',
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
        return self::PRESENT;
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
        return match ($status) {
            self::PRESENT => [self::get_resource(self::LATE), self::get_resource(self::ABSENT)],
            self::LATE => [self::get_resource(self::PRESENT), self::get_resource(self::ABSENT)],
            self::ABSENT => [self::get_resource(self::PRESENT), self::get_resource(self::LATE)],
            default => [],
        };
    }
}