<?php

if (php_sapi_name() !== 'cli' || (function_exists('app') && app()->isProduction())) {
    http_response_code(403);
    exit('Forbidden: Script hanya dapat dijalankan melalui CLI di environment non-production.');
}

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::table('kriteria_relasi')->truncate();

$now = now()->toDateTimeString();

DB::table('kriteria_relasi')->insert([
    // Contoh Referensi (Pasif) untuk I.1
    [
        'kriteria_asal_kode' => 'I.1',
        'kriteria_tujuan_kode' => 'I.2',
        'jenis_relasi' => 'referensi',
        'deskripsi_keterkaitan' => 'Kebijakan harus diturunkan menjadi perencanaan.',
        'created_at' => $now, 'updated_at' => $now
    ],
    [
        'kriteria_asal_kode' => 'I.1',
        'kriteria_tujuan_kode' => 'I.3',
        'jenis_relasi' => 'referensi',
        'deskripsi_keterkaitan' => 'Kebijakan harus dikomunikasikan ke seluruh pekerja.',
        'created_at' => $now, 'updated_at' => $now
    ],
    [
        'kriteria_asal_kode' => 'I.1',
        'kriteria_tujuan_kode' => 'II.1',
        'jenis_relasi' => 'referensi',
        'deskripsi_keterkaitan' => 'Komitmen dari manajemen puncak.',
        'created_at' => $now, 'updated_at' => $now
    ],
    
    // Contoh Kunci (Aktif - Banner Warning) untuk I.1
    [
        'kriteria_asal_kode' => 'I.1',
        'kriteria_tujuan_kode' => 'II.2.1',
        'jenis_relasi' => 'kunci',
        'deskripsi_keterkaitan' => 'Kritikal: Jika Kebijakan belum disahkan (temuan), periksa konsistensi Dokumen Sasaran dan Program di elemen II.2.1.',
        'created_at' => $now, 'updated_at' => $now
    ],

    // Contoh Kunci untuk I.2
    [
        'kriteria_asal_kode' => 'I.2',
        'kriteria_tujuan_kode' => 'II.2.2',
        'jenis_relasi' => 'kunci',
        'deskripsi_keterkaitan' => 'Periksa apakah perencanaan terintegrasi dengan RKAB.',
        'created_at' => $now, 'updated_at' => $now
    ],
]);

echo "Data sampel dummy berhasil di-seed (5 relasi)." . PHP_EOL;
