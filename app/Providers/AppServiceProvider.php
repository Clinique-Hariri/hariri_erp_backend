<?php

namespace App\Providers;

use App\Notifications\Channels\AirtelSmsChannel;
use App\Notifications\Channels\FcmChannel;
use App\Notifications\Channels\TwilioWhatsAppChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
      $this->app->singleton(Messaging::class, function () {
          return (new Factory)
              ->withServiceAccount(config('services.firebase.credentials'))
              ->createMessaging();
      });
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
      Notification::extend('fcm', function ($app) {
          return new FcmChannel($app->make(\Kreait\Firebase\Contract\Messaging::class));
      });

      Notification::extend('whatsapp', function () {
          return new TwilioWhatsAppChannel();
      });

      Notification::extend('sms', function () {
          return new AirtelSmsChannel();
      });
  }
}
