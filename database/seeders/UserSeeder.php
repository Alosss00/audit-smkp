<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Resolve plain password for the given role and username.
     * In production, generates a secure random password if no configuration is set.
     */
    public function resolvePassword(string $role, string $username, array &$randomTable): string
    {
        // 1. Check role-specific password
        $configured = match ($role) {
            'admin'                   => config('seeding.admin_password'),
            'auditor', 'auditor_smkp' => config('seeding.auditor_password'),
            default                   => null,
        };

        // 2. Check general default password
        if (blank($configured)) {
            $configured = config('seeding.default_password');
        }

        // 3. If configured, return it
        if (!blank($configured)) {
            return (string) $configured;
        }

        // 4. In production: do NOT use default 'password'. Generate random string and warn.
        if (app()->isProduction()) {
            $random = Str::random(16);
            $randomTable[] = [
                'username' => $username,
                'role'     => $role,
                'password' => $random,
            ];
            if ($this->command) {
                $this->command->warn("Production Warning: Generated random password for '{$username}': {$random}");
            }
            return $random;
        }

        // 5. In non-production: safe dev fallback
        return (string) config('seeding.fallback_dev_password', 'password');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $randomTable = [];

        $adminPass = $this->resolvePassword('admin', 'admin', $randomTable);
        $auditorSmkpPass = $this->resolvePassword('auditor_smkp', 'auditor_smkp', $randomTable);
        $auditorPass = $this->resolvePassword('auditor', 'auditor', $randomTable);

        // 1. Administrator (Akses Penuh Master Data, User Management, & Penilaian)
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'      => 'Administrator SMKP',
                'email'     => 'admin@smkp.id',
                'password'  => Hash::make($adminPass),
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
                'password'  => Hash::make($auditorSmkpPass),
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
                'password'  => Hash::make($auditorPass),
                'role'      => 'auditor',
                'area'      => 'PT Meares Soputan Mining',
                'is_active' => true,
            ]
        );

        // Output summary table if random passwords were generated
        if (!empty($randomTable) && $this->command && (app()->isLocal() || app()->runningInConsole())) {
            $this->command->newLine();
            $this->command->info('=== Ringkasan Password Seeder Otomatis (Random) ===');
            $this->command->table(['Username', 'Role', 'Password Baru'], $randomTable);
            $this->command->newLine();
        }
    }
}
