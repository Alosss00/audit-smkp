<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sesi = App\Models\AuditSesi::first();
echo "Testing for Sesi ID: {$sesi->id}, Perusahaan: " . ($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit) . "\n";

$companySessions = App\Models\AuditSesi::with(['user', 'perusahaan', 'auditDetails.kriteria.subElemen.elemen'])
    ->where('perusahaan_id', $sesi->perusahaan_id)
    ->get();

echo "Found " . $companySessions->count() . " sessions for this company.\n";

$allDetails = $companySessions->pluck('auditDetails')->flatten();
echo "Total Audit Details across sessions: " . $allDetails->count() . "\n";

$elemens = App\Models\Elemen::orderBy('kode_elemen')->get();
$rekap = [];
$totalSkorAkhir = 0;

foreach ($elemens as $el) {
    $matched = $allDetails->filter(function($d) use ($el) {
        return $d->kriteria && $d->kriteria->subElemen && $d->kriteria->subElemen->elemen_id == $el->id;
    });

    $aktual = 0;
    $maks = 0;
    foreach ($matched as $d) {
        if (!$d->is_na) {
            $aktual += (float)$d->nilai;
            $maks += (float)($d->kriteria->nilai_maksimal ?? 4);
        }
    }

    $pct = $maks > 0 ? ($aktual / $maks) * 100 : 0;
    $skorEl = $maks > 0 ? ($aktual / $maks) * (float)$el->bobot : 0;
    $totalSkorAkhir += $skorEl;

    $rekap[] = [
        'kode_elemen' => $el->kode_elemen,
        'nama_elemen' => $el->nama_elemen,
        'total_aktual' => $aktual,
        'total_maks' => $maks,
        'persentase' => round($pct, 2),
        'skor_elemen' => round($skorEl, 2)
    ];
}

echo "Total Skor Akhir Perusahaan (Akumulasi Semua Periode): " . round($totalSkorAkhir, 2) . "%\n";
