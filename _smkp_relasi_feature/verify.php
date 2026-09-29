<?php
require 'C:/laragon/www/audit/vendor/autoload.php';
$app = require_once 'C:/laragon/www/audit/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hasTable = Illuminate\Support\Facades\Schema::hasTable('kriteria_relasi');
$hasDep   = Illuminate\Support\Facades\Schema::hasColumn('kriterias', 'dependency_id');
$cols     = Illuminate\Support\Facades\Schema::getColumnListing('kriteria_relasi');

echo "kriteria_relasi table : " . ($hasTable ? 'EXISTS OK' : 'MISSING!') . PHP_EOL;
echo "dependency_id column  : " . ($hasDep   ? 'STILL EXISTS (error!)' : 'DROPPED OK') . PHP_EOL;
echo "kriteria_relasi cols  : " . implode(', ', $cols) . PHP_EOL;
