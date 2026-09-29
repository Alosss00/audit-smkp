<?php
require 'C:/laragon/www/audit/vendor/autoload.php';
$app = require_once 'C:/laragon/www/audit/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kriterias = Illuminate\Support\Facades\DB::table('kriterias')->limit(10)->pluck('kode_kriteria')->toArray();
echo implode(', ', $kriterias) . PHP_EOL;
