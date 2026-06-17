<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\NotificationResource;
use App\Notifications\AnalysisResultNotification;
use Exception;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Notification;


class NotificationsController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    try {
      $user = auth()->user();
      $notifications = $user->notifications()->orderBy('created_at', 'desc');
      if (request()->boolean('paginate')) {
        $notifications = $notifications->paginate($request->get('per_page', 10));
      } else {
        $notifications = $notifications->get();
      }
      return $this->successResponse(
        data: NotificationResource::collection($notifications),
      );
    } catch (Exception $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function unread(Request $request)
  {
    try {
      $user = auth()->user();
      $unreadNotifications = $user->unreadNotifications()->orderBy('created_at', 'desc');

      if (request()->boolean('paginate')) {
        $unreadNotifications = $unreadNotifications->paginate($request->get('per_page', 10));
      } else {
        $unreadNotifications = $unreadNotifications->get();
      }

      return $this->successResponse(
        data: NotificationResource::collection($unreadNotifications),
      );
    } catch (Exception $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function markAsRead($id, Request $request)
  {
    try {
      $user = auth()->user();
      $notification = $user->notifications()->findOrFail($id);
      $notification->markAsRead();
      return $this->successResponse(
        message: 'Notification marked as read successfully.',
        data: new NotificationResource($notification)
      );
    } catch (Exception $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function testSend(Request $request)
  {
    $data = $this->validateRequest($request, [
      'channels' => ['required', 'array', 'min:1'],
      'channels.*' => ['required', 'string', 'in:mail,whatsapp,sms'],
      'to' => ['required', 'array'],
      'to.mail' => ['nullable', 'email'],
      'to.whatsapp' => ['nullable', 'string', 'max:30'],
      'to.sms' => ['nullable', 'string', 'max:30'],
      'pdf_url' => ['nullable', 'url'],
      'date' => ['nullable', 'date_format:Y-m-d'],
      'reference' => ['nullable', 'string', 'max:100'],
      'contact' => ['nullable', 'string', 'max:100'],
    ]);

    $channels = collect($data['channels'])->unique()->values()->all();

    $routeMap = [
      'mail' => 'mail',
      'whatsapp' => 'whatsapp',
      'sms' => 'sms',
    ];

    $routes = [];
    foreach ($channels as $channel) {
      $address = data_get($data, 'to.' . $channel);
      if (blank($address)) {
        return $this->errorResponse("Missing 'to.{$channel}' for selected channel '{$channel}'.", 422);
      }
      $routes[$routeMap[$channel]] = trim((string) $address);
    }

    $errors = [];
    $successCount = 0;

    foreach ($channels as $channel) {
      try {
        Notification::routes([$routeMap[$channel] => $routes[$routeMap[$channel]]])->notify(
          new AnalysisResultNotification(
            pdfUrl: $data['pdf_url'] ?? 'https://example.com/test-report.pdf',
            date: $data['date'] ?? now()->format('Y-m-d'),
            reference: $data['reference'] ?? 'ANALYSIS-TEST-001',
            contact: $data['contact'] ?? config('services.clinic.contact', ''),
            channels: [$channel],
          )
        );

        $successCount++;
      } catch (Exception $e) {
        $errors[$channel] = $e->getMessage();
      }
    }

    $totalChannels = count($channels);
    $message = match (true) {
      $successCount === $totalChannels => 'success',
      $successCount > 0 => 'partial success',
      default => 'total failure',
    };

    return response()->json([
      'status' => 1,
      'message' => $message,
      'errors' => $errors,
      'data' => [
        'channels' => $channels,
        'to' => $routes,
      ],
    ]);
  }
}
