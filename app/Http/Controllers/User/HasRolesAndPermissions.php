<?php

namespace App\Http\Controllers\User;

use App\Models\Permission;
use App\Models\Role;

trait HasRolesAndPermissions
{
    /**
     * Verified permissions
     * 
     * @var array
     */
    protected $verified = [];

    /**
     * Checking user permissions
     * 
     * @param array $permits
     */
    public function can(...$permits)
    {
        if (!count($permits))
            return false;

        if ($verified = $this->canVerified($permits))
            return true;

        if ($verified === false)
            return false;

        foreach ($this->permissions()->whereIn('permission', $permits)->get() as $row) {
            $this->verified[$row->permission] = true;
        }

        foreach ($this->roles as $role) {
            foreach ($role->permissions()->whereIn('permission', $permits)->get() as $row)
                $this->verified[$row->permission] = true;
        }

        $checked = array_keys($this->verified);

        foreach ($permits as $permit) {
            if (!in_array($permit, $checked))
                $this->verified[$permit] = false;
        }

        return (bool) $this->canVerified($permits);
    }

    /**
     * Check verified permits
     * 
     * @return null|boolean
     */
    protected function canVerified($permits)
    {
        $check = false;
        $checked = array_keys($this->verified);

        foreach ($permits as $permit) {

            if (!in_array($permit, $checked)) {
                $check = true;
            }

            if (in_array($permit, $checked)) {
                if ($this->verified[$permit] === true) {
                    return true;
                }
            }
        }

        if ($check)
            return null;

        return false;
    }

    /**
     * Returns vetified permits
     * 
     * @return array
     */
    public function verified()
    {
        return $this->verified;
    }

    /**
     * Roles owned by the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Permissions owned by the user.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

    /**
     * All permissions owned by the user.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allPermissions()
    {
        $permissions = $this->permissions;

        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission)
                $permissions->push($permission);
        }
        
        return $permissions->unique();
    }

    /**
     * Adds a roles to a user.
     * 
     * @param array<int> $ids
     * @return null
     */
    public function assignRole(...$ids)
    {
        $available = $this->roles()->whereIn('id', $ids)->get()->map(function ($row) {
            return $row->id;
        })->toArray();

        foreach ($ids as $id) {

            if (!in_array($id, $available))
                $this->roles()->attach($id);
        }

        return null;
    }

    /**
     * Retrieve a roles from a user.
     * 
     * @param array<int> $ids
     * @return null
     */
    public function unassignRole(...$ids)
    {
        $this->roles()->whereIn('id', $ids)->each(function ($row) {
            $this->roles()->detach($row->id);
        });

        return null;
    }

    /**
     * Adds a permissions to a user.
     * 
     * @param array<int> $ids
     * @return null
     */
    public function assignPermission(...$ids)
    {
        $available = $this->permissions()->whereIn('id', $ids)->get()->map(function ($row) {
            return $row->id;
        })->toArray();

        foreach ($ids as $id) {

            if (!in_array($id, $available))
                $this->permissions()->attach($id);
        }

        return null;
    }

    /**
     * Retrieve a permissions from a user.
     * 
     * @param array<int> $ids
     * @return null
     */
    public function unassignPermission(...$ids)
    {
        $this->permissions()->whereIn('id', $ids)->each(function ($row) {
            $this->permissions()->detach($row->id);
        });

        return null;
    }
}
