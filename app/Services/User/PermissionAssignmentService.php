<?php

namespace App\Services\User;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionAssignmentService
{
    public function __construct() {}

    /**
     * Get all roles with their assigned permissions
     */
    public function getRolesWithPermissions(): array
    {
        $roles = Role::where('guard_name', 'web')->with('permissions')->get();
        $permissions = Permission::where('guard_name', 'web')->get();

        return [
            'roles' => $roles,
            'permissions' => $permissions,
        ];
    }

    /**
     * Get specific role's permissions data
     */
    public function getRolePermissions(Role $role): array
    {
        $allPermissions = Permission::where('guard_name', 'web')->get();
        $rolePermissions = $role->permissions;

        return [
            'role' => $role,
            'allPermissions' => $allPermissions,
            'rolePermissions' => $rolePermissions,
        ];
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissionsToRole(Role $role, array $permissionIds): void
    {
        if (empty($permissionIds)) {
            throw new \InvalidArgumentException('No permissions selected');
        }

        DB::transaction(function () use ($role, $permissionIds) {
            $permissions = Permission::whereIn('id', $permissionIds)->get();

            if ($permissions->count() !== count($permissionIds)) {
                throw new \InvalidArgumentException('Some permissions do not exist');
            }

            $role->syncPermissions($permissions);
        });
    }

    /**
     * Revoke permissions from role
     */
    public function revokePermissionsFromRole(Role $role, array $permissionIds): void
    {
        if (empty($permissionIds)) {
            throw new \InvalidArgumentException('No permissions selected for revocation');
        }

        DB::transaction(function () use ($role, $permissionIds) {
            $permissions = Permission::whereIn('id', $permissionIds)->get();
            $role->revokePermissionTo($permissions);
        });
    }

    /**
     * Get permissions grouped by module
     */
    public function getPermissionsGroupedByModule(): array
    {
        $permissions = Permission::where('guard_name', 'web')->get();

        $grouped = [];
        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);
            $module = $parts[1] ?? 'general';
            $grouped[$module][] = $permission;
        }

        return $grouped;
    }

    /**
     * Check if role has specific permission
     */
    public function roleHasPermission(Role $role, string $permissionName): bool
    {
        return $role->hasPermissionTo($permissionName);
    }
}
