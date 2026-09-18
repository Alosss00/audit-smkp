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
        // 1. Administrator (Akses Penuh Master Data, User Management, & Penilaian)
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

        // 2. Auditor SMKP (Akses Dashboard, Penilaian & Monitoring)
        User::updateOrCreate(
            ['username' => 'auditor_smkp'],
            [
                'name'      => 'Auditor SMKP Minerba',
                'email'     => 'auditor.smkp@smkp.id',
                'password'  => Hash::make('password'),
                'role'      => 'auditor_smkp',
                'is_active' => true,
            ]
        );

        // 3. Auditor Perusahaan (Auditee / PIC Area Kerja)
        User::updateOrCreate(
            ['username' => 'auditor'],
            [
                'name'      => 'Auditor Perusahaan (PT MSM)',
                'email'     => 'auditor.msm@smkp.id',
                'password'  => Hash::make('password'),
                'role'      => 'auditor',
                'area'      => 'PT. Meares Soputan Mining',
                'is_active' => true,
            ]
        );
    }
}
