<?php

namespace App\Notifications;

use App\Constants\NotificationMessages;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Messaging\Aps;
use Kreait\Firebase\Messaging\AndroidNotification;


class NewMessageNotification extends Notification implements ShouldQueue
{
  use Queueable;

  protected $key;
  protected $title;
  protected $body;
  protected array $data;
  protected array $channels;

  public function __construct($key, array $data = [], array $channels = ['database', 'fcm'], array $replace = [])
  {
    $this->key = $key;
    $this->title = [
      'en' => NotificationMessages::title($key, 'en'),
      'ar' => NotificationMessages::title($key, 'ar'),
      'fr' => NotificationMessages::title($key, 'fr'),
    ];
    $this->body = [
      'en' => NotificationMessages::body($key, 'en', $replace),
      'ar' => NotificationMessages::body($key, 'ar', $replace),
      'fr' => NotificationMessages::body($key, 'fr', $replace),
    ];
    $this->data = $data;
    $this->channels = $channels;
  }

  public function via($notifiable): array
  {
    $via = [];

    if (in_array('database', $this->channels)) {
      $via[] = 'database';
    }

    if (in_array('fcm', $this->channels) && $notifiable->device_token) {
      $via[] = 'fcm';
    }

    return $via;
  }

  public function toDatabase($notifiable): array
  {
    return [
      'key' => $this->key,
      'title' => $this->title,
      'body' => $this->body,
      'data' => $this->data,
    ];
  }

  public function toFcm($notifiable): CloudMessage
  {
    $locale = app()->getLocale();

    $notification = FirebaseNotification::create(
      NotificationMessages::title($this->key, $locale),
      NotificationMessages::body($this->key, $locale)
    );

    // Android config (يدوي)
    $androidConfig = AndroidConfig::fromArray([
      'priority' => 'high',
      'notification' => [
        'sound' => 'default',
        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        'channel_id' => 'clinique_hariri_channel',
      ],
    ]);

    // iOS (Apns) config (يدوي)
    $apnsConfig = ApnsConfig::fromArray([
      'headers' => [
        'apns-priority' => '10',
      ],
      'payload' => [
        'aps' => [
          'alert' => [
            'title' => NotificationMessages::title($this->key, $locale),
            'body' => NotificationMessages::body($this->key, $locale),
          ],
          'sound' => 'default',
        ],
      ],
    ]);

    return CloudMessage::withTarget('token', $notifiable->device_token)
      ->withNotification($notification)
      ->withData($this->data)
      ->withAndroidConfig($androidConfig)
      ->withApnsConfig($apnsConfig);
  }
}
