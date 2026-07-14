<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_designations')->insert([
            [
                'designation_type_id' => 1,
                'name' => 'Director',
                'short_name' => 'DIR',
                'hierarchy_level' => 1,
                'can_assign_task' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'designation_type_id' => 1,
                'name' => 'Deputy Director',
                'short_name' => 'DD',
                'hierarchy_level' => 2,
                'can_assign_task' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'designation_type_id' => 2,
                'name' => 'JCP',
                'short_name' => 'JCP',
                'hierarchy_level' => 3,
                'can_assign_task' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}