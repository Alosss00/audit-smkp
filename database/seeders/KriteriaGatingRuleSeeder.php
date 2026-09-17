<?php

namespace Database\Seeders;

use App\Models\KriteriaGatingRule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaGatingRuleSeeder extends Seeder
{
    /**
     * Run the database seeds for 8 Cross-Check / Scoring Gating Logic Nodes.
     *
     * CATATAN PENTING:
     * - Kolom `kriteria_hulu_id` dan `kriteria_hilir_id` sengaja diset NULL
     *   sebagai placeholder (TODO) agar pemetaan (mapping) ID kriteria aktual
     *   dapat diverifikasi dan dikonfirmasi langsung sebelum dieksekusi.
     */
    public function run(): void
    {
        // Helper helper closure to get Kriteria ID by kode_kriteria
        $getId = fn(string $kode) => \App\Models\Kriteria::where('kode_kriteria', $kode)->value('id');

        $rules = [
            // Simpul 1: I.2 (Isi Kebijakan) -> II.4 (Tujuan, Sasaran, & Program)
            [
                'kriteria_hulu_id'  => $getId('I.2'),
                'kriteria_hilir_id' => $getId('II.4'),
                'ambang_hulu'       => 2.00,
                'skor_maks_hilir'   => 2.00,
                'mode'              => 'hard_block',
                'deskripsi_simpul'  => 'Simpul 1: Sub-Elemen I.2 (Isi Kebijakan) -> II.4 (Tujuan, Sasaran, dan Program). Jika skor I.2 <= 2 maka batas maksimal skor II.4 adalah 2.',
                'is_active'         => true,
            ],

            // Simpul 2a: II.2.4 (IBPR/HIRADC) -> IV.1.1 (SOP/JSA/Izin Kerja)
            [
                'kriteria_hulu_id'  => $getId('II.2.4'),
                'kriteria_hilir_id' => $getId('IV.1.1'),
                'ambang_hulu'       => 2.00,
                'skor_maks_hilir'   => 2.00,
                'mode'              => 'hard_block',
                'deskripsi_simpul'  => 'Simpul 2a: Sub-Elemen II.2.4 (IBPR/HIRADC) -> IV.1.1 (SOP/JSA). Jika skor II.2.4 <= 2 maka batas maksimal skor IV.1.1 adalah 2.',
                'is_active'         => true,
            ],

            // Simpul 2b: II.2.4 (IBPR/HIRADC) -> IV.1.2 (Izin Kerja Khusus)
            [
                'kriteria_hulu_id'  => $getId('II.2.4'),
                'kriteria_hilir_id' => $getId('IV.1.2'),
                'ambang_hulu'       => 2.00,
                'skor_maks_hilir'   => 2.00,
                'mode'              => 'hard_block',
                'deskripsi_simpul'  => 'Simpul 2b: Sub-Elemen II.2.4 (IBPR/HIRADC) -> IV.1.2 (Izin Kerja Khusus). Jika skor II.2.4 <= 2 maka batas maksimal skor IV.1.2 adalah 2.',
                'is_active'         => true,
            ],

            // Simpul 3: II.3 (Register Regulasi) -> V.3 (Evaluasi Kepatuhan Hukum)
            [
                'kriteria_hulu_id'  => $getId('II.3'),
                'kriteria_hilir_id' => $getId('V.3'),
                'ambang_hulu'       => 2.00,
                'skor_maks_hilir'   => 2.00,
                'mode'              => 'hard_block',
                'deskripsi_simpul'  => 'Simpul 3: Sub-Elemen II.3 (Register Regulasi) -> V.3 (Evaluasi Kepatuhan Hukum). Jika skor II.3 <= 2 maka batas maksimal skor V.3 adalah 2.',
                'is_active'         => true,
            ],

            // Simpul 4: II.5 (RKAB KP) -> V.1.1 (Pemantauan Sasaran/Program)
            [
                'kriteria_hulu_id'  => $getId('II.5'),
                'kriteria_hilir_id' => $getId('V.1.1'),
                'ambang_hulu'       => null,
                'skor_maks_hilir'   => null,
                'mode'              => 'soft_flag',
                'deskripsi_simpul'  => 'Simpul 4: Sub-Elemen II.5 (RKAB KP) -> V.1.1 (Pemantauan Sasaran/Program). Peringatan inkonsistensi sistem (soft-flag).',
                'is_active'         => true,
            ],

            // Simpul 5a: III.5 (Legalitas Pengawas Operasional/Teknik) -> IV.4.3 (Kelayakan Sarana/Unit/Peralatan)
            [
                'kriteria_hulu_id'  => $getId('III.5'),
                'kriteria_hilir_id' => $getId('IV.4.3'),
                'ambang_hulu'       => 2.00,
                'skor_maks_hilir'   => 2.00,
                'mode'              => 'hard_block',
                'deskripsi_simpul'  => 'Simpul 5a: Sub-Elemen III.5 (Pengawas Operasional/Teknik) -> IV.4.3 (Kelayakan Sarana/Unit). Jika skor III.5 <= 2 maka skor IV.4.3 maksimal 2.',
                'is_active'         => true,
            ],

            // Simpul 5b: III.6 (Tenaga Teknik Khusus) -> IV.4.4 (Kompetensi Tenaga Teknik)
            [
                'kriteria_hulu_id'  => $getId('III.6'),
                'kriteria_hilir_id' => $getId('IV.4.4'),
                'ambang_hulu'       => 2.00,
                'skor_maks_hilir'   => 2.00,
                'mode'              => 'hard_block',
                'deskripsi_simpul'  => 'Simpul 5b: Sub-Elemen III.6 (Tenaga Teknik Khusus) -> IV.4.4 (Kompetensi Tenaga Teknik). Jika skor III.6 <= 2 maka skor IV.4.4 maksimal 2.',
                'is_active'         => true,
            ],

            // Simpul 6: III.8 (Tim Tanggap Darurat) -> IV.9 (Pengelolaan Keadaan Darurat)
            [
                'kriteria_hulu_id'  => $getId('III.8'),
                'kriteria_hilir_id' => $getId('IV.9'),
                'ambang_hulu'       => null,
                'skor_maks_hilir'   => null,
                'mode'              => 'soft_flag',
                'deskripsi_simpul'  => 'Simpul 6: Sub-Elemen III.8 (Tim Tanggap Darurat) -> IV.9 (Pengelolaan Keadaan Darurat). Peringatan kesiapan tim tanggap darurat (soft-flag).',
                'is_active'         => true,
            ],

            // Simpul 7: Elemen IV.1.1 (Pelaksanaan KP) -> V.2 (Inspeksi KP)
            [
                'kriteria_hulu_id'  => $getId('IV.1.1'),
                'kriteria_hilir_id' => $getId('V.2'),
                'ambang_hulu'       => null,
                'skor_maks_hilir'   => null,
                'mode'              => 'soft_flag',
                'deskripsi_simpul'  => 'Simpul 7: Kualitatif Elemen IV (Pelaksanaan Keselamatan Pertambangan) -> V.2 (Inspeksi KP). Peringatan inkonsistensi pelaksanaan vs temuan inspeksi (soft-flag).',
                'is_active'         => true,
            ],

            // Simpul 8a: V.6 (Audit Internal) -> VII.2 (Catatan Tinjauan Manajemen)
            [
                'kriteria_hulu_id'  => $getId('V.6'),
                'kriteria_hilir_id' => $getId('VII.2'),
                'ambang_hulu'       => 1.00,
                'skor_maks_hilir'   => 1.00,
                'mode'              => 'hard_block',
                'deskripsi_simpul'  => 'Simpul 8a: Sub-Elemen V.6 (Audit Internal) -> VII.2 (Catatan Tinjauan Manajemen). Jika skor V.6 <= 1 maka skor VII.2 maksimal 1.',
                'is_active'         => true,
            ],

            // Simpul 8b: V.7 (Tindak Lanjut Audit) -> VII.3 (Keluaran Tinjauan Manajemen)
            [
                'kriteria_hulu_id'  => $getId('V.7'),
                'kriteria_hilir_id' => $getId('VII.3'),
                'ambang_hulu'       => 1.00,
                'skor_maks_hilir'   => 1.00,
                'mode'              => 'hard_block',
                'deskripsi_simpul'  => 'Simpul 8b: Sub-Elemen V.7 (Tindak Lanjut Audit) -> VII.3 (Keluaran Tinjauan Manajemen). Jika skor V.7 <= 1 maka skor VII.3 maksimal 1.',
                'is_active'         => true,
            ],
        ];

        foreach ($rules as $rule) {
            KriteriaGatingRule::updateOrCreate(
                ['deskripsi_simpul' => $rule['deskripsi_simpul']],
                $rule
            );
        }
    }
}
