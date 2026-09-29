<?php
require 'C:/laragon/www/audit/vendor/autoload.php';
$app = require_once 'C:/laragon/www/audit/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::table('kriteria_relasi')->truncate();

$kriterias = DB::table('kriterias')->pluck('kode_kriteria')->toArray();
$totalKriteria = count($kriterias);

if ($totalKriteria < 5) {
    die("Data kriteria terlalu sedikit.");
}

$now = now()->toDateTimeString();
$relasi = [];

// Helper functions for random dummy text
$referensiTexts = [
    'Kebijakan harus sejalan dengan implementasi di elemen ini.',
    'Catatan audit sering ditemukan beririsan dengan area ini.',
    'Bisa saling mendukung dalam ketersediaan dokumen.',
    'Verifikasi silang untuk kelengkapan administrasi.',
    'Jika dokumen ini ada, kemungkinan dokumen terkait juga sudah disiapkan.',
    'Berhubungan dengan aspek operasional harian.',
    'Secara struktural bergantung pada keputusan manajemen di area ini.'
];

$kunciTexts = [
    'Kritikal: Periksa konsistensi jika terdapat temuan pada area ini!',
    'Warning: Ketidaksesuaian di sini umumnya berdampak pada pelaksanaan elemen terkait.',
    'Perhatian: Pastikan implementasi di lapangan sesuai dengan perencanaan yang tertulis.',
    'Wajib ditinjau silang untuk memastikan akar masalah (root cause).',
    'Temuan di sini biasanya memicu ketidaksesuaian berantai pada elemen tujuan.'
];

foreach ($kriterias as $kriteriaAsal) {
    // 35% chance to have 'referensi' relation
    if (rand(1, 100) <= 35) {
        $numRefs = rand(1, 4); // 1 to 4 references
        for ($i = 0; $i < $numRefs; $i++) {
            $kriteriaTujuan = $kriterias[array_rand($kriterias)];
            if ($kriteriaAsal !== $kriteriaTujuan) {
                $relasi[] = [
                    'kriteria_asal_kode' => $kriteriaAsal,
                    'kriteria_tujuan_kode' => $kriteriaTujuan,
                    'jenis_relasi' => 'referensi',
                    'deskripsi_keterkaitan' => $referensiTexts[array_rand($referensiTexts)],
                    'created_at' => $now, 'updated_at' => $now
                ];
            }
        }
    }

    // 15% chance to have 'kunci' relation
    if (rand(1, 100) <= 15) {
        $kriteriaTujuan = $kriterias[array_rand($kriterias)];
        if ($kriteriaAsal !== $kriteriaTujuan) {
            $relasi[] = [
                'kriteria_asal_kode' => $kriteriaAsal,
                'kriteria_tujuan_kode' => $kriteriaTujuan,
                'jenis_relasi' => 'kunci',
                'deskripsi_keterkaitan' => $kunciTexts[array_rand($kunciTexts)],
                'created_at' => $now, 'updated_at' => $now
            ];
        }
    }
}

// Ensure at least I.1, I.2, I.3 have some so the user easily finds them at the top
$mustHaves = [
    ['I.1', 'I.2', 'referensi', 'Dokumen kebijakan saling terkait dengan perencanaan.'],
    ['I.1', 'II.1', 'kunci', 'Warning: Jika kebijakan tidak ada, perencanaan di II.1 pasti terdampak.'],
    ['I.2', 'I.4', 'referensi', 'Anggaran membutuhkan persetujuan dari dewan direksi.'],
    ['I.3', 'II.2.1', 'kunci', 'Kritikal: Pastikan sosialisasi tercatat dengan benar.']
];

foreach ($mustHaves as $mh) {
    if (in_array($mh[0], $kriterias) && in_array($mh[1], $kriterias)) {
        $relasi[] = [
            'kriteria_asal_kode' => $mh[0],
            'kriteria_tujuan_kode' => $mh[1],
            'jenis_relasi' => $mh[2],
            'deskripsi_keterkaitan' => $mh[3],
            'created_at' => $now, 'updated_at' => $now
        ];
    }
}

// Bulk insert
foreach (array_chunk($relasi, 200) as $chunk) {
    DB::table('kriteria_relasi')->insertOrIgnore($chunk);
}

echo "Berhasil generate " . count($relasi) . " relasi acak di seluruh elemen." . PHP_EOL;
