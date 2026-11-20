<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Seeder Passwords
    |--------------------------------------------------------------------------
    |
    | Credentials used when seeding default user accounts.
    | In production (app()->isProduction()), if no password is configured,
    | the seeder generates a secure random password (Str::random(16))
    | and warns in the console, preventing insecure default credentials.
    |
    */
    'default_password' => env('SEED_DEFAULT_PASSWORD'),
    'admin_password'   => env('SEED_ADMIN_PASSWORD'),
    'auditor_password' => env('SEED_AUDITOR_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Production Seeding Protection Flag
    |--------------------------------------------------------------------------
    |
    | DatabaseSeeder will refuse to run in production unless this flag is true.
    |
    */
    'allow_production' => (bool) env('SEED_ALLOW_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Fallback Development Password
    |--------------------------------------------------------------------------
    |
    | Fallback password used only in non-production environments (local/testing)
    | when SEED_DEFAULT_PASSWORD is not provided.
    |
    */
    'fallback_dev_password' => env('SEED_FALLBACK_DEV_PASSWORD', 'password'),
];
