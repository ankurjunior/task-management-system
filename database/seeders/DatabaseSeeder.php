<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DesignationTypeSeeder::class,
            DesignationSeeder::class,
            TaskPrioritySeeder::class,
            TaskStateSeeder::class,
            AdminUserSeeder::class,
            DirectorUserSeeder::class,
            UpdateFrequencySeeder::class,
            DistrictSeeder::class,
        ]);
    }
}
