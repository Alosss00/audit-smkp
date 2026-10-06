<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AuditSesi;

// Update PT Tambang Tondano Nusa Jaya (TTN) for Periode 2025
$sesis = AuditSesi::where('tahun_periode', '2025')
    ->where(function($q) {
        $q->where('area_audit', 'like', '%Tondano%')
          ->orWhere('area_audit', 'like', '%TTN%')
          ->orWhereHas('perusahaan', function($qp) {
              $qp->where('nama_perusahaan', 'like', '%Tondano%')
                ->orWhere('nama_perusahaan', 'like', '%TTN%');
          });
    })->get();

echo "Memperbarui sesi audit PT. TTN periode tahun 2025...\n";
foreach ($sesis as $s) {
    $s->tanggal_mulai = '2026-01-05';
    $s->tanggal_selesai = '2026-01-07';
    $s->save();
    echo "BERHASIL DIUBAH -> ID: {$s->id} | Area: {$s->area_audit} | Tahun Periode: {$s->tahun_periode} | Tanggal Pelaksanaan: {$s->tanggal_mulai->format('d F Y')} s/d {$s->tanggal_selesai->format('d F Y')}\n";
}

echo "\n--- HASIL DAFTAR SESI AUDIT TERBARU ---\n";
$all = AuditSesi::with('perusahaan')->get();
foreach ($all as $s) {
    echo "ID: {$s->id} | Perusahaan/Area: " . ($s->perusahaan->nama_perusahaan ?? $s->area_audit) . " | Tahun Periode: {$s->tahun_periode} | Tanggal Pelaksanaan: " . ($s->tanggal_mulai ? $s->tanggal_mulai->format('d M Y') : '-') . " s/d " . ($s->tanggal_selesai ? $s->tanggal_selesai->format('d M Y') : '-') . "\n";
}
