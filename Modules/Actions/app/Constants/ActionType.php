<?php

namespace Modules\Actions\Constants;

use Illuminate\Support\Collection;

class ActionType
{
    const string CHECKUP_PAYMENT_ACTION  = 'checkup_payment_action';
    const string ANALYSIS_PAYMENT_ACTION = 'analysis_payment_action';
    const string ANALYSIS_RESULT_ACTION  = 'analysis_result_action';
    const string SALARY_PAYMENT_ACTION = 'salary_payment_action';
    const string OPERATION_PAYMENT_ACTION = 'operation_payment_action';
    const string HOSPITALIZATION_PAYMENT_ACTION = 'hospitalization_payment_action';
    const string HOSPITALIZATION_EXTENSION_ACTION = 'hospitalization_extension_action';

    public static function all(bool $translated = false): array
    {
        return [
            self::CHECKUP_PAYMENT_ACTION   => $translated ? __('actions::app.checkup_payment_action')   : self::CHECKUP_PAYMENT_ACTION,
            self::ANALYSIS_PAYMENT_ACTION  => $translated ? __('actions::app.analysis_payment_action')  : self::ANALYSIS_PAYMENT_ACTION,
            self::ANALYSIS_RESULT_ACTION   => $translated ? __('actions::app.analysis_result_action')   : self::ANALYSIS_RESULT_ACTION,
            self::SALARY_PAYMENT_ACTION    => $translated ? __('actions::app.salary_payment_action')    : self::SALARY_PAYMENT_ACTION,
            self::OPERATION_PAYMENT_ACTION => $translated ? __('actions::app.operation_payment_action') : self::OPERATION_PAYMENT_ACTION,
            self::HOSPITALIZATION_PAYMENT_ACTION   => $translated ? __('actions::app.hospitalization_payment_action')   : self::HOSPITALIZATION_PAYMENT_ACTION,
            self::HOSPITALIZATION_EXTENSION_ACTION => $translated ? __('actions::app.hospitalization_extension_action') : self::HOSPITALIZATION_EXTENSION_ACTION,
        ];
    }

    public static function collection(): Collection
    {
        return collect(self::all());
    }

    public static function get_name(string $type): string
    {
        return self::all(true)[$type] ?? $type;
    }

    public static function get_resource(string $type): array
    {
        return [
            'value' => $type,
            'label' => self::get_name($type),
        ];
    }
}
