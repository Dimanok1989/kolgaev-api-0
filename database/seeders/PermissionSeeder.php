<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $factory = Permission::factory();
        $count = count($factory->permissions);

        $factory->count($count)
            ->sequence(fn ($sequence) => $factory->permissions[$sequence->index])
            ->create();
    }
}
