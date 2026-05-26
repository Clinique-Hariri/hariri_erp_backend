<?php

namespace Modules\Inventory\Constants\SupplyRequest;

use Illuminate\Support\Collection;

class Status
{
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const COMPLETED = 'completed';


    public static function all($translated = false):array
    {
        return [
            self::PENDING => $translated ? __('supply_requests::app.pending') : self::PENDING,
            self::APPROVED => $translated ? __('supply_requests::app.approved') : self::APPROVED,
            self::REJECTED => $translated ? __('supply_requests::app.rejected') : self::REJECTED,
            self::COMPLETED => $translated ? __('supply_requests::app.completed') : self::COMPLETED,
        ];
    }

    public static function colors(): array
    {
        return [
          self::PENDING => 'warning',
          self::APPROVED => 'info',
          self::REJECTED => 'danger',
          self::COMPLETED => 'success',
        ];
    }

    public static function collection():Collection
    {
        return collect(array_combine(self::all(), self::all()));
    }

    public static function get(string $gender):string
    {
        return self::collection()->get($gender);
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
        return self::PENDING;
    }

}
