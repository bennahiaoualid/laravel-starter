<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

trait RoleManipulation
{
    private ?Collection $cachedRoles = null;

    public function possibleRoles(): Collection
    {
        if ($this->cachedRoles !== null) {
            return $this->cachedRoles;
        }

        $role = Auth::user()->roles->pluck('name')->first();

        $this->cachedRoles = ($role === 'super_admin')
            ? Role::select('id', 'name')->whereNotIn('name', ['super_admin', 'owner'])->get()
            : Role::select('id', 'name')->get();

        return $this->cachedRoles;
    }

    public function possibleRolesIds($role)
    {
        if ($role == 'super_admin') {
            return Role::whereNotIn('name', ['super_admin', 'owner'])->pluck('id');
        } else {
            return Role::pluck('id');
        }
    }
}
