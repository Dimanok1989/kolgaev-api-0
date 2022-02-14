<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Permissions data
     * 
     * @var array
     */
    public $permissions = [
        ['id' => 1, 'permission' => "disk_access", 'comment' => "Доступ к диску"],
        ['id' => 2, 'permission' => "fuel_access", 'comment' => "Доступ к расходу топлива"],
        ['id' => 3, 'permission' => "admin_access", 'comment' => "Доступ к админ панели"],
    ];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $key = rand(0, count($this->permissions) - 1);

        return array_merge($this->permissions[$key], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
