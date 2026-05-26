<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnalysisResultNotification extends Notification implements ShouldQueue
{
  use Queueable;

  public function __construct(
    protected string $pdfUrl,
    protected string $date,
    protected string $reference,
    protected string $contact,
    protected array $channels = ['whatsapp', 'mail', 'sms']
  ) {
  }

  public function via($notifiable): array
  {
    $via = [];

    if (in_array('whatsapp', $this->channels, true) && filled($this->routeFor($notifiable, 'whatsapp'))) {
      $via[] = 'whatsapp';
    }

    if (in_array('mail', $this->channels, true) && filled($this->routeFor($notifiable, 'mail'))) {
      $via[] = 'mail';
    }

    if (in_array('sms', $this->channels, true) && filled($this->routeFor($notifiable, 'sms'))) {
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
      'variables' => [
        'pdf' => $this->pdfUrl,
        'date' => $this->date,
        'ref' => $this->reference,
        'contact' => $this->contact,
      ],
    ];
  }

  public function toMail($notifiable): MailMessage
  {
    return (new MailMessage)
      ->subject('Resultats d analyses disponibles')
      ->line('Bonjour cher(e) patient(e),')
      ->line('Bonne nouvelle ! Vos resultats d analyses sont maintenant disponibles.')
      ->line('A la Clinique Hariri Internationale, votre sante reste notre priorite.')
      ->line('Accedez a votre rapport en un clic :')
      ->action('Voir le rapport', $this->pdfUrl)
      ->line('Date des analyses : ' . $this->date)
      ->line('Reference : ' . $this->reference)
      ->line('Pour votre securite et un meilleur suivi, nous vous recommandons de consulter votre medecin avec ces resultats.')
      ->line('Plus d infos : ' . $this->contact)
      ->line('Merci pour votre confiance - Clinique Hariri Internationale.');
  }

  public function toSms($notifiable): string
  {
    return sprintf(
      'Clinique Hariri: Vos resultats d analyses sont disponibles. Ref: %s, Date: %s, Rapport: %s, Contact: %s',
      $this->reference,
      $this->date,
      $this->pdfUrl,
      $this->contact
    );
  }
}
