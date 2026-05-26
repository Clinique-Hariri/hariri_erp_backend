<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
  public function handle(Request $request, Closure $next): Response
  {
    App::setLocale(
      in_array($locale = $request->getPreferredLanguage(['en', 'fr', 'ar']), ['en', 'fr', 'ar'])
        ? $locale
        : config('app.locale')
    );

    return $next($request);
  }
}
