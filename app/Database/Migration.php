<?php

namespace App\Database;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Migrations\Migration as BaseMigration;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

abstract class Migration extends BaseMigration
{
    const NEW_PERMISSIONS = [];

    const REMOVE_PERMISSIONS = [];

    const STARRED_ROLES = [
        'Super Admin',
        'Employee',
    ];


    public function up()
    {
        if (count(static::REMOVE_PERMISSIONS) > 0) {
            $this->revokeAndDeletePermissionsToRoles(static::REMOVE_PERMISSIONS);
        }

        if (count(static::NEW_PERMISSIONS) > 0) {
            $this->givePermissionsToRoles(static::NEW_PERMISSIONS);
        }

        return true;
    }

    public function down()
    {
        if (count(static::NEW_PERMISSIONS) > 0) {
            $this->revokeAndDeletePermissionsToRoles(static::NEW_PERMISSIONS);
        }

        if (count(static::REMOVE_PERMISSIONS) > 0) {
            $this->givePermissionsToRoles(static::REMOVE_PERMISSIONS);
        }

        return true;
    }

    protected function givePermissionsToRoles(array $permissions_to_roles): bool
    {
        foreach ($permissions_to_roles as $groups) {
            if (!$groups[0] || !$groups[1]) {
                throw new \Exception('Please specify permissions and roles correctly');
            }
            foreach ($groups[0] as $permission) {
                Permission::updateOrCreate(['name' => $permission, 'guard_name' => 'web']);

                foreach ($groups[1] as $role_name) {
                    if ($role_name === '*') {
                        foreach (static::STARRED_ROLES as $starred_role_name) {
                            $this->givePermissionToRole($permission, $starred_role_name);
                        }
                        continue;
                    }
                    $this->givePermissionToRole($permission, $role_name);
                }
            }
        }

        return true;
    }

    protected function givePermissionToRole(string $permission, string $role_name): bool
    {
        $roles = Role::where('name', $role_name)->get();
        foreach ($roles as $role) {
            if (!$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            } else {
                Log::warning(sprintf('role %s already has permission to \'%s\'', $role->name, $permission));
            }
        }

        return true;
    }

    protected function revokeAndDeletePermissionsToRoles(array $permissions_to_roles): bool
    {
        foreach ($permissions_to_roles as $groups) {
            if (!isset($groups[0]) || !isset($groups[1])) {
                throw new \Exception('Please specify permissions and roles correctly');
            }
            foreach ($groups[0] as $permission) {
                $permission_model = Permission::where('name', $permission)->first();
                if (!$permission_model) {
                    Log::warning(sprintf('there is no permission named \'%s\'', $permission));
                } else {
                    foreach ($groups[1] as $role_name) {
                        if ($role_name === '*') {
                            foreach (static::STARRED_ROLES as $starred_role_name) {
                                $this->revokePermissionToRole($permission, $starred_role_name);
                            }
                            continue;
                        }
                        $this->revokePermissionToRole($permission, $role_name);
                    }

                    $permission_model->delete();
                }
            }
        }

        return true;
    }

    protected function revokePermissionToRole(string $permission, string $role_name): bool
    {
        $roles = Role::where('name', $role_name)->get();
        foreach ($roles as $role) {
            if ($role->hasPermissionTo($permission)) {
                $role->revokePermissionTo($permission);
            } else {
                Log::warning(sprintf('role %s hasn\'t permission to \'%s\'', $role->name, $permission));
            }
        }

        return true;
    }
}
