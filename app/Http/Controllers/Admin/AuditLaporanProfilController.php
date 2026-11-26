<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLaporan;
use App\Models\AuditLaporanNarasiElemen;
use App\Models\AuditLaporanPeralatan;
use App\Models\AuditLaporanProfil;
use App\Models\AuditLog;
use App\Models\AuditRencana;
use App\Models\AuditSesi;
use App\Models\AuditTahapAwal;
use App\Models\Elemen;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuditLaporanProfilController extends Controller
{
    /**
     * Halaman editor Profil & Informasi Umum Laporan Audit untuk Admin & Auditor SMKP.
     */
    public function edit(int|string $id): View
    {
        $sesi = AuditSesi::with(['perusahaan.legalitas', 'user'])->findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);
        $laporan->load(['profil', 'peralatans', 'narasiElemens']);

        $elemens = Elemen::orderBy('kode_elemen')->get();
        $narasiMap = $laporan->narasiElemens->keyBy('elemen_id');
        $rencana = AuditRencana::where('audit_sesi_id', $sesi->id)->with('areas')->first();
        $tahapAwal = AuditTahapAwal::where('audit_sesi_id', $sesi->id)->first();
        $prevSesi = $this->findPreviousPeriodSesi($sesi);

        $isReadOnly = false;
        $totalPerKelompok = $laporan->getTotalPeralatanPerKelompok();
        $grandTotalPeralatan = $laporan->getTotalPeralatan();

        return view('admin.audit-sesi.laporan-profil', compact(
            'sesi',
            'laporan',
            'elemens',
            'narasiMap',
            'rencana',
            'tahapAwal',
            'prevSesi',
            'isReadOnly',
            'totalPerKelompok',
            'grandTotalPeralatan'
        ));
    }

    /**
     * Halaman read-only Profil Laporan untuk role auditor (dibatasi area).
     */
    public function showForAuditor(int|string $id): View
    {
        $userArea = auth()->user()->area;
        if (empty($userArea)) {
            abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun. Hubungi Administrator.');
        }

        $sesi = AuditSesi::with(['perusahaan.legalitas', 'user'])
            ->forUserArea($userArea)
            ->findOrFail($id);

        $laporan = AuditLaporan::where('audit_sesi_id', $sesi->id)
            ->with(['profil', 'peralatans', 'narasiElemens'])
            ->first();

        if (! $laporan) {
            abort(404, 'Data profil laporan audit untuk sesi ini belum disusun.');
        }

        $elemens = Elemen::orderBy('kode_elemen')->get();
        $narasiMap = $laporan->narasiElemens->keyBy('elemen_id');
        $rencana = AuditRencana::where('audit_sesi_id', $sesi->id)->with('areas')->first();
        $tahapAwal = AuditTahapAwal::where('audit_sesi_id', $sesi->id)->first();
        $prevSesi = null;

        $isReadOnly = true;
        $totalPerKelompok = $laporan->getTotalPeralatanPerKelompok();
        $grandTotalPeralatan = $laporan->getTotalPeralatan();

        return view('admin.audit-sesi.laporan-profil', compact(
            'sesi',
            'laporan',
            'elemens',
            'narasiMap',
            'rencana',
            'tahapAwal',
            'prevSesi',
            'isReadOnly',
            'totalPerKelompok',
            'grandTotalPeralatan'
        ));
    }

    /**
     * Simpan perubahan Profil Laporan, Peralatan, dan Narasi Penerapan SMKP per Elemen.
     */
    public function update(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::with('perusahaan')->findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);
        $profil = $laporan->profil ?? AuditLaporanProfil::create(['audit_laporan_id' => $laporan->id]);

        $validated = $request->validate([
            'kapasitas_produksi'     => 'nullable|numeric|min:0|max:999999999999.99',
            'realisasi_produksi'     => 'nullable|numeric|min:0|max:999999999999.99',
            'satuan_produksi'        => 'nullable|string|max:50',
            'jumlah_pekerja'         => 'nullable|integer|min:0|max:10000000',
            'jumlah_pekerja_asing'   => 'nullable|integer|min:0|max:10000000',
            'narasi_kegiatan'        => 'nullable|string|max:20000',
            'narasi_penerapan_smkp'  => 'nullable|string|max:20000',
            'struktur_organisasi'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'hapus_struktur'         => 'nullable|boolean',
            'peralatans'             => 'nullable|array',
            'peralatans.*.kelompok'  => 'nullable|string|max:150',
            'peralatans.*.jenis'     => 'nullable|string|max:150',
            'peralatans.*.tipe'      => 'nullable|string|max:150',
            'peralatans.*.kapasitas' => 'nullable|string|max:100',
            'peralatans.*.jumlah'    => 'nullable|integer|min:0|max:1000000',
            'narasi_elemens'         => 'nullable|array',
            'narasi_elemens.*'       => 'nullable|string|max:10000',
        ], [
            'kapasitas_produksi.numeric'    => 'Kapasitas produksi harus berupa angka.',
            'realisasi_produksi.numeric'    => 'Realisasi produksi harus berupa angka.',
            'jumlah_pekerja.integer'        => 'Jumlah pekerja harus berupa bilangan bulat.',
            'jumlah_pekerja_asing.integer'  => 'Jumlah pekerja asing harus berupa bilangan bulat.',
            'struktur_organisasi.mimes'     => 'Berkas struktur organisasi harus berformat PDF, JPG, JPEG, atau PNG.',
            'struktur_organisasi.max'       => 'Ukuran berkas struktur organisasi maksimal 5 MB.',
            'peralatans.*.jumlah.integer'   => 'Jumlah unit peralatan harus berupa bilangan bulat.',
            'peralatans.*.jumlah.min'       => 'Jumlah unit peralatan tidak boleh negatif.',
        ]);

        $oldSnapshot = [
            'profil'         => $profil->toArray(),
            'peralatans'     => $laporan->peralatans()->get()->toArray(),
            'narasi_elemens' => $laporan->narasiElemens()->get()->toArray(),
        ];

        $newFilePath = $profil->struktur_organisasi_path;
        $oldFileToDelete = null;

        if ($request->boolean('hapus_struktur') && $newFilePath) {
            $oldFileToDelete = $newFilePath;
            $newFilePath = null;
        }

        if ($request->hasFile('struktur_organisasi')) {
            if ($profil->struktur_organisasi_path) {
                $oldFileToDelete = $profil->struktur_organisasi_path;
            }
            $newFilePath = $request->file('struktur_organisasi')->store('struktur_organisasi', 'attachments');
        }

        DB::beginTransaction();
        try {
            $profil->update([
                'kapasitas_produksi'       => $validated['kapasitas_produksi'] ?? null,
                'realisasi_produksi'       => $validated['realisasi_produksi'] ?? null,
                'satuan_produksi'          => isset($validated['satuan_produksi']) && trim((string) $validated['satuan_produksi']) !== ''
                    ? trim((string) $validated['satuan_produksi'])
                    : null,
                'jumlah_pekerja'           => $validated['jumlah_pekerja'] ?? null,
                'jumlah_pekerja_asing'     => $validated['jumlah_pekerja_asing'] ?? null,
                'narasi_kegiatan'          => isset($validated['narasi_kegiatan']) && trim((string) $validated['narasi_kegiatan']) !== ''
                    ? trim((string) $validated['narasi_kegiatan'])
                    : null,
                'narasi_penerapan_smkp'    => isset($validated['narasi_penerapan_smkp']) && trim((string) $validated['narasi_penerapan_smkp']) !== ''
                    ? trim((string) $validated['narasi_penerapan_smkp'])
                    : null,
                'struktur_organisasi_path' => $newFilePath,
            ]);

            // Sync daftar peralatan
            $laporan->peralatans()->delete();
            $urutanAlat = 1;
            foreach ((array) $request->input('peralatans', []) as $alat) {
                $kelompok = trim((string) ($alat['kelompok'] ?? ''));
                $jenis = trim((string) ($alat['jenis'] ?? ''));
                if ($kelompok === '' && $jenis === '') {
                    continue;
                }
                AuditLaporanPeralatan::create([
                    'audit_laporan_id' => $laporan->id,
                    'kelompok'         => $kelompok !== '' ? $kelompok : 'Lainnya',
                    'jenis'            => $jenis !== '' ? $jenis : '-',
                    'tipe'             => trim((string) ($alat['tipe'] ?? '')) ?: null,
                    'kapasitas'        => trim((string) ($alat['kapasitas'] ?? '')) ?: null,
                    'jumlah'           => max(0, (int) ($alat['jumlah'] ?? 0)),
                    'urutan'           => $urutanAlat++,
                ]);
            }

            // Upsert narasi per elemen
            $validElemenIds = Elemen::pluck('id')->all();
            foreach ((array) $request->input('narasi_elemens', []) as $elemenId => $narasiText) {
                $eid = (int) $elemenId;
                if (! in_array($eid, $validElemenIds, true)) {
                    continue;
                }
                $cleanText = trim((string) $narasiText);
                AuditLaporanNarasiElemen::updateOrCreate(
                    ['audit_laporan_id' => $laporan->id, 'elemen_id' => $eid],
                    ['narasi' => $cleanText !== '' ? $cleanText : null]
                );
            }

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Profil Laporan Audit',
                'tindakan'        => "Memperbarui Profil & Informasi Umum Laporan Audit sesi #{$sesi->id}: {$sesi->area_audit}",
                'data_lama'       => $oldSnapshot,
                'data_baru'       => [
                    'profil'         => $profil->fresh()->toArray(),
                    'peralatans'     => $laporan->peralatans()->get()->toArray(),
                    'narasi_elemens' => $laporan->narasiElemens()->get()->toArray(),
                ],
                'waktu_perubahan' => now(),
            ]);

            DB::commit();

            if ($oldFileToDelete && Storage::disk('attachments')->exists($oldFileToDelete)) {
                Storage::disk('attachments')->delete($oldFileToDelete);
            }

            return redirect()->route('admin.audit-sesi.laporan.profil', $sesi->id)
                ->with('success', 'Profil dan Informasi Umum Laporan Audit berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->hasFile('struktur_organisasi') && $newFilePath && Storage::disk('attachments')->exists($newFilePath)) {
                Storage::disk('attachments')->delete($newFilePath);
            }

            return back()->withInput()->with('error', 'Gagal menyimpan profil laporan: ' . $e->getMessage());
        }
    }

    /**
     * Salin jumlah pekerja dari AuditTahapAwal sesi yang sama.
     */
    public function copyFromTahapAwal(int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);
        $profil = $laporan->profil ?? AuditLaporanProfil::create(['audit_laporan_id' => $laporan->id]);

        $tahapAwal = AuditTahapAwal::where('audit_sesi_id', $sesi->id)->first();
        if (! $tahapAwal || $tahapAwal->jumlah_pekerja === null) {
            return redirect()->route('admin.audit-sesi.laporan.profil', $sesi->id)
                ->with('error', 'Jumlah pekerja pada Dokumen Tahap Awal sesi ini belum diisi.');
        }

        $oldJumlah = $profil->jumlah_pekerja;
        $profil->update(['jumlah_pekerja' => (int) $tahapAwal->jumlah_pekerja]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Profil Laporan Audit',
            'tindakan'        => "Menyalin jumlah pekerja ({$tahapAwal->jumlah_pekerja}) dari Tahap Awal ke Profil Laporan sesi #{$sesi->id}",
            'data_lama'       => ['jumlah_pekerja' => $oldJumlah],
            'data_baru'       => ['jumlah_pekerja' => (int) $tahapAwal->jumlah_pekerja],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.laporan.profil', $sesi->id)
            ->with('success', "Jumlah pekerja ({$tahapAwal->jumlah_pekerja} orang) berhasil disalin dari Dokumen Tahap Awal.");
    }

    /**
     * Salin daftar peralatan & narasi dari sesi periode sebelumnya (tahun_periode - 1) perusahaan yang sama.
     */
    public function copyFromPreviousPeriod(int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);
        $profil = $laporan->profil ?? AuditLaporanProfil::create(['audit_laporan_id' => $laporan->id]);

        $prevSesi = $this->findPreviousPeriodSesi($sesi);
        if (! $prevSesi || ! $prevSesi->laporan) {
            return redirect()->route('admin.audit-sesi.laporan.profil', $sesi->id)
                ->with('error', 'Tidak ditemukan data profil laporan dari periode sebelumnya (' . ($sesi->tahun_periode - 1) . ') untuk perusahaan ini.');
        }

        $prevLaporan = $prevSesi->laporan;
        $prevLaporan->load(['profil', 'peralatans', 'narasiElemens']);

        DB::beginTransaction();
        try {
            if ($prevLaporan->profil) {
                $profil->update([
                    'narasi_kegiatan'       => $prevLaporan->profil->narasi_kegiatan,
                    'narasi_penerapan_smkp' => $prevLaporan->profil->narasi_penerapan_smkp,
                    'satuan_produksi'       => $profil->satuan_produksi ?: $prevLaporan->profil->satuan_produksi,
                ]);
            }

            $laporan->peralatans()->delete();
            $copiedAlat = 0;
            foreach ($prevLaporan->peralatans as $alat) {
                AuditLaporanPeralatan::create([
                    'audit_laporan_id' => $laporan->id,
                    'kelompok'         => $alat->kelompok,
                    'jenis'            => $alat->jenis,
                    'tipe'             => $alat->tipe,
                    'kapasitas'        => $alat->kapasitas,
                    'jumlah'           => $alat->jumlah,
                    'urutan'           => $alat->urutan,
                ]);
                $copiedAlat++;
            }

            $copiedNarasi = 0;
            foreach ($prevLaporan->narasiElemens as $ne) {
                AuditLaporanNarasiElemen::updateOrCreate(
                    ['audit_laporan_id' => $laporan->id, 'elemen_id' => $ne->elemen_id],
                    ['narasi' => $ne->narasi]
                );
                $copiedNarasi++;
            }

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Profil Laporan Audit',
                'tindakan'        => "Menyalin profil laporan dari periode {$prevSesi->tahun_periode} (sesi #{$prevSesi->id}) ke sesi #{$sesi->id}",
                'data_lama'       => null,
                'data_baru'       => [
                    'source_sesi_id'      => $prevSesi->id,
                    'copied_peralatans'   => $copiedAlat,
                    'copied_narasi_elemen'=> $copiedNarasi,
                ],
                'waktu_perubahan' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.audit-sesi.laporan.profil', $sesi->id)
                ->with('success', "Berhasil menyalin {$copiedAlat} peralatan dan {$copiedNarasi} narasi elemen dari periode {$prevSesi->tahun_periode}.");
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('admin.audit-sesi.laporan.profil', $sesi->id)
                ->with('error', 'Gagal menyalin data periode sebelumnya: ' . $e->getMessage());
        }
    }

    /**
     * Cari sesi periode sebelumnya (tahun_periode - 1) pada perusahaan yang sama yang memiliki AuditLaporan.
     */
    protected function findPreviousPeriodSesi(AuditSesi $sesi): ?AuditSesi
    {
        if (! $sesi->perusahaan_id || ! $sesi->tahun_periode) {
            return null;
        }

        return AuditSesi::where('perusahaan_id', $sesi->perusahaan_id)
            ->where('tahun_periode', $sesi->tahun_periode - 1)
            ->where('id', '!=', $sesi->id)
            ->whereHas('laporan')
            ->with('laporan')
            ->latest('id')
            ->first();
    }
}
