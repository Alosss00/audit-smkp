<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1 Admin User
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'      => 'Administrator SMKP',
                'email'     => 'admin@smkp.id',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // 1 Auditor User
        User::updateOrCreate(
            ['username' => 'auditor'],
            [
                'name'      => 'Auditor Internal SMKP',
                'email'     => 'auditor@smkp.id',
                'password'  => Hash::make('password'),
                'role'      => 'auditor',
                'area'      => 'Area Tambang Utama Pit West & Haulage Road',
                'is_active' => true,
            ]
        );
    }
}
