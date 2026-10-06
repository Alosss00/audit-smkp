<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sesis = \App\Models\AuditSesi::with('perusahaan')->get();
foreach ($sesis as $s) {
    echo "ID: {$s->id} | perusahaan_id: {$s->perusahaan_id} | Nama Perusahaan: " . ($s->perusahaan->nama_perusahaan ?? 'NULL') . " | Area: {$s->area_audit} | Tahun: {$s->tahun_periode}\n";
}
