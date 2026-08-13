<?php

namespace Database\Seeders;

use App\Models\AuditDetail;
use App\Models\AuditSesi;
use App\Models\Kriteria;
use App\Models\Pica;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleAuditSeeder extends Seeder
{
    /**
     * Run the database seeds for realistic sample audit data & PICA entries.
     */
    public function run(): void
    {
        $auditor = User::where('role', 'auditor')->first();
        if (!$auditor) {
            return;
        }

        $kriterias = Kriteria::all();
        if ($kriterias->isEmpty()) {
            return;
        }

        // ==========================================
        // 1. Sesi Audit 1: Operational Area Pit West (Status: berjalan)
        // ==========================================
        $sesi1 = AuditSesi::create([
            'user_id'         => $admin->id,
            'tanggal_mulai'   => now()->subDays(5)->toDateString(),
            'tanggal_selesai' => now()->subDays(3)->toDateString(),
            'area_audit'      => 'Area Tambang Utama Pit West & Haulage Road',
            'status'          => 'berjalan',
            'skor_akhir'      => 0.00,
        ]);

        foreach ($kriterias as $index => $kriteria) {
            $max = (float) $kriteria->nilai_maksimal;
            $nilai = $max;
            $catatan = null;
            $isNa = false;

            // Introduce realistic non-conformities on specific criteria indices
            if ($index === 0) {
                // I.1.1 Policy signed & updated
                $nilai = $max;
                $catatan = 'Kebijakan Keselamatan Pertambangan tertulis, ditandatangani KTT, dan dipasang di seluruh papan pengumuman pit.';
            } elseif ($index === 2) {
                // I.2.1 Review of policy
                $nilai = 2.00;
                $catatan = 'Kebijakan KP belum dievaluasi secara berkala dalam 12 bulan terakhir.';
            } elseif ($index === 5) {
                // II.1.2 IBPR Risk Matrix
                $nilai = 3.00;
                $catatan = 'Dokumen IBPR area lereng barat belum mencakup analisis risiko longsoran musim hujan.';
            } elseif ($index === 8) {
                // III.1.1 KTT Certificate
                $nilai = 2.00;
                $catatan = 'Sertifikat kompetensi POP untuk 2 supervisor tambang telah kadaluarsa dan dalam proses perpanjangan.';
            } elseif ($index === 12) {
                // IV.2.1 Explosive Storage
                $nilai = 0.00;
                $catatan = 'Rambu peringatan bahaya di area penimbunan bahan peledak sementara mengalami kerusakan fisik.';
            }

            $detail = AuditDetail::create([
                'audit_sesi_id' => $sesi1->id,
                'kriteria_id' => $kriteria->id,
                'nilai' => $nilai,
                'is_na' => $isNa,
                'catatan' => $catatan,
            ]);

            // Auto-generate PICA for non-conformities
            if ($nilai < $max && !$isNa) {
                if ($index === 2) {
                    Pica::create([
                        'audit_detail_id' => $detail->id,
                        'deskripsi_temuan' => $catatan,
                        'akar_masalah' => 'Jadwal tinjauan tahunan Kebijakan KP terlewat karena adanya restrukturisasi organisasi K3PL.',
                        'tindakan_koreksi' => 'Melaksanakan rapat evaluasi Kebijakan KP bersama KTT dan seluruh Kepala Bagian.',
                        'tindakan_pencegahan' => 'Menetapkan kalender pengingat otomatis di portal HSE setiap bulan Januari.',
                        'tenggat_waktu' => now()->addDays(14)->toDateString(),
                        'status' => 'in_progress',
                    ]);
                } elseif ($index === 5) {
                    Pica::create([
                        'audit_detail_id' => $detail->id,
                        'deskripsi_temuan' => $catatan,
                        'akar_masalah' => 'Kajian geoteknik lereng barat baru selesai minggu lalu dan belum diintegrasikan ke IBPR.',
                        'tindakan_koreksi' => 'Revisi dokumen IBPR lereng barat sesuai rekomendasi geoteknik terbaru.',
                        'tindakan_pencegahan' => 'SOP Wajib pemutakhiran IBPR setiap ada laporan kajian geoteknik baru.',
                        'tenggat_waktu' => now()->addDays(5)->toDateString(),
                        'status' => 'closed',
                        'catatan_verifikasi_auditor' => 'Telah diverifikasi: Dokumen IBPR lereng barat revisi 02 telah disahkan KTT pada ' . now()->subDay()->format('d M Y'),
                    ]);
                } else {
                    Pica::create([
                        'audit_detail_id' => $detail->id,
                        'deskripsi_temuan' => $catatan,
                        'status' => 'open',
                    ]);
                }
            }
        }

        $sesi1->hitungSkorAkhir();

        // ==========================================
        // 2. Sesi Audit 2: Processing Plant & Workshop (Status: selesai)
        // ==========================================
        $sesi2 = AuditSesi::create([
            'user_id'         => $admin->id,
            'tanggal_mulai'   => now()->subDays(12)->toDateString(),
            'tanggal_selesai' => now()->subDays(10)->toDateString(),
            'area_audit'      => 'Processing Plant & Heavy Equipment Workshop',
            'status'          => 'selesai',
            'skor_akhir'      => 0.00,
        ]);

        foreach ($kriterias as $index => $kriteria) {
            $max = (float) $kriteria->nilai_maksimal;
            $nilai = $max;
            $catatan = null;

            if ($index === 3) {
                $nilai = 3.00;
                $catatan = 'Pemeriksaan harian (P2H) alat berat di workshop belum 100% diarsip digital.';
            }

            $detail = AuditDetail::create([
                'audit_sesi_id' => $sesi2->id,
                'kriteria_id' => $kriteria->id,
                'nilai' => $nilai,
                'is_na' => false,
                'catatan' => $catatan,
            ]);

            if ($nilai < $max) {
                Pica::create([
                    'audit_detail_id' => $detail->id,
                    'deskripsi_temuan' => $catatan,
                    'akar_masalah' => 'Form P2H fisik terlambat di-input oleh admin shift malam.',
                    'tindakan_koreksi' => 'Penerapan aplikasi checklist P2H berbasis tablet di area workshop.',
                    'tindakan_pencegahan' => 'Pengadaan 5 unit tablet tangguh (rugged tablet) untuk mekanik P2H.',
                    'tenggat_waktu' => now()->subDays(2)->toDateString(),
                    'pic_perbaikan' => 'Eko Prasetyo (Plant Superintendent)',
                    'status' => 'closed',
                    'catatan_verifikasi_auditor' => 'Verifikasi selesai: Sistem P2H digital tablet telah beroperasi 100% di workshop.',
                ]);
            }
        }

        $sesi2->hitungSkorAkhir();
    }
}
