<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskPrioritySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_task_priorities')->insert([
            [
                'priority_name' => 'Critical',
                'priority_code' => 'CRITICAL',
                'sort_order' => 1,
                'default_sla_days' => 1,
                'color_code' => '#DC3545',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'priority_name' => 'High',
                'priority_code' => 'HIGH',
                'sort_order' => 2,
                'default_sla_days' => 3,
                'color_code' => '#FD7E14',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'priority_name' => 'Medium',
                'priority_code' => 'MEDIUM',
                'sort_order' => 3,
                'default_sla_days' => 7,
                'color_code' => '#FFC107',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'priority_name' => 'Low',
                'priority_code' => 'LOW',
                'sort_order' => 4,
                'default_sla_days' => 15,
                'color_code' => '#198754',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}