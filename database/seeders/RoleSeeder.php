<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $factory = Role::factory();
        $count = count($factory->roles);

        $factory->count($count)
            ->sequence(fn ($sequence) => $factory->roles[$sequence->index])
            ->create();
    }
}
