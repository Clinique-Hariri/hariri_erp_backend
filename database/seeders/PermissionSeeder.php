<?php

namespace Database\Seeders;

use App\Support\Enum\PermissionNames;
use App\Support\Enum\UserRoles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
  public function run(): void
  {
    // Create permissions
    foreach (PermissionNames::all() as $permission) {
      Permission::findOrCreate($permission);
    }

    // Define role permissions
    $rolesPermissions = [
      UserRoles::SUPER_ADMIN => PermissionNames::all(),
      UserRoles::ADMIN => PermissionNames::admin(),
      UserRoles::RECEPTIONIST => PermissionNames::receptionist(),
      UserRoles::ACCOUNTANT => PermissionNames::accountant(),
      UserRoles::DOCTOR => PermissionNames::doctor(),
      UserRoles::RADIOLOGY_MANAGER => PermissionNames::radiologyManager(),
      UserRoles::LABORATORY_MANAGER => PermissionNames::laboratoryManager(),
      UserRoles::OPERATIONS_MANAGER => PermissionNames::operationsManager(),
      UserRoles::HUMAN_RESOURCES => PermissionNames::humanResources(),
      UserRoles::INVENTORY_MANAGER => PermissionNames::inventoryManager(),
    ];

    // Assign permissions to roles
    foreach ($rolesPermissions as $role => $permissions) {
      Permission::whereIn('name', $permissions)->each(
        fn($permission) => $permission->assignRole([$role])
      );
    }
  }
}
