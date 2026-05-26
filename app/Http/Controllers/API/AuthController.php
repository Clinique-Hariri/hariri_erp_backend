<?php

namespace App\Http\Controllers\API;

use App\Constants\Statuses\UserStatus;
use Exception;
use App\Models\User;
use App\Constants\Gender;
use App\Traits\RandomTrait;
use Illuminate\Http\Request;
use App\Traits\FirebaseTrait;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
  use FirebaseTrait, ApiResponseTrait, RandomTrait;

  public function login(Request $request)
  {
    $this->validateRequest($request, [
      'email' => 'required|email',
      'password' => 'required|string|min:8',
    ]);

    try {
      $user = User::where('email', strtolower(trim($request->email)))->first();

      if (! $user || ! Hash::check($request->password, $user->password)) {
        return $this->errorResponse('Invalid email or password.', 401);
      }

      if ($user->status !== UserStatus::ACTIVE) {
        return $this->errorResponse('Your account is not active.', 403);
      }

      $token = $user->createToken('api_token')->plainTextToken;

      return $this->successResponse(data: [
        'token' => $token,
        'user' => new UserResource($user),
      ]);

    } catch (Exception $e) {
      Log::error('Login error: ' . $e->getMessage());
      return $this->errorResponse('Something went wrong. Try again later.', 500);
    }
  }

  public function logout(Request $request)
  {
    try {

      if (! $request->user() || ! $request->user()->currentAccessToken()) {
        return $this->errorResponse('Unauthorized.', 401);
      }

      $request->user()->currentAccessToken()->delete();

      return $this->successResponse('Logged out.');

    } catch (Exception $e) {
      Log::error('Logout error: ' . $e->getMessage());
      return $this->errorResponse('Something went wrong. Try again later.', 500);
    }
  }

  public function deleteAccount(Request $request)
  {
    try {

      $user = $request->user();
      $user->tokens()->delete();
      $user->delete();

      return $this->successResponse('Account deleted.');

    } catch (Exception $e) {
      Log::error('Delete account error: ' . $e->getMessage());
      return $this->errorResponse('Something went wrong. Try again later.', 500);
    }
  }

  public function updateAccount(Request $request)
  {
    $validated = $this->validateRequest($request, [
      'fullname' => 'required|string|max:255',
      'phone' => 'required|regex:/^(\+?\d{1,3})?(\d{9})$/|unique:users,phone,' . auth()->id(),
      'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:8192',
      'birthdate' => 'nullable|date',
      'gender' => 'required|string|in:' . implode(',', Gender::all()),
    ]);

    try {
      DB::beginTransaction();

      $user = auth()->user();


      if ($request->hasFile('avatar')) {
        $validated['avatar'] = storeWebP($request->file('avatar'), 'uploads/users/avatars');
      }

      $user->update($validated);

      DB::commit();

      return $this->successResponse(data: new UserResource($user));

    } catch (Exception $e) {
      DB::rollBack();
      Log::error('Update account error: ' . $e->getMessage());
      return $this->errorResponse('Something went wrong. Try again later.', 500);
    }
  }

  public function changePassword(Request $request)
  {
    $this->validateRequest(request: $request, rules: [
      'current_password' => 'required|string|min:8',
      'new_password' => 'required|string|min:8|confirmed',
    ]);

    try {
      $user = auth()->user();

      if (! Hash::check(request('current_password'), $user->password)) {
        return $this->errorResponse('Current password is incorrect.', 400);
      }

      $user->update([
        'password' => Hash::make(request('new_password')),
      ]);

      $user->tokens()->delete();
      $token = $user->createToken('api_token')->plainTextToken;

      return $this->successResponse(
        message: 'Password updated successfully.',
        data: [
          'token' => $token,
          'user' => new UserResource($user),
        ]
      );

    } catch (Exception $e) {
      Log::error('Update password error: ' . $e->getMessage());
      return $this->errorResponse('Something went wrong. Try again later.', 500);
    }
  }
}
