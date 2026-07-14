<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateFrequencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_update_frequencies')->insert([
            [
                'frequency_name' => 'Daily',
                'frequency_code' => 'DAILY',
                'interval_days' => 1,
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'frequency_name' => 'Weekly',
                'frequency_code' => 'WEEKLY',
                'interval_days' => 7,
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'frequency_name' => 'Fortnightly',
                'frequency_code' => 'FORTNIGHTLY',
                'interval_days' => 15,
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'frequency_name' => 'Monthly',
                'frequency_code' => 'MONTHLY',
                'interval_days' => 30,
                'sort_order' => 4,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
