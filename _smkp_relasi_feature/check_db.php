<?php
require 'C:/laragon/www/audit/vendor/autoload.php';
$app = require_once 'C:/laragon/www/audit/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kunci = Illuminate\Support\Facades\DB::table('kriteria_relasi')->where('jenis_relasi', 'kunci')->first();
$referensi = Illuminate\Support\Facades\DB::table('kriteria_relasi')->where('jenis_relasi', 'referensi')->first();
$count = Illuminate\Support\Facades\DB::table('kriteria_relasi')->count();

echo "Total Data: " . $count . PHP_EOL;
echo "Contoh Kunci: " . ($kunci->kriteria_asal_kode ?? 'TIDAK ADA') . PHP_EOL;
echo "Contoh Referensi: " . ($referensi->kriteria_asal_kode ?? 'TIDAK ADA') . PHP_EOL;
