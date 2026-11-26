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
                'isi_template' => "Dalam rangka memenuhi ketentuan peraturan perundang-undangan di bidang keselamatan pertambangan mineral dan batubara serta untuk mengevaluasi tingkat pencapaian penerapan Sistem Manajemen Keselamatan Pertambangan (SMKP) Minerba, {{nama_perusahaan}} telah melaksanakan Audit Internal SMKP periode tahun {{tahun_periode}} pada tanggal {{tanggal_mulai}} sampai dengan {{tanggal_selesai}}.\n\nPelaksanaan dan pelaporan audit internal ini mengacu pada dasar hukum yang berlaku sebagai berikut:\n{{daftar_dasar_hukum}}",
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
