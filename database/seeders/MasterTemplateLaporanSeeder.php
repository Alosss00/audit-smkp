<?php

namespace Database\Seeders;

use App\Models\MasterTemplateLaporan;
use Illuminate\Database\Seeder;

class MasterTemplateLaporanSeeder extends Seeder
{
    /**
     * Seed template narasi awal untuk bagian-bagian Laporan Audit Internal SMKP.
     * Seluruh entri ditandai is_verified = false agar ditinjau & diverifikasi oleh Administrator.
     */
    public function run(): void
    {
        $items = [
            [
                'bagian'       => 'latar_belakang',
                'judul'        => 'Bab 1 — Latar Belakang',
                'isi_template' => "Audit SMKP Minerba ini merupakan bagian dari penerapan Elemen SMKP Minerba sesuai dengan ketentuan dalam Peraturan Menteri Energi dan Sumber Daya Mineral Nomor 26 Tahun 2018 dan Keputusan Menteri Energi dan Sumber Daya Mineral Nomor 1827.K/30/MEM/2018 Lampiran IV serta Keputusan Dirjen Mineral dan Batubara Nomor 185.K/37.04/DJB/2019 tentang Petunjuk Teknis Pelaksanaan, Penilaian, dan Pelaporan SMKP Minerba.\n\nAudit SMKP Minerba ini bertujuan untuk mendapatkan gambaran tingkat penerapan SMKP Minerba di {{nama_perusahaan}} sejak periode 1 Januari {{tahun_periode}} sampai dengan periode 31 Desember {{tahun_periode}}.",
            ],
            [
                'bagian'       => 'tujuan_audit',
                'judul'        => 'Tujuan Pelaksanaan Audit Internal',
                'isi_template' => "Audit Internal SMKP pada {{nama_perusahaan}} periode tahun {{tahun_periode}} dilaksanakan dengan tujuan untuk memperoleh gambaran objektif mengenai tingkat penerapan SMKP Minerba, mengidentifikasi praktik terbaik dan ketidaksesuaian, serta menetapkan rencana tindak lanjut perbaikan berkelanjutan.",
            ],
            [
                'bagian'       => 'penutup',
                'judul'        => 'Bab Penutup',
                'isi_template' => "Demikian Laporan Audit Internal SMKP Minerba {{nama_perusahaan}} periode tahun {{tahun_periode}} dengan tingkat pencapaian sebesar {{skor_akhir}}% (tercatat {{total_temuan}} temuan ketidaksesuaian: {{jumlah_temuan_kritikal}} Kritikal, {{jumlah_temuan_mayor}} Mayor, dan {{jumlah_temuan_minor}} Minor) ini disusun sebagai bahan evaluasi manajemen dan pemenuhan kewajiban pelaporan. Diterbitkan di {{tempat_penerbitan}} pada tanggal {{tanggal_laporan}} oleh Tim Audit yang dipimpin oleh {{nama_lead_auditor}}.",
            ],
            [
                'bagian'       => 'respon_manajemen',
                'judul'        => 'Template Respon Manajemen terhadap Hasil Audit',
                'isi_template' => "Manajemen {{nama_perusahaan}} telah menerima Laporan Audit Internal SMKP Nomor {{nomor_laporan}} periode tahun {{tahun_periode}} dengan tingkat pencapaian {{skor_akhir}}% serta berkomitmen menindaklanjuti seluruh temuan ketidaksesuaian sesuai rencana tindak lanjut yang telah disepakati bersama Tim Audit.",
            ],
        ];

        foreach ($items as $item) {
            MasterTemplateLaporan::firstOrCreate(
                ['bagian' => $item['bagian']],
                array_merge($item, [
                    'is_active'   => true,
                    'is_verified' => false,
                ])
            );
        }
    }
}
