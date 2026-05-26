<?php

namespace App\Http\Controllers\API\Users;

use App\Constants\Statuses\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Support\Enum\PermissionNames;
use App\Support\Enum\UserTypes;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Throwable;

class UsersApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::USERS_VIEW);
    }

    try {
      $query = User::query();

      if ($request->filled('type')) {
        $query->where('type', $request->type);
      }

      if ($request->filled('status')) {
        $query->where('status', $request->status);
      }

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
          $q->where('fullname', 'like', "%{$searchTerm}%");
        });
      }

      if ($request->filled('created_at_from')) {
        $query->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $query->whereDate('created_at', '<=', $request->created_at_to);
      }

      $allowedSorts = ['id', 'created_at', 'fullname', 'type', 'status'];

      $query->when(
        in_array($request->get('sort_by'), $allowedSorts),
        fn($q) => $q->orderBy($request->get('sort_by'), $request->get('sort_order', 'desc')),
        fn($q) => $q->latest()
      );

      if ($request->boolean('paginate')) {
        $model = $query->paginate($request->get('per_page', 10));
      } else {
        $model = $query->get();
      }

      return $this->successResponse(
        data: UserResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission(PermissionNames::USERS_VIEW);

    try {
      $user = User::findOrFail($id);
      return $this->successResponse(
        data: new UserResource($user)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::USERS_UPDATE);

    $data = $this->validateRequest(
      request: $request,
      rules: [
//        'fullname' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
        'phone' => ['required', 'string', 'max:20', 'unique:users,phone,' . $id],
        'role' => ['required', 'string', 'exists:roles,name'],
      ]);

    try {
      $user = User::findOrFail($id);

      if (isset($data['role'])) {
        $user->syncRoles($data['role']);
      }

      $user->update($data);

      return $this->successResponse(
        data: new UserResource($user)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function changePassword(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::USERS_UPDATE);

    $data = $this->validateRequest(
      request: $request,
      rules: [
        'password' => ['required', 'string', 'min:8', 'confirmed'],
      ]);

    try {
      $user = User::findOrFail($id);
      $user->password = bcrypt($data['password']);
      $user->save();

      return $this->successResponse(
        data: [
          'password' => $request->password
        ]
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function updateStatus(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::USERS_UPDATE);

    $data = $this->validateRequest(
      request: $request,
      rules: [
        'status' => ['required', 'in:' . implode(',', array_keys(UserStatus::all()))],
      ]);

    try {
      $user = User::findOrFail($id);
      $user->status = $data['status'];
      $user->save();

      return $this->successResponse(
        data: new UserResource($user)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
