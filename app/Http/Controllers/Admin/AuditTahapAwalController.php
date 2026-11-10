<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AuditSesi;
use App\Models\AuditTahapAwal;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditTahapAwalController extends Controller
{
    /**
     * Show the multi-tab form for editing Berita Acara Tahap Awal Audit (TT-MGT-FRS-026L).
     */
    public function edit(int|string $id): View
    {
        $sesi = AuditSesi::with(['user', 'perusahaan'])->findOrFail($id);
        $tahapAwal = AuditTahapAwal::getOrCreateForSesi($sesi);

        $kondisiPenyesuaian = AuditTahapAwal::getDaftarKondisiPenyesuaian();
        $indikatorKelayakan = AuditTahapAwal::getDaftarIndikatorKelayakan();
        $elemenDokumentasi  = AuditTahapAwal::getDaftarElemenDokumentasi();

        return view('admin.audit-sesi.tahap-awal', compact(
            'sesi',
            'tahapAwal',
            'kondisiPenyesuaian',
            'indikatorKelayakan',
            'elemenDokumentasi'
        ));
    }

    /**
     * Update Berita Acara Tahap Awal Audit (TT-MGT-FRS-026L).
     */
    public function update(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::with('perusahaan')->findOrFail($id);
        $tahapAwal = AuditTahapAwal::getOrCreateForSesi($sesi);

        DB::beginTransaction();
        try {
            // Clean array helper for 1D text lists
            $cleanList = function ($items) {
                if (!is_array($items)) {
                    return [];
                }
                return array_values(array_filter(array_map(fn($v) => is_string($v) ? trim($v) : '', $items), fn($v) => $v !== ''));
            };

            // Clean Tim Audit array
            $cleanTim = function ($rows) {
                if (!is_array($rows)) {
                    return [];
                }
                $result = [];
                foreach ($rows as $r) {
                    $nama = trim((string) ($r['nama'] ?? ''));
                    $jabatan = trim((string) ($r['jabatan'] ?? 'Auditor'));
                    $reg = trim((string) ($r['no_registrasi'] ?? ''));
                    if ($nama !== '' || $reg !== '') {
                        $result[] = [
                            'nama'          => $nama,
                            'jabatan'       => $jabatan ?: 'Auditor',
                            'no_registrasi' => $reg,
                        ];
                    }
                }
                return $result;
            };

            // Clean Top Risks array
            $cleanRisks = function ($rows) {
                if (!is_array($rows)) {
                    return [];
                }
                $result = [];
                foreach ($rows as $r) {
                    $keg = trim((string) ($r['kegiatan'] ?? ''));
                    if ($keg !== '') {
                        $result[] = [
                            'kegiatan'   => $keg,
                            'risiko'     => trim((string) ($r['risiko'] ?? '')),
                            'nilai_awal' => trim((string) ($r['nilai_awal'] ?? '')),
                            'nilai_sisa' => trim((string) ($r['nilai_sisa'] ?? '')),
                        ];
                    }
                }
                return $result;
            };

            // Clean Unjuk Kerja Peralatan
            $cleanPeralatan = function ($rows) {
                if (!is_array($rows)) {
                    return [];
                }
                $result = [];
                foreach ($rows as $r) {
                    $jenis = trim((string) ($r['jenis_peralatan'] ?? ''));
                    if ($jenis !== '') {
                        $result[] = [
                            'jenis_peralatan' => $jenis,
                            'pa'              => trim((string) ($r['pa'] ?? '')),
                            'ma'              => trim((string) ($r['ma'] ?? '')),
                        ];
                    }
                }
                return $result;
            };

            // Clean KLI
            $cleanKli = function ($rows) {
                if (!is_array($rows)) {
                    return [];
                }
                $result = [];
                foreach ($rows as $r) {
                    $ind = trim((string) ($r['indikator'] ?? ''));
                    if ($ind !== '') {
                        $result[] = [
                            'indikator' => $ind,
                            'capaian'   => trim((string) ($r['capaian'] ?? '')),
                        ];
                    }
                }
                return $result;
            };

            $trendInput = $request->input('trend_data', []);
            $trendData = [
                'faktor_penyebab'  => $cleanList($trendInput['faktor_penyebab'] ?? []),
                'lokasi'           => $cleanList($trendInput['lokasi'] ?? []),
                'jenis_kegiatan'   => $cleanList($trendInput['jenis_kegiatan'] ?? []),
                'jenis_jabatan'    => $cleanList($trendInput['jenis_jabatan'] ?? []),
                'deviasi_inspeksi' => $cleanList($trendInput['deviasi_inspeksi'] ?? []),
                'faktor_pak'       => $cleanList($trendInput['faktor_pak'] ?? []),
            ];

            $auditPrevInput = $request->input('data_audit_sebelumnya', []);
            $dataAuditSebelumnya = [
                'tahun'            => trim((string) ($auditPrevInput['tahun'] ?? '')),
                'nilai_audit'      => trim((string) ($auditPrevInput['nilai_audit'] ?? '')),
                'status_kritikal'  => trim((string) ($auditPrevInput['status_kritikal'] ?? '')),
                'status_mayor'     => trim((string) ($auditPrevInput['status_mayor'] ?? '')),
                'status_minor'     => trim((string) ($auditPrevInput['status_minor'] ?? '')),
                'catatan_instansi' => $cleanList($auditPrevInput['catatan_instansi'] ?? []),
            ];

            $payload = [
                'no_dokumen_header'       => trim((string) $request->input('no_dokumen_header', '')) ?: null,
                'no_dokumen_footer'       => trim((string) $request->input('no_dokumen_footer', '')) ?: null,
                'no_revisi'               => trim((string) $request->input('no_revisi', '')) ?: null,
                'tanggal_terbit'          => $request->input('tanggal_terbit') ?: null,
                'tanggal_tinjau_ulang'    => $request->input('tanggal_tinjau_ulang') ?: null,
                'nama_perusahaan_auditi'  => trim((string) $request->input('nama_perusahaan_auditi', '')) ?: null,
                'jenis_perizinan'         => trim((string) $request->input('jenis_perizinan', '')) ?: null,
                'jenis_komoditas'         => trim((string) $request->input('jenis_komoditas', '')) ?: null,
                'alamat_kantor_site'      => trim((string) $request->input('alamat_kantor_site', '')) ?: null,
                'alamat_lokasi_tambang'   => trim((string) $request->input('alamat_lokasi_tambang', '')) ?: null,
                'alamat_kantor_pusat'     => trim((string) $request->input('alamat_kantor_pusat', '')) ?: null,
                'periode_audit_label'     => trim((string) $request->input('periode_audit_label', '')) ?: null,
                'kinerja_kp'              => $request->input('kinerja_kp', []),
                'faktor_penyesuaian'      => $request->input('faktor_penyesuaian', []),
                'jumlah_pekerja'          => $request->filled('jumlah_pekerja') ? (int) $request->input('jumlah_pekerja') : null,
                'kelas_risiko'            => trim((string) $request->input('kelas_risiko', '')) ?: null,
                'mandays_awal'            => $request->filled('mandays_awal') ? (float) $request->input('mandays_awal') : null,
                'faktor_penyesuaian_hari' => $request->filled('faktor_penyesuaian_hari') ? (float) $request->input('faktor_penyesuaian_hari') : null,
                'total_mandays'           => $request->filled('total_mandays') ? (float) $request->input('total_mandays') : null,
                'alokasi_tahap_1'         => $request->filled('alokasi_tahap_1') ? (float) $request->input('alokasi_tahap_1') : null,
                'alokasi_tahap_2'         => trim((string) $request->input('alokasi_tahap_2', '')) ?: null,
                'no_surat_tugas'          => trim((string) $request->input('no_surat_tugas', '')) ?: null,
                'tanggal_surat_tugas'     => $request->input('tanggal_surat_tugas') ?: null,
                'tim_audit_tahap_1'       => $cleanTim($request->input('tim_audit_tahap_1', [])),
                'kontak_awal'             => $request->input('kontak_awal', []),
                'kelayakan_audit'         => $request->input('kelayakan_audit', []),
                'kesimpulan_kelayakan'    => $request->input('kesimpulan_kelayakan') ?: null,
                'kecukupan_dokumen'       => $request->input('kecukupan_dokumen', []),
                'kesimpulan_kecukupan'    => $request->input('kesimpulan_kecukupan') ?: null,
                'tim_audit_tahap_2'       => $cleanTim($request->input('tim_audit_tahap_2', [])),
                'top_risks_present'       => $cleanRisks($request->input('top_risks_present', [])),
                'top_risks_future'        => $cleanRisks($request->input('top_risks_future', [])),
                'trend_data'              => $trendData,
                'unjuk_kerja_peralatan'   => $cleanPeralatan($request->input('unjuk_kerja_peralatan', [])),
                'capaian_kli'             => $cleanKli($request->input('capaian_kli', [])),
                'data_audit_sebelumnya'   => $dataAuditSebelumnya,
                'harapan_pemangku'        => $cleanList($request->input('harapan_pemangku', [])),
                'tanggal_berita_acara'    => $request->input('tanggal_berita_acara') ?: null,
                'ketua_tim_audit'         => trim((string) $request->input('ketua_tim_audit', '')) ?: null,
                'nama_ktt'                => trim((string) $request->input('nama_ktt', '')) ?: null,
            ];

            $tahapAwal->update($payload);

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Tahap Awal Audit',
                'tindakan'        => "Memperbarui Berita Acara Tahap Awal Audit (TT-MGT-FRS-026L): {$sesi->area_audit}",
                'data_lama'       => null,
                'data_baru'       => ['audit_sesi_id' => $sesi->id, 'no_surat_tugas' => $payload['no_surat_tugas']],
                'waktu_perubahan' => now(),
            ]);

            DB::commit();

            if ($request->has('save_and_print')) {
                return redirect()->route('admin.audit-sesi.tahap-awal.cetak', $sesi->id);
            }

            return back()->with('success', 'Dokumen Berita Acara Tahap Awal Audit (TT-MGT-FRS-026L) berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan dokumen Tahap Awal Audit: ' . $e->getMessage());
        }
    }

    /**
     * Reset Tahap Awal Audit form to empty/blank state.
     */
    public function resetPreset(int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::with('perusahaan')->findOrFail($id);
        AuditTahapAwal::ensureTableExists();

        $defaults = AuditTahapAwal::buildDefaultAttributes($sesi);
        AuditTahapAwal::updateOrCreate(
            ['audit_sesi_id' => $sesi->id],
            $defaults
        );

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Tahap Awal Audit',
            'tindakan'        => "Mereset data Tahap Awal Audit ke preset standar: {$sesi->area_audit}",
            'data_lama'       => null,
            'data_baru'       => ['audit_sesi_id' => $sesi->id],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.tahap-awal', $sesi->id)
            ->with('success', 'Data dokumen Tahap Awal Audit berhasil direset ke preset standar!');
    }

    /**
     * Render printable 17-page A4 Portrait official document (TT-MGT-FRS-026L).
     */
    public function cetak(int|string $id): View
    {
        $query = AuditSesi::with(['user', 'perusahaan']);

        // Enforce area ownership when accessed by Auditee (role: auditor)
        if (auth()->check() && auth()->user()->role === 'auditor') {
            $userArea = auth()->user()->area;
            if (empty($userArea)) {
                abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun. Hubungi Administrator.');
            }
            $query->where(function ($q) use ($userArea) {
                $q->where('area_audit', $userArea)
                  ->orWhere('area_audit', 'like', '%' . $userArea . '%');
            });
        }

        $sesi = $query->findOrFail($id);
        $tahapAwal = AuditTahapAwal::getOrCreateForSesi($sesi);

        $kondisiPenyesuaian = AuditTahapAwal::getDaftarKondisiPenyesuaian();
        $indikatorKelayakan = AuditTahapAwal::getDaftarIndikatorKelayakan();
        $elemenDokumentasi  = AuditTahapAwal::getDaftarElemenDokumentasi();

        return view('laporan.cetak_tahap_awal', compact(
            'sesi',
            'tahapAwal',
            'kondisiPenyesuaian',
            'indikatorKelayakan',
            'elemenDokumentasi'
        ));
    }
}

