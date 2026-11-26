<?php

namespace Database\Seeders;

use App\Models\MasterDasarHukum;
use Illuminate\Database\Seeder;

class MasterDasarHukumSeeder extends Seeder
{
    /**
     * Seed data awal dasar hukum yang dikutip materi pelatihan.
     * Seluruh entri ditandai is_verified = false agar diverifikasi ulang oleh Administrator
     * terhadap regulasi terbaru yang berlaku.
     */
    public function run(): void
    {
        $items = [
            [
                'kode'            => 'PERMEN_ESDM_26_2018',
                'nomor_peraturan' => 'Peraturan Menteri ESDM Nomor 26 Tahun 2018',
                'judul'           => 'Pelaksanaan Kaidah Pertambangan yang Baik dan Pengawasan Pertambangan Mineral dan Batubara',
                'ringkasan'       => 'Kewajiban penerapan dan audit internal Sistem Manajemen Keselamatan Pertambangan (SMKP) Minerba.',
                'urutan'          => 1,
            ],
            [
                'kode'            => 'KEPMEN_ESDM_1827_2018',
                'nomor_peraturan' => 'Keputusan Menteri ESDM Nomor 1827.K/30/MEM/2018',
                'judul'           => 'Pedoman Pelaksanaan Kaidah Teknik Pertambangan yang Baik',
                'ringkasan'       => 'Lampiran IV memuat pedoman penerapan SMKP Mineral dan Batubara (verifikasi nomor peraturan oleh admin).',
                'urutan'          => 2,
            ],
            [
                'kode'            => 'KEPDIRJEN_185_2019',
                'nomor_peraturan' => 'Keputusan Direktur Jenderal Minerba Nomor 185.K/37.04/DJB/2019',
                'judul'           => 'Petunjuk Teknis Pelaksanaan Keselamatan Pertambangan dan Pelaksanaan, Penilaian, dan Pelaporan SMKP Minerba',
                'ringkasan'       => 'Lampiran II mengatur petunjuk teknis pelaksanaan, penilaian, dan pelaporan audit internal SMKP Minerba.',
                'urutan'          => 3,
            ],
            [
                'kode'            => 'PERMEN_ESDM_17_2025',
                'nomor_peraturan' => 'Peraturan Menteri ESDM Nomor 17 Tahun 2025',
                'judul'           => 'Tata Cara Penyusunan, Penyampaian, dan Persetujuan RKAB serta Pelaporan Pelaksanaan Kegiatan Usaha Pertambangan Mineral dan Batubara',
                'ringkasan'       => 'Pengaturan pelaporan kegiatan usaha pertambangan terkini (menggantikan ketentuan pelaporan terdahulu; wajib diverifikasi oleh admin).',
                'urutan'          => 4,
            ],
        ];

        foreach ($items as $item) {
            MasterDasarHukum::firstOrCreate(
                ['kode' => $item['kode']],
                array_merge($item, [
                    'is_active'   => true,
                    'is_verified' => false,
                ])
            );
        }
    }
}
