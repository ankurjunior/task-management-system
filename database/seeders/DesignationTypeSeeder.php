<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_designation_types')->insert([
            [
                'name' => 'State Level',
                'description' => 'State level officials',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'District Level',
                'description' => 'District level officials',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}