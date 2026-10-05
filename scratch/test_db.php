<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sesis = App\Models\AuditSesi::with('perusahaan')->get(['id', 'area_audit', 'tahun_periode', 'tanggal_mulai', 'perusahaan_id']);
foreach ($sesis as $s) {
    echo "ID: {$s->id} | Area: {$s->area_audit} | Perusahaan: " . ($s->perusahaan->nama_perusahaan ?? 'N/A') . " | Tahun: {$s->tahun_periode} | Mulai: {$s->tanggal_mulai}\n";
}
