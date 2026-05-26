<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ApiGuestMiddleware
{
  public function handle(Request $request, Closure $next)
  {
    $token = PersonalAccessToken::findToken($request->bearerToken());
    if ($token && Auth::id() == null) {
      Auth::login($token->tokenable);
    }
    return $next($request);
  }
}
