<?php

namespace Database\Seeders;

use App\Models\KriteriaRelasi;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

/**
 * KriteriaRelasiSeeder
 *
 * Membaca file Excel Matriks_Hubungan_Antar_Elemen_SMKP.xlsx dan
 * melakukan seeding ke tabel kriteria_relasi.
 *
 * CARA PENGGUNAAN:
 *   php artisan db:seed --class=KriteriaRelasiSeeder
 *
 * STRUKTUR EXCEL YANG DIASUMSIKAN:
 *   Sheet 1: "Hubungan Kunci"
 *     - Kolom A: Kode Kriteria Asal  (e.g. "I.1.1")
 *     - Kolom B: Kode Kriteria Tujuan (e.g. "II.4.2")
 *     - Kolom C: Deskripsi Keterkaitan (narasi sebab-akibat)
 *     - Baris 1: Header, data mulai baris 2. Total ~20 baris data.
 *
 *   Sheet 2: "Matriks Keterkaitan"
 *     - Kolom A: Kode Kriteria Asal
 *     - Kolom B: Kode Kriteria Tujuan
 *     - Kolom C: Deskripsi Keterkaitan (nullable)
 *     - Baris 1: Header, data mulai baris 2. Total ~699 baris data.
 *     - (Kolom D "Fokus Bukti Audit" DIABAIKAN - datanya repetitif)
 *
 * PENEMPATAN FILE EXCEL:
 *   Letakkan file di: storage/app/seeder/Matriks_Hubungan_Antar_Elemen_SMKP.xlsx
 */
class KriteriaRelasiSeeder extends Seeder
{
    /** Path relatif dari storage_path('app/') */
    private const EXCEL_PATH = 'seeder/Matriks_Hubungan_Antar_Elemen_SMKP.xlsx';

    /** Nama sheet di dalam file Excel */
    private const SHEET_KUNCI      = 'Hubungan Kunci';
    private const SHEET_REFERENSI  = 'Matriks Keterkaitan';

    public function run(): void
    {
        $filePath = storage_path('app/' . self::EXCEL_PATH);

        if (!file_exists($filePath)) {
            $this->command->error(
                "File Excel tidak ditemukan: {$filePath}\n" .
                "Letakkan file di: storage/app/seeder/Matriks_Hubungan_Antar_Elemen_SMKP.xlsx"
            );
            return;
        }

        $this->command->info('Membersihkan data relasi lama...');
        KriteriaRelasi::truncate();

        // -- Proses Sheet 1: Hubungan Kunci -------------------------------------
        $this->command->info('Memproses sheet "' . self::SHEET_KUNCI . '"...');
        $jumlahKunci = $this->seedFromSheet($filePath, self::SHEET_KUNCI, 'kunci');
        $this->command->info("  -> {$jumlahKunci} relasi KUNCI berhasil di-seed.");

        // -- Proses Sheet 2: Matriks Keterkaitan --------------------------------
        $this->command->info('Memproses sheet "' . self::SHEET_REFERENSI . '"...');
        $jumlahReferensi = $this->seedFromSheet($filePath, self::SHEET_REFERENSI, 'referensi');
        $this->command->info("  -> {$jumlahReferensi} relasi REFERENSI berhasil di-seed.");

        $this->command->line('');
        $this->command->info("Seeding selesai. Total: " . ($jumlahKunci + $jumlahReferensi) . " relasi.");
    }

    /**
     * Baca satu sheet dari file Excel dan insert batchnya ke database.
     *
     * @param  string $filePath  Path absolut ke file Excel
     * @param  string $sheetName Nama sheet yang dibaca
     * @param  string $jenis     'kunci' atau 'referensi'
     * @return int               Jumlah baris yang berhasil diinsert
     */
    private function seedFromSheet(string $filePath, string $sheetName, string $jenis): int
    {
        // Load sheet tertentu menggunakan PhpSpreadsheet via Maatwebsite/Excel
        $rows = Excel::toCollection(null, $filePath, null, \Maatwebsite\Excel\Excel::XLSX)
            ->get($sheetName);

        if (!$rows) {
            $this->command->warn("  Sheet '{$sheetName}' tidak ditemukan, dilewati.");
            return 0;
        }

        $batch   = [];
        $skipped = 0;
        $now     = now()->toDateTimeString();

        // Baris pertama (index 0) adalah header, mulai dari index 1
        foreach ($rows->slice(1) as $row) {
            $asal   = trim((string) ($row[0] ?? ''));
            $tujuan = trim((string) ($row[1] ?? ''));
            $desc   = trim((string) ($row[2] ?? ''));

            // Skip baris kosong
            if (empty($asal) || empty($tujuan)) {
                $skipped++;
                continue;
            }

            // Skip relasi ke diri sendiri (self-loop)
            if ($asal === $tujuan) {
                $skipped++;
                continue;
            }

            $batch[] = [
                'kriteria_asal_kode'    => $asal,
                'kriteria_tujuan_kode'  => $tujuan,
                'jenis_relasi'          => $jenis,
                'deskripsi_keterkaitan' => $desc ?: null,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
        }

        if ($skipped > 0) {
            $this->command->warn("  {$skipped} baris dilewati (kosong atau self-loop).");
        }

        // Chunk insert untuk efisiensi memori (batch per 200 baris)
        $inserted = 0;
        foreach (array_chunk($batch, 200) as $chunk) {
            KriteriaRelasi::upsert(
                $chunk,
                ['kriteria_asal_kode', 'kriteria_tujuan_kode', 'jenis_relasi'], // unique keys
                ['deskripsi_keterkaitan', 'updated_at']                          // update jika duplicate
            );
            $inserted += count($chunk);
        }

        return $inserted;
    }
}
