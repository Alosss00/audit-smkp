<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Perusahaan;
use App\Models\AuditSesi;

echo "--- DAFTAR PERUSAHAAN ---\n";
foreach (Perusahaan::all() as $p) {
    echo "ID: {$p->id} | Nama: {$p->nama_perusahaan}\n";
}

echo "\n--- DAFTAR SKOR AKHIR SESI AUDIT ---\n";
foreach (AuditSesi::all() as $s) {
    $skor = $s->hitungSkorAkhir();
    echo "ID: {$s->id} | Area: {$s->area_audit} | Tahun: {$s->tahun_periode} | Skor Akhir: {$skor}%\n";
}
