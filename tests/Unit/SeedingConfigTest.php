<?php

namespace Tests\Unit;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\UserSeeder;
use RuntimeException;
use Tests\TestCase;

class SeedingConfigTest extends TestCase
{
    /**
     * Seeder uses the configured default password if set.
     */
    public function test_seeder_uses_configured_default_password(): void
    {
        config([
            'seeding.default_password' => 'SecretGlobalPass!123',
            'seeding.admin_password'   => null,
            'seeding.auditor_password' => null,
        ]);

        $seeder = new UserSeeder();
        $randomTable = [];

        $adminPass = $seeder->resolvePassword('admin', 'admin', $randomTable);
        $auditorPass = $seeder->resolvePassword('auditor', 'auditor', $randomTable);

        $this->assertSame('SecretGlobalPass!123', $adminPass);
        $this->assertSame('SecretGlobalPass!123', $auditorPass);
        $this->assertEmpty($randomTable);
    }

    /**
     * Seeder prioritizes role-specific password over default password.
     */
    public function test_seeder_prioritizes_role_specific_password(): void
    {
        config([
            'seeding.default_password' => 'FallbackGlobalPass',
            'seeding.admin_password'   => 'AdminExclusivePass#99',
            'seeding.auditor_password' => 'AuditorExclusivePass#77',
        ]);

        $seeder = new UserSeeder();
        $randomTable = [];

        $adminPass = $seeder->resolvePassword('admin', 'admin', $randomTable);
        $auditorPass = $seeder->resolvePassword('auditor', 'auditor', $randomTable);

        $this->assertSame('AdminExclusivePass#99', $adminPass);
        $this->assertSame('AuditorExclusivePass#77', $auditorPass);
        $this->assertEmpty($randomTable);
    }

    /**
     * In non-production environments, empty configuration falls back to fallback_dev_password.
     */
    public function test_seeder_uses_fallback_in_non_production_when_empty(): void
    {
        config([
            'seeding.default_password'      => null,
            'seeding.admin_password'        => null,
            'seeding.auditor_password'      => null,
            'seeding.fallback_dev_password' => 'custom_dev_fallback',
        ]);

        $seeder = new UserSeeder();
        $randomTable = [];

        $pass = $seeder->resolvePassword('admin', 'admin', $randomTable);

        $this->assertSame('custom_dev_fallback', $pass);
        $this->assertEmpty($randomTable);
    }

    /**
     * In production environment, empty configuration generates a random 16-character password and warns.
     */
    public function test_seeder_generates_random_password_in_production_when_empty(): void
    {
        $originalEnv = $this->app->environment();
        $this->app['env'] = 'production';

        try {
            config([
                'seeding.default_password'      => null,
                'seeding.admin_password'        => null,
                'seeding.auditor_password'      => null,
                'seeding.fallback_dev_password' => 'insecure_password',
            ]);

            $seeder = new UserSeeder();
            $randomTable = [];

            $generated = $seeder->resolvePassword('admin', 'admin', $randomTable);

            $this->assertNotSame('insecure_password', $generated);
            $this->assertSame(16, strlen($generated));
            $this->assertCount(1, $randomTable);
            $this->assertSame('admin', $randomTable[0]['username']);
            $this->assertSame('admin', $randomTable[0]['role']);
            $this->assertSame($generated, $randomTable[0]['password']);
        } finally {
            $this->app['env'] = $originalEnv;
        }
    }

    /**
     * DatabaseSeeder blocks execution in production when allow_production flag is false.
     */
    public function test_database_seeder_blocks_production_without_flag(): void
    {
        $originalEnv = $this->app->environment();
        $this->app['env'] = 'production';

        try {
            config(['seeding.allow_production' => false]);

            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Database seeding di environment production diblokir');

            $seeder = new DatabaseSeeder();
            $seeder->run();
        } finally {
            $this->app['env'] = $originalEnv;
        }
    }
}
