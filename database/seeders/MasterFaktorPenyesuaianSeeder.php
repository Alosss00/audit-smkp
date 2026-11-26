<?php

namespace Database\Seeders;

use App\Models\MasterFaktorPenyesuaian;
use Illuminate\Database\Seeder;

class MasterFaktorPenyesuaianSeeder extends Seeder
{
    /**
     * Seed standard adjustment factor definitions without unverified numeric values.
     * Admin must fill nilai_hari after verifying against Kepdirjen 185 Lampiran II.
     */
    public function run(): void
    {
        $items = [
            [
                'kode'       => 'JARAK_OBJEK_4_JAM',
                'nama'       => 'Jarak antar objek audit saling berjauhan dengan waktu tempuh ≥ 4 jam',
                'tipe'       => 'tambah',
                'urutan'     => 1,
                'keterangan' => 'Faktor penambah hari kerja audit. Isi nilai_hari sesuai dokumen acuan resmi.',
            ],
            [
                'kode'       => 'LEBIH_SATU_METODE_TAMBANG',
                'nama'       => 'Perusahaan pertambangan menggunakan lebih dari satu metode penambangan',
                'tipe'       => 'tambah',
                'urutan'     => 2,
                'keterangan' => 'Faktor penambah hari kerja audit. Isi nilai_hari sesuai dokumen acuan resmi.',
            ],
            [
                'kode'       => 'FASILITAS_PENGOLAHAN',
                'nama'       => 'Perusahaan pertambangan memiliki fasilitas pengolahan dan/atau pemurnian',
                'tipe'       => 'tambah',
                'urutan'     => 3,
                'keterangan' => 'Faktor penambah hari kerja audit. Isi nilai_hari sesuai dokumen acuan resmi.',
            ],
            [
                'kode'       => 'SR_FR_DIATAS_NASIONAL',
                'nama'       => 'Severity rate atau frequency rate kecelakaan tahun terakhir di atas rata-rata nasional',
                'tipe'       => 'tambah',
                'urutan'     => 4,
                'keterangan' => 'Faktor penambah hari kerja audit. Isi nilai_hari sesuai dokumen acuan resmi.',
            ],
            [
                'kode'       => 'KEJADIAN_BERBAHAYA_BERULANG',
                'nama'       => 'Terjadi kejadian berbahaya serupa dan berulang dalam satu tahun terakhir',
                'tipe'       => 'tambah',
                'urutan'     => 5,
                'keterangan' => 'Faktor penambah hari kerja audit. Isi nilai_hari sesuai dokumen acuan resmi.',
            ],
            [
                'kode'       => 'PENYAKIT_AKIBAT_KERJA',
                'nama'       => 'Terjadi kejadian akibat penyakit tenaga kerja dan/atau penyakit akibat kerja',
                'tipe'       => 'tambah',
                'urutan'     => 6,
                'keterangan' => 'Faktor penambah hari kerja audit. Isi nilai_hari sesuai dokumen acuan resmi.',
            ],
            [
                'kode'       => 'TERSERTIFIKASI_SMKP_LS',
                'nama'       => 'Perusahaan sudah tersertifikasi SMKP oleh Lembaga Sertifikasi yang ditunjuk Dirjen',
                'tipe'       => 'kurang',
                'urutan'     => 7,
                'keterangan' => 'Faktor pengurang hari kerja audit. Isi nilai_hari sesuai dokumen acuan resmi.',
            ],
        ];

        foreach ($items as $item) {
            MasterFaktorPenyesuaian::updateOrCreate(
                ['kode' => $item['kode']],
                [
                    'nama'       => $item['nama'],
                    'tipe'       => $item['tipe'],
                    'nilai_hari' => null,
                    'urutan'     => $item['urutan'],
                    'is_active'  => true,
                    'keterangan' => $item['keterangan'],
                ]
            );
        }
    }
}
