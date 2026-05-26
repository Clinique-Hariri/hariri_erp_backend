<?php

namespace App\Http\Controllers\Synchronization;

use App\Http\Controllers\Controller;
use App\Jobs\Synch\SendOrderToPharmacyJob;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\NoReturn;

class OrderController extends Controller
{
  #[NoReturn]
  public function send(Request $request)
  {
    // Validate request
    $request->validate([
      'phone' => 'nullable|string|max:15',
      'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
    ]);

    // Prepare order array
    $path = $request->file('image')->store('orders_temp');
    $order = [
      'phone' => $request->phone,
      'image' => $path,
    ];

    // Dispatch the job
    SendOrderToPharmacyJob::dispatch($order);

    return response()->json([
      'message' => 'Order synchronization started.'
    ]);
  }
}
