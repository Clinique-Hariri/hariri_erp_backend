<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;


return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    // Global Middleware (auto-loaded in Laravel 11)
    // If you want to explicitly declare more, use:
    // $middleware->append(SomeGlobalMiddleware::class);

    // Middleware Groups
    $middleware->web([

      \App\Http\Middleware\EncryptCookies::class,
      \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
      \Illuminate\Session\Middleware\StartSession::class,
      \Illuminate\View\Middleware\ShareErrorsFromSession::class,
      \App\Http\Middleware\VerifyCsrfToken::class,
      \Illuminate\Routing\Middleware\SubstituteBindings::class,
      \App\Http\Middleware\LocaleMiddleware::class,
    ]);

    $middleware->api([
      \Illuminate\Routing\Middleware\SubstituteBindings::class,
      \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
      \App\Http\Middleware\SetLocaleFromHeader::class,
    ]);

    $middleware->alias([
      'api.guest' => \App\Http\Middleware\ApiGuestMiddleware::class,
      'record.visits' => \App\Http\Middleware\RecordUserVisits::class,
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    //
  })
  ->withSchedule(function (Schedule $schedule) {
    $schedule->command('queue:work --stop-when-empty')
      ->everyMinute()
      ->withoutOverlapping()
      ->runInBackground();
  })
  ->create();
