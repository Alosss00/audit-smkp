<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sesis = App\Models\AuditSesi::with('perusahaan')->get();
echo "--- DAFTAR SESI AUDIT SAAT INI ---\n";
foreach ($sesis as $s) {
    echo "ID: {$s->id} | Area: {$s->area_audit} | Perusahaan: " . ($s->perusahaan->nama_perusahaan ?? '-') . " | Tahun Periode: {$s->tahun_periode} | Pelaksanaan: " . ($s->tanggal_mulai ? $s->tanggal_mulai->format('Y-m-d') : '-') . " s/d " . ($s->tanggal_selesai ? $s->tanggal_selesai->format('Y-m-d') : '-') . "\n";
}
