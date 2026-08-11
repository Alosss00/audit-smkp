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
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator SMKP',
                'email' => 'admin@smkp.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 1 Auditor User
        User::firstOrCreate(
            ['username' => 'auditor'],
            [
                'name' => 'Auditor Internal SMKP',
                'email' => 'auditor@smkp.id',
                'password' => Hash::make('password'),
                'role' => 'auditor',
            ]
        );
    }
}
