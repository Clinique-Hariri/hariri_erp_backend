<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class TwilioWhatsAppChannel
{
  public function send($notifiable, Notification $notification): void
  {
    if (!method_exists($notification, 'toWhatsApp')) {
      return;
    }

    $to = $notifiable->routeNotificationFor('whatsapp', $notification);
    if (blank($to)) {
      return;
    }

    $sid = config('services.twilio.sid');
    $authToken = config('services.twilio.auth_token');
    $serviceSid = config('services.twilio.service_sid');
    $from = config('services.twilio.whatsapp_from');
    $contentSid = config('services.twilio.content_sid');

    if (blank($sid) || blank($authToken) || blank($contentSid) || (blank($serviceSid) && blank($from))) {
      return;
    }

    $messageData = $notification->toWhatsApp($notifiable);

    $to = str_starts_with($to, 'whatsapp:') ? $to : 'whatsapp:' . $to;

    $payload = [
      'contentSid' => $contentSid,
      'contentVariables' => json_encode($messageData['variables'] ?? [], JSON_UNESCAPED_UNICODE),
    ];

    if (filled($serviceSid)) {
      $payload['messagingServiceSid'] = $serviceSid;
    } else {
      $payload['from'] = $from;
    }

    $client = new Client($sid, $authToken);
    $client->messages->create($to, $payload);
  }
}
