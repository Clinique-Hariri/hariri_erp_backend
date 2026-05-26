<?php

namespace App\Constants;

class NotificationMessages
{
    const string EXAMINATION_PAYMENT = 'examination_payment';
    const string WALLET_CHARGING = 'wallet_charging';
    const string APPOINTMENT_CONFIRMATION = 'appointment_confirmation';
    const string APPOINTMENT_CANCELLATION = 'appointment_cancellation';
    const string APPOINTMENT_COMPLETION = 'appointment_completion';

    //custom notification messages
    const string TEST_NOTIFICATION = 'test_notification';
    const string NEW_UPDATE = 'new_update';

    // order messages
    const string ORDER_PENDING = 'order_pending';
    const string ORDER_ACCEPTED = 'order_accepted';
    const string ORDER_CANCELED = 'order_canceled';
    const string ORDER_ONGOING = 'order_ongoing';
    const string ORDER_DELIVERED = 'order_delivered';

    public static function titles(?string $locale = null): array
    {
        return [
            self::EXAMINATION_PAYMENT => __('messages.titles.examination_payment', [], $locale),
            self::WALLET_CHARGING => __('messages.titles.wallet_charging', [], $locale),
            self::APPOINTMENT_CONFIRMATION => __('messages.titles.appointment_confirmation', [], $locale),
            self::APPOINTMENT_CANCELLATION => __('messages.titles.appointment_cancellation', [], $locale),
            self::APPOINTMENT_COMPLETION => __('messages.titles.appointment_completion', [], $locale),

            // Custom notification messages
            self::TEST_NOTIFICATION => __('messages.titles.test_notification', [], $locale),
            self::NEW_UPDATE => __('messages.titles.new_update', [], $locale),

            // Order messages
            self::ORDER_PENDING => __('messages.titles.order_pending', [], $locale),
            self::ORDER_ACCEPTED => __('messages.titles.order_accepted', [], $locale),
            self::ORDER_CANCELED => __('messages.titles.order_canceled', [], $locale),
            self::ORDER_ONGOING => __('messages.titles.order_ongoing', [], $locale),
            self::ORDER_DELIVERED => __('messages.titles.order_delivered', [], $locale),
        ];
    }

    public static function bodies(?string $locale = null, array $replace = []): array
    {
        return [
            self::EXAMINATION_PAYMENT => __('messages.bodies.examination_payment', $replace, $locale),
            self::WALLET_CHARGING => __('messages.bodies.wallet_charging', $replace, $locale),
            self::APPOINTMENT_CONFIRMATION => __('messages.bodies.appointment_confirmation', $replace, $locale),
            self::APPOINTMENT_CANCELLATION => __('messages.bodies.appointment_cancellation', $replace, $locale),
            self::APPOINTMENT_COMPLETION => __('messages.bodies.appointment_completion', $replace, $locale),

            // Custom notification messages
            self::TEST_NOTIFICATION => __('messages.bodies.test_notification', $replace, $locale),
            self::NEW_UPDATE => __('messages.bodies.new_update', $replace, $locale),

            // Order messages
            self::ORDER_PENDING => __('messages.bodies.order_pending', $replace, $locale),
            self::ORDER_ACCEPTED => __('messages.bodies.order_accepted', $replace, $locale),
            self::ORDER_CANCELED => __('messages.bodies.order_canceled', $replace, $locale),
            self::ORDER_ONGOING => __('messages.bodies.order_ongoing', $replace, $locale),
            self::ORDER_DELIVERED => __('messages.bodies.order_delivered', $replace, $locale),
        ];
    }

    public static function customNotifications(): array
    {
        return [
            self::TEST_NOTIFICATION,
            self::NEW_UPDATE,
        ];
    }

    public static function title(string $key, ?string $locale = null): ?string
    {
        return self::titles($locale)[$key] ?? null;
    }

    public static function body(string $key, ?string $locale = null, array $replace = []): ?string
    {
        return self::bodies($locale, $replace)[$key] ?? null;
    }
}
