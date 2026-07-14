<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DirectorUserSeeder extends Seeder
{
    public function run(): void
    {
        $directorDesignationId = DB::table('master_designations')
            ->where('short_name', 'DIR')
            ->value('id');

        $adminRoleId = DB::table('roles')
            ->where('name', 'Admin')
            ->value('id');

        User::updateOrCreate(
            ['email' => 'director@Organization.in'],
            [
                'username' => 'director',
                'name' => 'State Director',
                'password' => Hash::make('Director@123'),
                'role_id' => $adminRoleId,
                'designation_id' => $directorDesignationId,
                'district_id' => null,
                'reporting_to_user_id' => null,
                'is_active' => true,
            ]
        );
    }
}
