<?php

if (php_sapi_name() !== 'cli' || (function_exists('app') && app()->isProduction())) {
    http_response_code(403);
    exit('Forbidden: Script hanya dapat dijalankan melalui CLI di environment non-production.');
}

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kunci = Illuminate\Support\Facades\DB::table('kriteria_relasi')->where('jenis_relasi', 'kunci')->first();
$referensi = Illuminate\Support\Facades\DB::table('kriteria_relasi')->where('jenis_relasi', 'referensi')->first();
$count = Illuminate\Support\Facades\DB::table('kriteria_relasi')->count();

echo "Total Data: " . $count . PHP_EOL;
echo "Contoh Kunci: " . ($kunci->kriteria_asal_kode ?? 'TIDAK ADA') . PHP_EOL;
echo "Contoh Referensi: " . ($referensi->kriteria_asal_kode ?? 'TIDAK ADA') . PHP_EOL;
