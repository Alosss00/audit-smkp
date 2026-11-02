<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AuditSesi;
use App\Models\AuditTahapAwal;
use App\Models\MasterTahapAwal;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterTahapAwalController extends Controller
{
    /**
     * Display a listing of all registered Perusahaans and their Master Tahap Awal Audit configuration.
     */
    public function index(Request $request)
    {
        MasterTahapAwal::ensureTableExists();

        $query = Perusahaan::with(['masterTahapAwal', 'auditSesis']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_perusahaan', 'like', "%{$search}%")
                  ->orWhere('kode_perusahaan', 'like', "%{$search}%")
                  ->orWhere('jenis_komoditas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status_master')) {
            if ($request->status_master === 'customized') {
                $query->whereHas('masterTahapAwal', function ($q) {
                    $q->where('is_customized', true);
                });
            } elseif ($request->status_master === 'default') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('masterTahapAwal')
                      ->orWhereHas('masterTahapAwal', function ($sq) {
                          $sq->where('is_customized', false);
                      });
                });
            }
        }

        $perusahaans = $query->orderBy('nama_perusahaan')->get();

        // Stats across all active companies
        $allPerusahaans = Perusahaan::with('masterTahapAwal')->get();
        $totalPerusahaan = $allPerusahaans->count();
        $totalCustomized = $allPerusahaans->filter(fn($p) => $p->masterTahapAwal && $p->masterTahapAwal->is_customized)->count();
        $totalIup = $allPerusahaans->where('kategori', 'Pemegang IUP')->count();
        $totalKontraktor = $allPerusahaans->whereIn('kategori', ['Kontraktor', 'Subkontraktor'])->count();

        return view('admin.master-tahap-awal.index', compact(
            'perusahaans',
            'totalPerusahaan',
            'totalCustomized',
            'totalIup',
            'totalKontraktor'
        ));
    }

    /**
     * Show the 4-tab Master Data form for a specific Perusahaan's Dokumen Tahap Awal Audit.
     */
    public function edit($perusahaanId)
    {
        MasterTahapAwal::ensureTableExists();

        $perusahaan = Perusahaan::with('auditSesis')->findOrFail($perusahaanId);
        $tahapAwal = MasterTahapAwal::getOrCreateForPerusahaan($perusahaan);
        $allPerusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

        $kondisiPenyesuaian = AuditTahapAwal::getDaftarKondisiPenyesuaian();
        $indikatorKelayakan = AuditTahapAwal::getDaftarIndikatorKelayakan();
        $elemenDokumentasi  = AuditTahapAwal::getDaftarElemenDokumentasi();

        return view('admin.master-tahap-awal.edit', compact(
            'perusahaan',
            'tahapAwal',
            'allPerusahaans',
            'kondisiPenyesuaian',
            'indikatorKelayakan',
            'elemenDokumentasi'
        ));
    }

    /**
     * Update Master Data Dokumen Tahap Awal Audit for a specific Perusahaan.
     */
    public function update(Request $request, $perusahaanId)
    {
        MasterTahapAwal::ensureTableExists();

        $perusahaan = Perusahaan::findOrFail($perusahaanId);
        $tahapAwal = MasterTahapAwal::getOrCreateForPerusahaan($perusahaan);

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
                            'risiko'     => trim((string) ($r['risiko'] ?? 'Kematian/Fatality/ Permanen disbility')),
                            'nilai_awal' => trim((string) ($r['nilai_awal'] ?? 'Tinggi')),
                            'nilai_sisa' => trim((string) ($r['nilai_sisa'] ?? 'Rendah')),
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
                            'pa'              => trim((string) ($r['pa'] ?? '0')),
                            'ma'              => trim((string) ($r['ma'] ?? '0')),
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
                            'capaian'   => trim((string) ($r['capaian'] ?? '0%')),
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
                'faktor_pak'       => $cleanList($trendInput['faktor_pak'] ?? ['Tidak Ada']),
            ];

            $auditPrevInput = $request->input('data_audit_sebelumnya', []);
            $dataAuditSebelumnya = [
                'tahun'            => trim((string) ($auditPrevInput['tahun'] ?? (date('Y') - 1))),
                'nilai_audit'      => trim((string) ($auditPrevInput['nilai_audit'] ?? '-')),
                'status_kritikal'  => trim((string) ($auditPrevInput['status_kritikal'] ?? 'Tidak ada temuan kritikal')),
                'status_mayor'     => trim((string) ($auditPrevInput['status_mayor'] ?? 'Tidak ada temuan Mayor')),
                'status_minor'     => trim((string) ($auditPrevInput['status_minor'] ?? '-')),
                'catatan_instansi' => $cleanList($auditPrevInput['catatan_instansi'] ?? []),
            ];

            $payload = [
                'no_dokumen_header'       => trim((string) $request->input('no_dokumen_header', 'TT-MGT-FRS-026L')),
                'no_dokumen_footer'       => trim((string) $request->input('no_dokumen_footer', 'TT-MGT-FRS-026K')),
                'no_revisi'               => trim((string) $request->input('no_revisi', '01')),
                'tanggal_terbit'          => $request->input('tanggal_terbit') ?: '2025-05-07',
                'tanggal_tinjau_ulang'    => $request->input('tanggal_tinjau_ulang') ?: '2028-05-07',
                'nama_perusahaan_auditi'  => trim((string) $request->input('nama_perusahaan_auditi', $perusahaan->nama_perusahaan)),
                'jenis_perizinan'         => trim((string) $request->input('jenis_perizinan', '')),
                'jenis_komoditas'         => trim((string) $request->input('jenis_komoditas', 'Emas DMP')),
                'alamat_kantor_site'      => trim((string) $request->input('alamat_kantor_site', '')),
                'alamat_lokasi_tambang'   => trim((string) $request->input('alamat_lokasi_tambang', '')),
                'alamat_kantor_pusat'     => trim((string) $request->input('alamat_kantor_pusat', '')),
                'periode_audit_label'     => trim((string) $request->input('periode_audit_label', '')),
                'kinerja_kp'              => $request->input('kinerja_kp', []),
                'faktor_penyesuaian'      => $request->input('faktor_penyesuaian', []),
                'jumlah_pekerja'          => (int) $request->input('jumlah_pekerja', 3086),
                'kelas_risiko'            => trim((string) $request->input('kelas_risiko', 'Tinggi')),
                'mandays_awal'            => (float) $request->input('mandays_awal', 25.0),
                'faktor_penyesuaian_hari' => (float) $request->input('faktor_penyesuaian_hari', 2.5),
                'total_mandays'           => (float) $request->input('total_mandays', 27.5),
                'alokasi_tahap_1'         => (float) $request->input('alokasi_tahap_1', 3.0),
                'alokasi_tahap_2'         => trim((string) $request->input('alokasi_tahap_2', '24,5 Hari ( 4.9 Hari, 5 Auditor)')),
                'no_surat_tugas'          => trim((string) $request->input('no_surat_tugas', '')),
                'tanggal_surat_tugas'     => $request->input('tanggal_surat_tugas') ?: '2024-09-25',
                'tim_audit_tahap_1'       => $cleanTim($request->input('tim_audit_tahap_1', [])),
                'kontak_awal'             => $request->input('kontak_awal', []),
                'kelayakan_audit'         => $request->input('kelayakan_audit', []),
                'kesimpulan_kelayakan'    => $request->input('kesimpulan_kelayakan', 'layak'),
                'kecukupan_dokumen'       => $request->input('kecukupan_dokumen', []),
                'kesimpulan_kecukupan'    => $request->input('kesimpulan_kecukupan', 'dapat_dilanjutkan'),
                'tim_audit_tahap_2'       => $cleanTim($request->input('tim_audit_tahap_2', [])),
                'top_risks_present'       => $cleanRisks($request->input('top_risks_present', [])),
                'top_risks_future'        => $cleanRisks($request->input('top_risks_future', [])),
                'trend_data'              => $trendData,
                'unjuk_kerja_peralatan'   => $cleanPeralatan($request->input('unjuk_kerja_peralatan', [])),
                'capaian_kli'             => $cleanKli($request->input('capaian_kli', [])),
                'data_audit_sebelumnya'   => $dataAuditSebelumnya,
                'harapan_pemangku'        => $cleanList($request->input('harapan_pemangku', [])),
                'tanggal_berita_acara'    => $request->input('tanggal_berita_acara') ?: null,
                'ketua_tim_audit'         => trim((string) $request->input('ketua_tim_audit', 'Fiska Polii')),
                'nama_ktt'                => trim((string) $request->input('nama_ktt', 'Tejo Prihantoro')),
                'is_customized'           => true,
            ];

            $tahapAwal->update($payload);

            // Sync static profile fields to Perusahaan master table
            $perusahaan->update([
                'jenis_perizinan'       => $payload['jenis_perizinan'],
                'jenis_komoditas'       => $payload['jenis_komoditas'],
                'alamat_kantor_site'    => $payload['alamat_kantor_site'],
                'alamat_lokasi_tambang' => $payload['alamat_lokasi_tambang'],
                'alamat_kantor_pusat'   => $payload['alamat_kantor_pusat'],
                'nama_ktt_default'      => $payload['nama_ktt'],
            ]);

            // Optionally sync to existing AuditTahapAwal records for this company's sessions
            if ($request->has('sync_active_sesi')) {
                $sesiIds = AuditSesi::where('perusahaan_id', $perusahaan->id)
                    ->orWhere('area_audit', $perusahaan->nama_perusahaan)
                    ->pluck('id');

                $syncPayload = $payload;
                unset($syncPayload['is_customized']);

                foreach ($sesiIds as $sesiId) {
                    AuditTahapAwal::updateOrCreate(
                        ['audit_sesi_id' => $sesiId],
                        array_merge($syncPayload, ['audit_sesi_id' => $sesiId])
                    );
                }
            }

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Master Tahap Awal Audit',
                'tindakan'        => "Memperbarui Master Data Dokumen Tahap Awal Audit untuk perusahaan: {$perusahaan->nama_perusahaan}",
                'data_lama'       => null,
                'data_baru'       => ['perusahaan_id' => $perusahaan->id, 'nama_perusahaan' => $perusahaan->nama_perusahaan],
                'waktu_perubahan' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.master-tahap-awal.edit', $perusahaan->id)
                ->with('success', "Master Data Dokumen Tahap Awal Audit untuk '{$perusahaan->nama_perusahaan}' berhasil disimpan!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan Master Data Tahap Awal Audit: ' . $e->getMessage());
        }
    }

    /**
     * Reset a Perusahaan's Master Tahap Awal Audit data back to default preset.
     */
    public function reset($perusahaanId)
    {
        MasterTahapAwal::ensureTableExists();

        $perusahaan = Perusahaan::findOrFail($perusahaanId);
        $defaults = MasterTahapAwal::buildDefaultForPerusahaan($perusahaan);

        MasterTahapAwal::updateOrCreate(
            ['perusahaan_id' => $perusahaan->id],
            $defaults
        );

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Tahap Awal Audit',
            'tindakan'        => "Mereset Master Data Tahap Awal Audit ke preset standar: {$perusahaan->nama_perusahaan}",
            'data_lama'       => null,
            'data_baru'       => ['perusahaan_id' => $perusahaan->id],
            'waktu_perubahan' => now(),
        ]);

        return back()->with('success', "Master Data Dokumen Tahap Awal Audit untuk '{$perusahaan->nama_perusahaan}' berhasil direset ke preset standar!");
    }
}

