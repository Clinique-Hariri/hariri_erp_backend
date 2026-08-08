<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PatientWelcomeNotification extends Notification implements ShouldQueue
{
  use Queueable;

  public function __construct(
    protected string $patientName,
    protected string $patientNumber,
  ) {
  }

  public function via($notifiable): array
  {
    $via = [];

    if (filled($this->routeFor($notifiable, 'whatsapp'))) {
      $via[] = 'whatsapp';
    }

    if (filled($this->routeFor($notifiable, 'mail'))) {
      $via[] = 'mail';
    }

    if (filled($this->routeFor($notifiable, 'sms'))) {
      $via[] = 'sms';
    }

    return $via;
  }

  protected function routeFor($notifiable, string $channel): ?string
  {
    $method = 'routeNotificationFor' . ucfirst($channel);

    if (method_exists($notifiable, $method)) {
      return $notifiable->{$method}();
    }

    if (method_exists($notifiable, 'routeNotificationFor')) {
      $route = $notifiable->routeNotificationFor($channel, $this);

      return filled($route) ? (string) $route : null;
    }

    return null;
  }

  public function toWhatsApp($notifiable): array
  {
    return [
      'content_sid' => config('services.twilio.content_sids.patient_welcome'),
      'variables' => [
        'name' => $this->patientName,
        'patient_number' => $this->patientNumber,
      ],
    ];
  }

  public function toMail($notifiable): MailMessage
  {
    return (new MailMessage)
      ->subject('Bienvenue à la Clinique Hariri')
      ->view('emails.patient-welcome', [
        'patientName' => $this->patientName,
        'patientNumber' => $this->patientNumber,
      ]);
  }

  public function toSms($notifiable): string
  {
    return sprintf(
      'Bienvenue a la Clinique Hariri ! Votre dossier patient a ete cree. Numero: %s',
      $this->patientNumber
    );
  }
}
