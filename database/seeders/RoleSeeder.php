<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions = [
            'view user',
            'add user',
            'update user',
            'delete user',
            'manage roles',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['guard_name' => 'web', 'name' => $permission]);
        }
        $roles = [
            'user Type A',
            'user Type B',
            'user Type C',
        ];

        $role_owner = Role::firstOrCreate(['guard_name' => 'web', 'name' => 'owner']);
        $role_owner->givePermissionTo($permissions);
        foreach ($roles as $role) {
            $role = Role::firstOrCreate(['guard_name' => 'web', 'name' => $role]);
        }
    }
}
