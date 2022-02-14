<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Users belonging to a role.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Permissions owned by a role.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Adds a permissions to a role.
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
     * Retrieve a permissions from a role.
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
