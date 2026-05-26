<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;

class FcmChannel
{
  protected $messaging;

  public function __construct(Messaging $messaging)
  {
    $this->messaging = $messaging;
  }

  /**
   * @throws MessagingException
   * @throws FirebaseException
   */
  public function send($notifiable, Notification $notification)
  {
    $message = $notification->toFcm($notifiable);
    if (!$message) return;
    return $this->messaging->send($message);
  }
}
