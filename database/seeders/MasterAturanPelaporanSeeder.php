<?php

namespace Database\Seeders;

use App\Models\MasterAturanPelaporan;
use Illuminate\Database\Seeder;

class MasterAturanPelaporanSeeder extends Seeder
{
    /**
     * Seed aturan tenggat pelaporan awal.
     * Nilai 30 hari setelah akhir triwulan IV berasal dari materi pelatihan 2021
     * dan ditandai is_verified = false agar diverifikasi admin terhadap regulasi terbaru.
     */
    public function run(): void
    {
        MasterAturanPelaporan::firstOrCreate(
            ['kode' => 'TENGGAT_TAHUNAN_SMKP'],
            [
                'nama_aturan'                => 'Tenggat Penyampaian Laporan Audit Internal SMKP Tahunan',
                'hari_setelah_akhir_periode' => 30,
                'keterangan'                 => 'Nilai awal: 30 hari kalender setelah berakhirnya triwulan IV (31 Desember tahun periode audit) merujuk materi pelatihan 2021. Wajib diverifikasi dan disesuaikan oleh Administrator sesuai peraturan perundang-undangan terbaru yang berlaku.',
                'is_active'                  => true,
                'is_verified'                => false,
            ]
        );
    }
}
