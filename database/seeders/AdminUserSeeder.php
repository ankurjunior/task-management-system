<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@Organization.in'],
            [
                'username' => 'admin',
                'name' => 'System Administrator',
                'password' => Hash::make('Admin@123'),
                'role_id' => 1,
                'designation_id' => null,
                'reporting_to_user_id' => null,
                'mobile' => '9999999999',
                'is_active' => 1,
            ]
        );
    }
}