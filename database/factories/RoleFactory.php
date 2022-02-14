<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RoleFactory extends Factory
{
    /**
     * Roles data
     * 
     * @var array
     */
    public $roles = [
        ['id' => 1, 'role' => "root", 'name' => "Владелец", 'comment' => "Доступ ко всему фкнционалу"],
        ['id' => 2, 'role' => "admin", 'name' => "Администоратор", 'comment' => "Администоратор сайта"],
        ['id' => 3, 'role' => "friend", 'name' => "Друзья", 'comment' => "Доступ к функционалу друзей"],
    ];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $key = rand(0, count($this->roles) - 1);

        return array_merge($this->roles[$key], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
