<?php

use App\Http\Controllers\API\Permissions\PermissionsApiController;
use App\Http\Controllers\API\NotificationsController;
use App\Http\Controllers\API\Roles\RolesApiController;
use App\Http\Controllers\API\SmsTestController;
use App\Http\Controllers\API\Users\UsersApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Support\Enum\UserTypes;
use App\Support\Enum\UserRoles;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
  Route::post('synch/orders', [\App\Http\Controllers\Synchronization\OrderController::class, 'send']);

  Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('delete-account', [AuthController::class, 'deleteAccount'])->middleware('auth:sanctum');
    Route::post('update-account', [AuthController::class, 'updateAccount'])->middleware('auth:sanctum');
    Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
  });

  Route::post('notifications/test-send', [NotificationsController::class, 'testSend']);
  Route::post('sms/test-send', [SmsTestController::class, 'send']);

  //  authenticated routes
  Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('roles', RolesApiController::class);
    Route::apiResource('permissions', PermissionsApiController::class)->only(['index']);

    Route::apiResource('users', UsersApiController::class)->except(['store', 'destroy']);
    Route::post('users/{user}/change-password', [UsersApiController::class, 'changePassword']);
    Route::post('users/{user}/update-status', [UsersApiController::class, 'updateStatus']);

    Route::get('/user', function (Request $request) {
      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => new \App\Http\Resources\User\UserResource($request->user())
      ]);
    });

    Route::get('/user-types', function () {
      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => UserTypes::all(true)
      ]);
    });

    Route::get('/user-roles', function () {
      return response()->json([
        'status' => 1,
        'message' => 'success',
        'data' => UserRoles::all(true)
      ]);
    });
  });
});
