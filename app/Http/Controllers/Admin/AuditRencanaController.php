<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AuditRencana;
use App\Models\AuditRencanaArea;
use App\Models\AuditRencanaJadwal;
use App\Models\AuditRencanaPengesahan;
use App\Models\AuditRencanaSampel;
use App\Models\AuditRencanaTim;
use App\Models\AuditSesi;
use App\Models\AuditTahapAwal;
use App\Models\Elemen;
use App\Services\RencanaAuditService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AuditRencanaController extends Controller
{
    public function __construct(protected RencanaAuditService $service)
    {
    }

    /**
     * Halaman editor 5 tab Rencana Audit (TT-MGT-FRS-026A) untuk Admin & Auditor SMKP.
     */
    public function edit(int|string $id): View
    {
        $sesi = AuditSesi::with(['perusahaan', 'user'])->findOrFail($id);
        $rencana = AuditRencana::getOrInitializeForSesi($sesi);
        $rencana->load(['tims', 'jadwals.elemen', 'sampels.subElemen.elemen', 'pengesahans', 'areas']);

        $elemens = Elemen::orderBy('kode_elemen')->get();
        $tahapAwal = AuditTahapAwal::where('audit_sesi_id', $sesi->id)->first();
        $mandaysWarning = $this->service->getMandaysWarning($rencana);

        $isReadOnly = $rencana->isFinal() && ! auth()->user()->isAdmin();
        $perusahaanKelompok = $sesi->perusahaan ? $sesi->perusahaan->kelompokPenandatangan() : null;

        return view('admin.audit-sesi.rencana', compact(
            'sesi',
            'rencana',
            'elemens',
            'tahapAwal',
            'mandaysWarning',
            'isReadOnly',
            'perusahaanKelompok'
        ));
    }

    /**
     * Halaman read-only Rencana Audit untuk role auditor (Auditee / PIC Area).
     */
    public function showForAuditor(int|string $id): View
    {
        $userArea = auth()->user()->area;
        if (empty($userArea)) {
            abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun. Hubungi Administrator.');
        }

        $sesi = AuditSesi::with(['perusahaan', 'user'])
            ->forUserArea($userArea)
            ->findOrFail($id);

        $rencana = AuditRencana::where('audit_sesi_id', $sesi->id)
            ->with(['tims', 'jadwals.elemen', 'sampels.subElemen.elemen', 'pengesahans', 'areas'])
            ->first();

        if (! $rencana) {
            abort(404, 'Rencana Audit untuk sesi ini belum disusun.');
        }

        $elemens = Elemen::orderBy('kode_elemen')->get();
        $tahapAwal = AuditTahapAwal::where('audit_sesi_id', $sesi->id)->first();
        $mandaysWarning = $this->service->getMandaysWarning($rencana);
        $isReadOnly = true;
        $perusahaanKelompok = $sesi->perusahaan ? $sesi->perusahaan->kelompokPenandatangan() : null;

        return view('admin.audit-sesi.rencana', compact(
            'sesi',
            'rencana',
            'elemens',
            'tahapAwal',
            'mandaysWarning',
            'isReadOnly',
            'perusahaanKelompok'
        ));
    }

    /**
     * Simpan perubahan seluruh tab Rencana Audit.
     */
    public function update(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::with('perusahaan')->findOrFail($id);
        $rencana = AuditRencana::getOrInitializeForSesi($sesi);

        // Jika status sudah final, hanya Admin yang boleh mengubah
        if ($rencana->isFinal() && ! auth()->user()->isAdmin()) {
            abort(403, 'Rencana Audit sudah berstatus Final dan hanya dapat diubah oleh Administrator.');
        }

        $validated = $request->validate([
            'no_dokumen'              => 'required|string|max:100',
            'jenis_audit'             => 'nullable|string|max:255',
            'kriteria_audit'          => 'nullable|string|max:5000',
            'tujuan'                  => 'nullable|string|max:5000',
            'ruang_lingkup'           => 'nullable|string|max:5000',
            'pengecualian'            => 'nullable|string|max:5000',
            'dokumen_relevan'         => 'nullable|array',
            'dokumen_relevan.*'       => 'nullable|string|max:500',
            'areas'                   => 'nullable|array',
            'areas.*'                 => 'nullable|string|max:255',
            'fasilitas'               => 'nullable|string|max:5000',
            'distribusi_laporan'      => 'nullable|string|max:5000',
            'kelompok_penandatangan'  => ['nullable', Rule::in(array_keys(AuditRencana::KELOMPOK_PENANDATANGAN_OPTIONS))],
            'tims'                    => 'nullable|array',
            'tims.*.nama'             => 'nullable|string|max:255',
            'tims.*.jabatan'          => 'nullable|string|max:255',
            'tims.*.peran'            => ['nullable', Rule::in(array_keys(AuditRencana::PERAN_OPTIONS))],
            'jadwals'                 => 'nullable|array',
            'jadwals.*.hari_ke'       => 'nullable|integer|min:1|max:365',
            'jadwals.*.tanggal'       => 'nullable|date',
            'jadwals.*.jam_mulai'     => 'nullable|string|max:10',
            'jadwals.*.jam_selesai'   => 'nullable|string|max:10',
            'jadwals.*.jenis_kegiatan' => ['nullable', Rule::in(array_keys(AuditRencana::JENIS_KEGIATAN_OPTIONS))],
            'jadwals.*.kegiatan'      => 'nullable|string|max:2000',
            'jadwals.*.elemen_id'     => 'nullable|integer|exists:elemens,id',
            'jadwals.*.auditor'       => 'nullable|string|max:255',
            'jadwals.*.auditi_pic'    => 'nullable|string|max:255',
            'jadwals.*.lokasi'        => 'nullable|string|max:255',
            'sampels'                 => 'nullable|array',
            'pengesahans'             => 'nullable|array',
            'pengesahans.*.jabatan'   => 'nullable|string|max:255',
            'pengesahans.*.nama'      => 'nullable|string|max:255',
            'pengesahans.*.tanggal_ttd' => 'nullable|date',
        ], [
            'no_dokumen.required' => 'Nomor dokumen Rencana Audit wajib diisi.',
        ]);

        // Kumpulkan baris jadwal yang terisi
        $rawJadwals = [];
        foreach ((array) $request->input('jadwals', []) as $j) {
            $kegiatan = trim((string) ($j['kegiatan'] ?? ''));
            $tgl = trim((string) ($j['tanggal'] ?? ''));
            if ($kegiatan === '' && $tgl === '') {
                continue;
            }
            $rawJadwals[] = [
                'hari_ke'        => (int) ($j['hari_ke'] ?? 1),
                'tanggal'        => $tgl,
                'jam_mulai'      => trim((string) ($j['jam_mulai'] ?? '08:00')),
                'jam_selesai'    => trim((string) ($j['jam_selesai'] ?? '17:00')),
                'jenis_kegiatan' => (string) ($j['jenis_kegiatan'] ?? 'audit_elemen'),
                'kegiatan'       => $kegiatan ?: 'Kegiatan Audit',
                'elemen_id'      => ! empty($j['elemen_id']) ? (int) $j['elemen_id'] : null,
                'auditor'        => trim((string) ($j['auditor'] ?? '')) ?: null,
                'auditi_pic'     => trim((string) ($j['auditi_pic'] ?? '')) ?: null,
                'lokasi'         => trim((string) ($j['lokasi'] ?? '')) ?: null,
            ];
        }

        // Validasi jadwal & sampel lewat RencanaAuditService
        $jadwalErrors = $this->service->validateJadwals($sesi, $rawJadwals, false);
        $sampelResult = $this->service->validateAndNormalizeSampels((array) $request->input('sampels', []), false);
        $allErrors = array_merge($jadwalErrors, $sampelResult['errors']);

        if (! empty($allErrors)) {
            return back()->withErrors($allErrors)->withInput();
        }

        $oldSnapshot = $rencana->toArray();

        DB::beginTransaction();
        try {
            $dokumenRelevan = array_values(array_filter(
                array_map(fn ($v) => is_string($v) ? trim($v) : '', (array) $request->input('dokumen_relevan', [])),
                fn ($v) => $v !== ''
            ));

            $kelompok = $validated['kelompok_penandatangan'] ?? $rencana->kelompok_penandatangan;

            $rencana->update([
                'no_dokumen'             => trim($validated['no_dokumen']),
                'jenis_audit'            => trim((string) ($validated['jenis_audit'] ?? '')) ?: null,
                'kriteria_audit'         => trim((string) ($validated['kriteria_audit'] ?? '')) ?: null,
                'tujuan'                 => trim((string) ($validated['tujuan'] ?? '')) ?: null,
                'ruang_lingkup'          => trim((string) ($validated['ruang_lingkup'] ?? '')) ?: null,
                'pengecualian'           => trim((string) ($validated['pengecualian'] ?? '')) ?: null,
                'dokumen_relevan'        => $dokumenRelevan,
                'fasilitas'              => trim((string) ($validated['fasilitas'] ?? '')) ?: null,
                'distribusi_laporan'     => trim((string) ($validated['distribusi_laporan'] ?? '')) ?: null,
                'kelompok_penandatangan' => $kelompok,
            ]);

            // Sync Daftar Area Lingkup Audit
            $rencana->areas()->delete();
            $urutanArea = 1;
            foreach ((array) $request->input('areas', []) as $areaItem) {
                $namaArea = is_array($areaItem)
                    ? trim((string) ($areaItem['nama_area'] ?? ''))
                    : trim((string) $areaItem);
                if ($namaArea === '') {
                    continue;
                }
                AuditRencanaArea::create([
                    'audit_rencana_id' => $rencana->id,
                    'nama_area'        => $namaArea,
                    'urutan'           => $urutanArea++,
                ]);
            }

            // Sync Tim Audit
            $rencana->tims()->delete();
            $urutanTim = 1;
            foreach ((array) $request->input('tims', []) as $t) {
                $nama = trim((string) ($t['nama'] ?? ''));
                if ($nama === '') {
                    continue;
                }
                AuditRencanaTim::create([
                    'audit_rencana_id' => $rencana->id,
                    'nama'             => $nama,
                    'jabatan'          => trim((string) ($t['jabatan'] ?? '')) ?: null,
                    'peran'            => in_array(($t['peran'] ?? ''), array_keys(AuditRencana::PERAN_OPTIONS), true) ? $t['peran'] : 'auditor',
                    'urutan'           => $urutanTim++,
                ]);
            }

            // Sync Jadwal
            $rencana->jadwals()->delete();
            foreach ($rawJadwals as $j) {
                if ($j['tanggal'] === '') {
                    continue;
                }
                AuditRencanaJadwal::create([
                    'audit_rencana_id' => $rencana->id,
                    'hari_ke'          => $j['hari_ke'],
                    'tanggal'          => $j['tanggal'],
                    'jam_mulai'        => $j['jam_mulai'],
                    'jam_selesai'      => $j['jam_selesai'],
                    'jenis_kegiatan'   => $j['jenis_kegiatan'],
                    'kegiatan'         => $j['kegiatan'],
                    'elemen_id'        => $j['elemen_id'],
                    'auditor'          => $j['auditor'],
                    'auditi_pic'       => $j['auditi_pic'],
                    'lokasi'           => $j['lokasi'],
                ]);
            }

            // Update Sampel rows
            foreach ($sampelResult['computed'] as $sampelId => $sData) {
                $sampelModel = AuditRencanaSampel::where('audit_rencana_id', $rencana->id)
                    ->where('id', (int) $sampelId)
                    ->first();
                if ($sampelModel) {
                    $sampelModel->update($sData);
                }
            }

            // Sync Pengesahan
            $rawPengesahan = (array) $request->input('pengesahans', []);
            $filledPengesahan = [];
            foreach ($rawPengesahan as $p) {
                $jab = trim((string) ($p['jabatan'] ?? ''));
                if ($jab === '') {
                    continue;
                }
                $filledPengesahan[] = [
                    'jabatan'     => $jab,
                    'nama'        => trim((string) ($p['nama'] ?? '')) ?: null,
                    'tanggal_ttd' => ! empty($p['tanggal_ttd']) ? $p['tanggal_ttd'] : null,
                ];
            }

            if (! empty($filledPengesahan)) {
                $rencana->pengesahans()->delete();
                foreach ($filledPengesahan as $idx => $p) {
                    AuditRencanaPengesahan::create([
                        'audit_rencana_id' => $rencana->id,
                        'urutan'           => $idx + 1,
                        'jabatan'          => $p['jabatan'],
                        'nama'             => $p['nama'],
                        'tanggal_ttd'      => $p['tanggal_ttd'],
                    ]);
                }
            } elseif ($kelompok && ! $rencana->pengesahans()->exists()) {
                $rencana->seedPengesahanFromMaster($kelompok);
            }

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Rencana Audit',
                'tindakan'        => "Memperbarui Rencana Audit ({$rencana->no_dokumen}) sesi #{$sesi->id}: {$sesi->area_audit}" . ($rencana->isFinal() ? ' [Override Final oleh Admin]' : ''),
                'data_lama'       => $oldSnapshot,
                'data_baru'       => $rencana->fresh()->toArray(),
                'waktu_perubahan' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.audit-sesi.rencana.edit', $sesi->id)
                ->with('success', 'Rencana Audit (Formulir 026A) berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan Rencana Audit: ' . $e->getMessage());
        }
    }

    /**
     * Finalisasi Rencana Audit setelah seluruh aturan validasi terpenuhi.
     */
    public function finalize(int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $rencana = AuditRencana::getOrInitializeForSesi($sesi);

        if ($rencana->isFinal()) {
            return redirect()->route('admin.audit-sesi.rencana.edit', $sesi->id)
                ->with('info', 'Rencana Audit ini sudah berstatus Final.');
        }

        $errors = $this->service->validateForFinalization($rencana);
        if (! empty($errors)) {
            return redirect()->route('admin.audit-sesi.rencana.edit', $sesi->id)
                ->withErrors($errors);
        }

        $oldStatus = $rencana->status;
        $rencana->update(['status' => 'final']);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Rencana Audit',
            'tindakan'        => "Memfinalisasi Rencana Audit ({$rencana->no_dokumen}) sesi #{$sesi->id}: {$sesi->area_audit}",
            'data_lama'       => ['status' => $oldStatus],
            'data_baru'       => ['status' => 'final'],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.rencana.edit', $sesi->id)
            ->with('success', 'Rencana Audit berhasil difinalkan. Dokumen kini terkunci kecuali untuk Administrator.');
    }

    /**
     * Salin anggota tim dari tim_audit_tahap_2 di AuditTahapAwal sesi yang sama.
     */
    public function copyTimTahap2(int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $rencana = AuditRencana::getOrInitializeForSesi($sesi);

        if ($rencana->isFinal() && ! auth()->user()->isAdmin()) {
            abort(403, 'Rencana Audit sudah berstatus Final dan hanya dapat diubah oleh Administrator.');
        }

        $tahapAwal = AuditTahapAwal::where('audit_sesi_id', $sesi->id)->first();
        $tim2 = $tahapAwal ? ($tahapAwal->tim_audit_tahap_2 ?? []) : [];

        if (empty($tim2) || ! is_array($tim2)) {
            return redirect()->route('admin.audit-sesi.rencana.edit', $sesi->id)
                ->with('error', 'Data Tim Audit Tahap II belum tersedia pada Dokumen Tahap Awal sesi ini.');
        }

        DB::beginTransaction();
        try {
            $rencana->tims()->delete();
            $urutan = 1;
            $copied = 0;

            foreach ($tim2 as $anggota) {
                $nama = trim((string) ($anggota['nama'] ?? ''));
                if ($nama === '') {
                    continue;
                }
                $jabatanRaw = trim((string) ($anggota['jabatan'] ?? 'Auditor'));
                $jabatanLower = strtolower($jabatanRaw);

                $peran = 'auditor';
                if (str_contains($jabatanLower, 'ketua') || str_contains($jabatanLower, 'lead')) {
                    $peran = 'lead_auditor';
                } elseif (str_contains($jabatanLower, 'observer') || str_contains($jabatanLower, 'pengamat')) {
                    $peran = 'pengamat';
                } elseif (str_contains($jabatanLower, 'ahli')) {
                    $peran = 'tenaga_ahli';
                }

                $noReg = trim((string) ($anggota['no_registrasi'] ?? ''));
                $jabatanLabel = $noReg !== '' ? "{$jabatanRaw} (Reg: {$noReg})" : $jabatanRaw;

                AuditRencanaTim::create([
                    'audit_rencana_id' => $rencana->id,
                    'nama'             => $nama,
                    'jabatan'          => $jabatanLabel,
                    'peran'            => $peran,
                    'urutan'           => $urutan++,
                ]);
                $copied++;
            }

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Rencana Audit',
                'tindakan'        => "Menyalin {$copied} anggota Tim Audit dari Tahap II ke Rencana Audit sesi #{$sesi->id}",
                'data_lama'       => null,
                'data_baru'       => ['copied_count' => $copied],
                'waktu_perubahan' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.audit-sesi.rencana.edit', $sesi->id)
                ->with('success', "Berhasil menyalin {$copied} anggota tim dari Tim Audit Tahap II.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.audit-sesi.rencana.edit', $sesi->id)
                ->with('error', 'Gagal menyalin tim: ' . $e->getMessage());
        }
    }

    /**
     * Halaman cetak Rencana Audit (A4 Landscape) untuk Admin, Auditor SMKP, dan Auditor (scoped by area).
     */
    public function cetak(int|string $id): View
    {
        $query = AuditSesi::with(['perusahaan', 'user']);

        if (auth()->check() && auth()->user()->role === 'auditor') {
            $userArea = auth()->user()->area;
            if (empty($userArea)) {
                abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun. Hubungi Administrator.');
            }
            $query->forUserArea($userArea);
        }

        $sesi = $query->findOrFail($id);
        $rencana = AuditRencana::where('audit_sesi_id', $sesi->id)
            ->with(['tims', 'jadwals.elemen', 'sampels.subElemen.elemen', 'pengesahans', 'areas'])
            ->first();

        if (! $rencana) {
            if (auth()->user()->role === 'auditor') {
                abort(404, 'Rencana Audit untuk sesi ini belum disusun.');
            }
            $rencana = AuditRencana::getOrInitializeForSesi($sesi);
            $rencana->load(['tims', 'jadwals.elemen', 'sampels.subElemen.elemen', 'pengesahans', 'areas']);
        }

        $tahapAwal = AuditTahapAwal::where('audit_sesi_id', $sesi->id)->first();

        return view('laporan.cetak_rencana_audit', compact('sesi', 'rencana', 'tahapAwal'));
    }
}
