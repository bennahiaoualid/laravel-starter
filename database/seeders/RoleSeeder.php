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
            'view partie',
            'add partie',
            'update partie',
            'delete partie',
            'view investor',
            'add investor',
            'update investor',
            'delete investor',
            'view my_companies',
            'add my_companies',
            'update my_companies',
            'delete my_companies',

            'view purchase_order',
            'add purchase_order',
            'update purchase_order',
            'delete purchase_order',

            'view invoice',
            'add invoice',
            'update invoice',
            'delete invoice',
            'add delivery_receipt',
            'delete delivery_receipt',

            'view product',
            'add product',
            'update product',
            'delete product',

            'view money_transaction',
            'add money_transaction',
            'update money_transaction',
            'delete money_transaction',

            'view cost',
            'add cost',

            'view field',
            'add field',
            'update field',
            'delete field',

            'view bank',
            'add bank',
            'update bank',
            'delete bank',

            'view file',
            'add file',
            'delete file',

            'manage documents',

            'manage report',

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
