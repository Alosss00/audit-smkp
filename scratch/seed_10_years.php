<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AuditSesi;
use App\Models\AuditDetail;
use App\Models\Kriteria;
use App\Models\Perusahaan;

$years = range(2017, 2026);
$kriterias = Kriteria::all();

if ($kriterias->isEmpty()) {
    echo "Tidak ada kriteria di database!\n";
    exit;
}

$perusahaans = Perusahaan::all();
if ($perusahaans->isEmpty()) {
    // Fallback company IDs
    $companies = [
        ['id' => 1, 'nama' => 'PT Meares Soputan Mining'],
        ['id' => 2, 'nama' => 'PT Tambang Tondano Nusa Jaya'],
    ];
} else {
    $companies = $perusahaans->map(function($p) {
        return ['id' => $p->id, 'nama' => $p->nama_perusahaan];
    })->toArray();
}

$createdCount = 0;

foreach ($years as $yearIndex => $yr) {
    foreach ($companies as $comp) {
        // Check if sample session already exists
        $existing = AuditSesi::where('perusahaan_id', $comp['id'])
            ->where('tahun_periode', $yr)
            ->first();

        if ($existing) {
            echo "Sesi untuk {$comp['nama']} tahun {$yr} sudah ada (ID: {$existing->id}), dilewati.\n";
            continue;
        }

        // Base score factor (slight variation and progression over years)
        // 2017 ~ 70%, 2026 ~ 90%
        $baseRatio = 0.68 + ($yearIndex * 0.022) + (($comp['id'] == 2 ? 0.03 : 0.0));

        $sesi = AuditSesi::create([
            'perusahaan_id'   => $comp['id'],
            'area_audit'      => $comp['nama'],
            'tanggal_mulai'   => "{$yr}-01-10",
            'tanggal_selesai' => "{$yr}-01-20",
            'tahun_periode'   => $yr,
            'user_id'         => 1,
            'lead_auditor_id' => 1,
            'status'          => 'selesai',
            'catatan_rekap'   => 'SAMPEL_10_TAHUN',
        ]);

        foreach ($kriterias as $kIndex => $k) {
            $maxVal = $k->nilai_maksimal ?? 4;
            // Generate realistic integer score (0 to maxVal) based on baseRatio
            $randomFactor = (($kIndex % 5) - 2) * 0.04;
            $finalRatio = min(1.0, max(0.4, $baseRatio + $randomFactor));
            $scoreVal = (int) round($maxVal * $finalRatio);

            AuditDetail::create([
                'audit_sesi_id' => $sesi->id,
                'kriteria_id'   => $k->id,
                'nilai'         => $scoreVal,
                'catatan'       => 'Sampel otomatis 10 tahun',
                'is_na'         => false,
            ]);
        }

        $finalScore = $sesi->hitungSkorAkhir();
        echo "Berhasil membuat sampel audit: {$comp['nama']} ({$yr}) -> Skor: {$finalScore}%\n";
        $createdCount++;
    }
}

echo "\nSelesai! Total {$createdCount} sesi sampel 10 tahun berhasil dibuat.\n";
