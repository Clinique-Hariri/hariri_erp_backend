<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class AirtelSmsChannel
{
  public function send($notifiable, Notification $notification): void
  {
    if (!method_exists($notification, 'toSms')) {
      return;
    }

    $to = $notifiable->routeNotificationFor('sms', $notification);
    if (blank($to)) {
      return;
    }

    $url = config('services.airtel_sms.url', 'https://www.airtel.td/gateway/v1/sendDefaultSms');
    $username = config('services.airtel_sms.username');
    $password = config('services.airtel_sms.password');
    $customerId = config('services.airtel_sms.customer_id');
    $senderId = config('services.airtel_sms.sender_id');

    if (blank($url) || blank($customerId) || blank($senderId) || blank($username) || blank($password)) {
      return;
    }

    $message = $notification->toSms($notifiable);
    if (blank($message)) {
      return;
    }

    $destination = ltrim((string) $to, '+');

    $payload = [
      'customerId' => $customerId,
      'senderId' => $senderId,
      'destinationAddress' => [$destination],
      'message' => (string) $message,
      'metaData' => [
        'subAccountId' => $username,
      ],
    ];

    $request = Http::withHeaders([
      'Content-Type' => 'application/json',
    ]);

    $request = $request->withBasicAuth((string) $username, (string) $password);

    $response = $request
      ->withHeaders([
        'Content-Type' => 'application/json',
      ])
      ->send('POST', $url, [
        'json' => $payload,
      ]);

    $response->throw();
  }
}
