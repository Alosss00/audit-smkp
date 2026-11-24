<?php

if (php_sapi_name() !== 'cli' || (function_exists('app') && app()->isProduction())) {
    http_response_code(403);
    exit('Forbidden: Script hanya dapat dijalankan melalui CLI di environment non-production.');
}

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kriterias = Illuminate\Support\Facades\DB::table('kriterias')->limit(10)->pluck('kode_kriteria')->toArray();
echo implode(', ', $kriterias) . PHP_EOL;
