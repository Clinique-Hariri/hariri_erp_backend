<?php

namespace App\Jobs\Synch;

use App\Services\Synch\SynchronizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class SendOrderToPharmacyJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable;

  public array $order;
  protected string $idempotencyKey;

  // maximum number of attempts
  public $tries = 6;

  /**
   * @param array $order ['phone' => ..., 'image' => UploadedFile]
   */
  public function __construct(array $order)
  {
    $this->order = $order;
    $this->idempotencyKey = Str::uuid()->toString();
  }

  /**
   * Handle the job.
   * @throws Exception
   */
  public function handle(SynchronizationService $service): void
  {
    $filePath = storage_path('app/' . $this->order['image']);

    $order = [
      'phone' => $this->order['phone'] ?? null,
      'image' => $filePath,
    ];

    $success = $service->sendOrder($order, $this->idempotencyKey);
    Log::info('SendOrder result: ' . ($success ? 'success' : 'failed'));

    if (!$success) {
      throw new Exception('Order not synced');
    } else {
      // Optionally delete the temporary file after successful sending
      if (file_exists($filePath)) {
        unlink($filePath);
      }
    }
  }

  /**
   * Custom backoff (seconds)
   */
  public function backoff(): array
  {
    return [
      5,      //first try → after 30 seconds
//      60,      //second try → after 1 minute
//      300,     //third try → after 5 minutes
//      3600,    //fourth try → after an hour
//      3600,    // fifth try → after an hour
//      3600,    // sixth try → after an hour
    ];
  }

  /**
   * Optional: log failed job
   */
  public function failed(Exception $exception): void
  {
    Log::error('SendOrderJob failed: ' . $exception->getMessage(), [
      'order' => $this->order,
      'idempotencyKey' => $this->idempotencyKey,
    ]);
  }
}
