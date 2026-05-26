<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordUserVisits
{
  public function handle(Request $request, Closure $next): Response
  {
    if(auth()->check()){
      $user = auth()->user();
      $user->visits()->create([
        'type' => $request->route()->getName(),
      ]);
    }

    return $next($request);
  }
}
