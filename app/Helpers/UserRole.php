<?php

namespace App\Helpers;

use Spatie\Permission\Models\Role;

class UserRole
{
    public static function getPossibleRolesForAssining()
    {
        return Role::select('id', 'name')->whereNotIn('name', ['owner'])->get();
    }

    public static function getPossibleRolesIdsForAssining()
    {
        return Role::whereNotIn('name', ['owner'])->pluck('id');
    }
}
