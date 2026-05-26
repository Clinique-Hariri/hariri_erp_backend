<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestSmsNotification extends Notification
{
  use Queueable;

  public function __construct(
    protected string $message
  ) {
  }

  public function via($notifiable): array
  {
    return filled($notifiable->routeNotificationFor('sms', $this)) ? ['sms'] : [];
  }

  public function toSms($notifiable): string
  {
    return $this->message;
  }
}
