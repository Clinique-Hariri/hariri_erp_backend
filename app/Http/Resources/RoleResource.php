<?php

namespace App\Http\Resources;

use App\Support\Enum\PermissionNames;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

/** @mixin Role */
class RoleResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'permissions_count' => $this->permissions_count,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,

      'permissions' => $this->whenLoaded('permissions', function () {
        return $this->permissions
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
              'group' => $group,
              'group_label' => PermissionNames::get_group_name($group),
              'items' => $permissions->values(),
            ];
          })
          ->values() //تحويل المجموعات إلى مصفوفة مرقمة
          ->toArray();
      }),
    ];
  }
}
