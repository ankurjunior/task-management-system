<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(

            [
                'email' => 'admin@Organization.gov.in'
            ],

            [
                'name' => 'System Admin',
                'username' => 'admin',
                'password' => Hash::make('Admin@123'),
                'role_id' => 1,
                'is_active' => 1
            ]
        );
    }
}