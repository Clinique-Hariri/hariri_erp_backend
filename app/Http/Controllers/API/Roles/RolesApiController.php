<?php

namespace App\Http\Controllers\API\Roles;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Throwable;

class RolesApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::ROLES_VIEW);
    }

    try {
      $query = Role::query()->withCount('permissions');

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
          $q->where('name', 'like', "%{$searchTerm}%");
        });
      }

      if ($request->filled('created_at_from')) {
        $query->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $query->whereDate('created_at', '<=', $request->created_at_to);
      }

      if ($request->boolean('paginate')){
        $model = $query->paginate($request->get('per_page', 10));
      } else {
        $model = $query->get();
      }

      return $this->successResponse(
        data: RoleResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission(PermissionNames::ROLES_VIEW);

    try {
      $role = Role::with(['permissions'])->withCount('permissions')->findOrFail($id);

      return $this->successResponse(
        data: new RoleResource($role)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNames::ROLES_CREATE);

    $data = $this->validateRequest(
      request: $request,
      rules: [
        'name' => 'required|string|max:255|unique:roles,name',
        'permissions' => 'nullable|array',
        'permissions.*' => 'string|exists:permissions,name',
      ]);

    try {
      $data['guard_name'] = 'web';

      $role = Role::create($data);
      if (isset($data['permissions'])) {
        $role->syncPermissions($data['permissions']);
      }

      $role->loadCount('permissions');

      return $this->successResponse(
        data: new RoleResource($role),
      );

    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNames::ROLES_UPDATE);

    $data = $this->validateRequest(
      request: $request,
      rules: [
        'name' => 'required|string|max:255|unique:roles,name,' . $id,
        'permissions' => 'nullable|array',
        'permissions.*' => 'string|exists:permissions,name',
        ]);

    try {
      $role = Role::findOrFail($id);
      $role->update($data);

      if (isset($data['permissions'])) {
        $role->syncPermissions($data['permissions']);
      }

      $role->loadCount('permissions');

      return $this->successResponse(
        data: new RoleResource($role),
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {

    $this->authorizePermission(PermissionNames::ROLES_DELETE);

    try {

      $role = Role::findOrFail($id);
      $role->permissions()->detach();
      $role->delete();

      return $this->successResponse(data: [
        'total' => Role::count()
      ]);
    } catch (QueryException $e) {
      // MySQL foreign key violation code = 23000
      if ($e->getCode() == 23000) {
        return response()->json([
          'success' => false,
          'message' => __('messages.cannot_delete_record_linked_to_other_records')
        ], 400);
      }

      // Fallback for any other database error
      return response()->json([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
      ], 500);
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
