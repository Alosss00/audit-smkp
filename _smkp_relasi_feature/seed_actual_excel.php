<?php
require 'C:/laragon/www/audit/vendor/autoload.php';
$app = require_once 'C:/laragon/www/audit/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = "C:/laragon/www/audit/Matriks_Hubungan_Antar_Elemen_SMKP.xlsx";
if (!file_exists($file)) die("File not found!");

$spreadsheet = IOFactory::load($file);
$now = now()->toDateTimeString();
$relasi = [];

echo "Membersihkan database..." . PHP_EOL;
DB::table('kriteria_relasi')->truncate();

// 1. Matriks Keterkaitan (Referensi)
$sheetRef = $spreadsheet->getSheetByName('Matriks Keterkaitan');
if ($sheetRef) {
    $rows = $sheetRef->toArray();
    foreach ($rows as $index => $row) {
        if ($index == 0) continue; // Skip header
        
        $asal = trim($row[1] ?? ''); // Col B
        $tujuans = trim($row[3] ?? ''); // Col D
        $deskripsi = trim($row[4] ?? ''); // Col E
        
        if (empty($asal) || empty($tujuans)) continue;
        
        $tujuanList = array_map('trim', explode(';', $tujuans));
        foreach ($tujuanList as $tujuan) {
            if (!empty($tujuan)) {
                $relasi[] = [
                    'kriteria_asal_kode' => $asal,
                    'kriteria_tujuan_kode' => $tujuan,
                    'jenis_relasi' => 'referensi',
                    'deskripsi_keterkaitan' => $deskripsi,
                    'created_at' => $now, 'updated_at' => $now
                ];
            }
        }
    }
}

// 2. Hubungan Kunci (Kunci)
$sheetKunci = $spreadsheet->getSheetByName('Hubungan Kunci');
if ($sheetKunci) {
    $rows = $sheetKunci->toArray();
    foreach ($rows as $index => $row) {
        if ($index == 0) continue; // Skip header
        
        $asal = trim($row[0] ?? ''); // Col A
        $tujuans = trim($row[1] ?? ''); // Col B
        $deskripsi = trim($row[2] ?? ''); // Col C
        
        if (empty($asal) || empty($tujuans)) continue;
        
        $tujuanList = array_map('trim', explode(';', $tujuans));
        foreach ($tujuanList as $tujuan) {
            if (!empty($tujuan)) {
                $relasi[] = [
                    'kriteria_asal_kode' => $asal,
                    'kriteria_tujuan_kode' => $tujuan,
                    'jenis_relasi' => 'kunci',
                    'deskripsi_keterkaitan' => $deskripsi,
                    'created_at' => $now, 'updated_at' => $now
                ];
            }
        }
    }
}

$inserted = 0;
foreach (array_chunk($relasi, 100) as $chunk) {
    $inserted += DB::table('kriteria_relasi')->insertOrIgnore($chunk);
}

// Check real count in DB
$countKunci = DB::table('kriteria_relasi')->where('jenis_relasi', 'kunci')->count();
$countRef = DB::table('kriteria_relasi')->where('jenis_relasi', 'referensi')->count();

echo "SELESAI!" . PHP_EOL;
echo "Total Relasi Kunci      : $countKunci" . PHP_EOL;
echo "Total Relasi Referensi  : $countRef" . PHP_EOL;
