<?php

if (php_sapi_name() !== 'cli' || (function_exists('app') && app()->isProduction())) {
    http_response_code(403);
    exit('Forbidden: Script hanya dapat dijalankan melalui CLI di environment non-production.');
}

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hasTable = Illuminate\Support\Facades\Schema::hasTable('kriteria_relasi');
$hasDep   = Illuminate\Support\Facades\Schema::hasColumn('kriterias', 'dependency_id');
$cols     = Illuminate\Support\Facades\Schema::getColumnListing('kriteria_relasi');

echo "kriteria_relasi table : " . ($hasTable ? 'EXISTS OK' : 'MISSING!') . PHP_EOL;
echo "dependency_id column  : " . ($hasDep   ? 'STILL EXISTS (error!)' : 'DROPPED OK') . PHP_EOL;
echo "kriteria_relasi cols  : " . implode(', ', $cols) . PHP_EOL;
