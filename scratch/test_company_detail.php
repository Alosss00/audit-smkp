<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sesi = App\Models\AuditSesi::first();
$perusahaanId = $sesi->perusahaan_id;

$sessions = App\Models\AuditSesi::with(['user', 'perusahaan', 'auditDetails.kriteria.subElemen.elemen'])
    ->where('perusahaan_id', $perusahaanId)
    ->get();

$allDetails = $sessions->pluck('auditDetails')->flatten();

// 1. Rekap Elemen & Hierarki
$elemens = App\Models\Elemen::with(['subElemens.kriterias'])->orderBy('kode_elemen')->get();

$hierarkis = [];
$rekapElemen = [];
$totalSkorAkhir = 0;

foreach ($elemens as $elemen) {
    $elAktual = 0;
    $elMaks = 0;
    $subList = [];

    foreach ($elemen->subElemens as $sub) {
        $subAktual = 0;
        $subMaks = 0;
        $subDetails = [];

        $matchedDetails = $allDetails->filter(fn($d) => $d->kriteria && $d->kriteria->sub_elemen_id == $sub->id);

        foreach ($matchedDetails as $d) {
            if (!$d->is_na) {
                $subAktual += (float)$d->nilai;
                $subMaks += (float)($d->kriteria->nilai_maksimal ?? 4);
            }

            $subDetails[] = [
                'id' => $d->id,
                'kriteria_id' => $d->kriteria_id,
                'kode_kriteria' => $d->kriteria->kode_kriteria ?? '-',
                'deskripsi' => $d->kriteria->deskripsi ?? '-',
                'nama_kriteria' => $d->kriteria->deskripsi ?? '-',
                'nilai' => (int)round($d->nilai),
                'nilai_aktual' => (int)round($d->nilai),
                'nilai_maksimal' => (int)round($d->kriteria->nilai_maksimal ?? 4),
                'is_na' => (bool)$d->is_na,
                'catatan' => $d->catatan,
                'lampiran_url' => $d->lampiran_url,
                'lampiran_urls' => $d->lampiran_urls,
                'has_pica' => (bool)$d->pica,
                'pica_kategori' => $d->pica ? $d->pica->kategori_temuan : null,
            ];
        }

        $subPct = $subMaks > 0 ? ($subAktual / $subMaks) * 100 : 0;
        $elAktual += $subAktual;
        $elMaks += $subMaks;
        $isDirect = count($subDetails) === 1;

        $subList[] = [
            'sub_elemen_id' => $sub->id,
            'kode_sub' => $sub->kode_sub,
            'kode_sub_elemen' => $sub->kode_sub,
            'nama_sub' => $sub->nama_sub,
            'nama_sub_elemen' => $sub->nama_sub,
            'bobot' => (float)($sub->bobot ?? 0),
            'total_nilai_aktual' => (int)round($subAktual),
            'nilai_aktual' => (int)round($subAktual),
            'total_nilai_maks_efektif' => (int)round($subMaks),
            'nilai_maks_efektif' => (int)round($subMaks),
            'persentase' => round($subPct, 2),
            'details' => $subDetails,
            'kriterias' => $subDetails,
            'is_direct' => $isDirect,
            'direct_detail' => $isDirect ? ($subDetails[0] ?? null) : null,
        ];
    }

    $persentase = $elMaks > 0 ? ($elAktual / $elMaks) * 100 : 0;
    $skorElemen = $elMaks > 0 ? ($elAktual / $elMaks) * (float)$elemen->bobot : 0;
    $totalSkorAkhir += $skorElemen;

    $hierarkis[] = [
        'elemen_id' => $elemen->id,
        'kode_elemen' => $elemen->kode_elemen,
        'nama_elemen' => $elemen->nama_elemen,
        'bobot' => (float)$elemen->bobot,
        'total_nilai_aktual' => (int)round($elAktual),
        'nilai_aktual' => (int)round($elAktual),
        'total_nilai_maks_efektif' => (int)round($elMaks),
        'nilai_maks_efektif' => (int)round($elMaks),
        'persentase' => round($persentase, 2),
        'skor_elemen' => round($skorElemen, 2),
        'sub_elemens' => $subList,
    ];

    $rekapElemen[] = [
        'elemen_id' => $elemen->id,
        'kode_elemen' => $elemen->kode_elemen,
        'nama_elemen' => $elemen->nama_elemen,
        'bobot' => (float)$elemen->bobot,
        'total_nilai_aktual' => (int)round($elAktual),
        'total_nilai_maks_efektif' => (int)round($elMaks),
        'persentase' => round($persentase, 2),
        'skor_elemen' => round($skorElemen, 2),
    ];
}

echo "Hierarkis count: " . count($hierarkis) . "\n";
echo "Rekap Elemen count: " . count($rekapElemen) . "\n";
echo "Skor Akhir: " . round($totalSkorAkhir, 2) . "%\n";
