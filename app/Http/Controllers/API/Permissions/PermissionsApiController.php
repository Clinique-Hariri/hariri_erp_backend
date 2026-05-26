<?php

namespace App\Http\Controllers\API\Permissions;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class PermissionsApiController extends Controller
{
  use ApiResponseTrait;
  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::PERMISSIONS_VIEW);
    }

    try {
      $permissions = Permission::query();

      if ($request->boolean('paginate')) {
        $permissions = $permissions->paginate($request->get('per_page', 10));
      } else {
        $permissions = $permissions->get();
      }

      $grouped = $permissions
        ->map(function ($permission) {
          return [
            'id'    => $permission->id,
            'name'  => $permission->name,
            'label' => PermissionNames::get_name($permission->name),
          ];
        })
        ->groupBy(function ($permission) {
          // نجمع حسب الجزء الأول قبل النقطة
          return explode('.', $permission['name'])[0];
        })
        ->map(function ($permissions, $group) {
          return [
            'group'       => $group,
            'group_label' => PermissionNames::get_group_name($group),
            'items'       => $permissions->values(),
          ];
        })
        ->values() // تحويل المجموعات إلى مصفوفة مرقمة
        ->toArray();

      $response['status'] = 1;
      $response['message'] = 'Success';
      $response['data'] = $grouped;
      if ($request->boolean('paginate')) {
        $response['meta'] = [
          'current_page' => $permissions->currentPage(),
          'per_page' => $permissions->perPage(),
          'total' => $permissions->total(),
          'last_page' => $permissions->lastPage(),
        ];
        $response['links'] = [
          'first' => $permissions->url(1),
          'last'  => $permissions->url($permissions->lastPage()),
          'prev'  => $permissions->previousPageUrl(),
          'next'  => $permissions->nextPageUrl(),
        ];
      }
      return response()->json($response, 200);

    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
