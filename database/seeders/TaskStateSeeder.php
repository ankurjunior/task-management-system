<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskStateSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            ['Initial',1],
            ['Assigned',2],
            ['WIP',3],
            ['On Hold',4],
            ['Completed',5],
            ['Closed',6],
            ['Cancelled',7],
        ];

        foreach($states as $state){
            DB::table('master_task_states')->insert([
                'state_name' => $state[0],
                'sort_order' => $state[1],
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}