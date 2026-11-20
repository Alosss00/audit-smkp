<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->isProduction() && !config('seeding.allow_production', false)) {
            throw new \RuntimeException(
                'Database seeding di environment production diblokir demi keamanan. ' .
                'Set SEED_ALLOW_PRODUCTION=true jika memang disengaja.'
            );
        }

        $this->call([
            UserSeeder::class,
            SMKPSeeder::class,
            PerusahaanSeeder::class,
            SampleAuditSeeder::class,
            KriteriaGatingRuleSeeder::class,
        ]);
    }
}
