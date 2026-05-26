<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Notifications\TestSmsNotification;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SmsTestController extends Controller
{
  use ApiResponseTrait;

  public function send(Request $request)
  {
    $data = $this->validateRequest($request, [
      'to' => ['required', 'string', 'max:30'],
      'message' => ['required', 'string', 'max:500'],
    ]);

    try {
      Notification::route('sms', trim($data['to']))
        ->notifyNow(new TestSmsNotification($data['message']));

      return $this->successResponse(
        message: 'SMS test notification sent successfully.',
        data: [
          'to' => trim($data['to']),
          'message' => $data['message'],
        ]
      );
    } catch (Exception $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
