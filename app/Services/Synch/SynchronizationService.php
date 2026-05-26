<?php

namespace App\Services\Synch;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SynchronizationService
{
  /**
   * @throws ConnectionException
   */
  public function sendOrder(array $order, string $idempotencyKey): bool
  {
    $request = Http::withHeaders([
      'X-SYSTEM-KEY' => config('system.shared_api_key'),
      'X-IDEMPOTENCY-KEY' => $idempotencyKey,
    ]);

    if (isset($order['image']) && file_exists($order['image'])) {
      $request = $request->attach(
        'image',
        fopen($order['image'], 'r'),
        basename($order['image'])
      );
    }

    try {
      $response = $request->post(
        config('system.synchronization_url') . '/api/v1/synch/orders',
        ['phone' => $order['phone'] ?? null]
      );

      Log::info('SendOrder response', [
        'status' => $response->status(),
        'body' => $response->body(),
        'headers' => $response->headers(),
      ]);

      return $response->successful();
    } catch (\Exception $e) {
      Log::error('SendOrder exception: ' . $e->getMessage(), [
        'order' => $order,
        'idempotencyKey' => $idempotencyKey,
      ]);
      return false;
    }
  }

}
