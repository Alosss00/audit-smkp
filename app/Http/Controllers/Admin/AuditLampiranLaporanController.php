<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditDaftarHadir;
use App\Models\AuditDaftarHadirPeserta;
use App\Models\AuditLaporan;
use App\Models\AuditLaporanIujp;
use App\Models\AuditLog;
use App\Models\AuditRencana;
use App\Models\AuditResponManajemen;
use App\Models\AuditSesi;
use App\Models\MasterTemplateLaporan;
use App\Services\LaporanAuditWorkflowService;
use App\Services\LaporanTemplateRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AuditLampiranLaporanController extends Controller
{
    public function __construct(
        protected LaporanAuditWorkflowService $workflow,
        protected LaporanTemplateRenderer $renderer
    ) {
    }

    /**
     * Halaman terpadu pengelolaan Lampiran Laporan (Daftar Hadir, Respon Manajemen, Hasil Audit IUJP).
     */
    public function index(int|string $id): View
    {
        $sesi = AuditSesi::with(['perusahaan', 'user'])->findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);

        return $this->renderLampiranView($sesi, $laporan, false);
    }

    /**
     * Halaman read-only Lampiran Laporan untuk role auditor (area-scoped).
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

        $laporan = AuditLaporan::where('audit_sesi_id', $sesi->id)->first();
        if (! $laporan) {
            abort(404, 'Laporan Audit untuk sesi ini belum disusun.');
        }

        return $this->renderLampiranView($sesi, $laporan, true);
    }

    protected function renderLampiranView(AuditSesi $sesi, AuditLaporan $laporan, bool $isReadOnly): View
    {
        $dhPembukaan = AuditDaftarHadir::with('pesertas')
            ->firstOrCreate(
                ['audit_sesi_id' => $sesi->id, 'jenis' => 'pembukaan'],
                [
                    'tanggal'        => $sesi->tanggal_mulai,
                    'judul_kegiatan' => AuditDaftarHadir::JENIS_OPTIONS['pembukaan'],
                ]
            );

        $dhPenutupan = AuditDaftarHadir::with('pesertas')
            ->firstOrCreate(
                ['audit_sesi_id' => $sesi->id, 'jenis' => 'penutupan'],
                [
                    'tanggal'        => $sesi->tanggal_selesai,
                    'judul_kegiatan' => AuditDaftarHadir::JENIS_OPTIONS['penutupan'],
                ]
            );

        $responManajemen = AuditResponManajemen::firstOrCreate(
            ['audit_sesi_id' => $sesi->id],
            [
                'nama_penandatangan'    => $sesi->perusahaan?->nama_ktt_default,
                'jabatan_penandatangan' => 'Kepala Teknik Tambang / PJO',
            ]
        );

        $iujpEligible = $laporan->isEligibleForIujp();
        $iujps = $laporan->iujps()->with('sumberSesi.perusahaan')->get();

        // Daftar sesi selesai milik perusahaan jasa pertambangan / kontraktor pada tahun sama untuk tarik otomatis
        $availableIujpSesis = collect();
        if ($iujpEligible && ! $isReadOnly) {
            $availableIujpSesis = AuditSesi::with('perusahaan')
                ->where('id', '!=', $sesi->id)
                ->where('status', 'selesai')
                ->whereHas('perusahaan', function ($q) {
                    $q->whereIn('jenis_usaha', ['jasa_pertambangan_inti', 'jasa_konsultasi_perencanaan', 'jasa_non_inti'])
                      ->orWhereIn('kategori', ['Kontraktor', 'Subkontraktor']);
                })
                ->orderByDesc('tahun_periode')
                ->orderByDesc('id')
                ->get();
        }

        $warnings = [
            'pembukaan' => $dhPembukaan->getDateWarning($sesi),
            'penutupan' => $dhPenutupan->getDateWarning($sesi),
            'respon'    => $responManajemen->getDateWarning($sesi),
        ];

        return view('admin.audit-sesi.laporan-lampiran', compact(
            'sesi',
            'laporan',
            'dhPembukaan',
            'dhPenutupan',
            'responManajemen',
            'iujpEligible',
            'iujps',
            'availableIujpSesis',
            'warnings',
            'isReadOnly'
        ));
    }

    /**
     * Simpan Daftar Hadir Pembukaan atau Penutupan beserta peserta & scan opsional.
     */
    public function saveDaftarHadir(Request $request, int|string $id, string $jenis): RedirectResponse
    {
        if (! array_key_exists($jenis, AuditDaftarHadir::JENIS_OPTIONS)) {
            abort(404, 'Jenis daftar hadir tidak dikenali.');
        }

        $sesi = AuditSesi::findOrFail($id);
        $dh = AuditDaftarHadir::firstOrCreate(
            ['audit_sesi_id' => $sesi->id, 'jenis' => $jenis],
            ['judul_kegiatan' => AuditDaftarHadir::JENIS_OPTIONS[$jenis]]
        );

        $validated = $request->validate([
            'tanggal'                 => 'nullable|date',
            'lokasi'                  => 'nullable|string|max:200',
            'scan'                    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'hapus_scan'              => 'nullable|boolean',
            'pesertas'                => 'nullable|array',
            'pesertas.*.nama'         => 'nullable|string|max:150',
            'pesertas.*.jabatan'      => 'nullable|string|max:150',
            'pesertas.*.unit_instansi'=> 'nullable|string|max:150',
            'pesertas.*.peran'        => ['nullable', Rule::in(array_keys(AuditDaftarHadirPeserta::PERAN_OPTIONS))],
            'pesertas.*.hadir'        => 'nullable',
        ], [
            'scan.mimes' => 'Berkas scan daftar hadir harus berformat PDF, JPG, JPEG, atau PNG.',
            'scan.max'   => 'Ukuran berkas scan daftar hadir maksimal 5 MB.',
        ]);

        $old = ['daftar_hadir' => $dh->toArray(), 'pesertas' => $dh->pesertas()->get()->toArray()];
        $newScanPath = $dh->scan_path;
        $oldScanToDelete = null;

        if ($request->boolean('hapus_scan') && $newScanPath) {
            $oldScanToDelete = $newScanPath;
            $newScanPath = null;
        }
        if ($request->hasFile('scan')) {
            if ($dh->scan_path) {
                $oldScanToDelete = $dh->scan_path;
            }
            $newScanPath = $request->file('scan')->store('daftar_hadir', 'attachments');
        }

        DB::beginTransaction();
        try {
            // Judul kegiatan selalu otomatis mengikuti jenis (mencegah kesalahan judul Pembukaan pada Penutupan)
            $dh->update([
                'tanggal'        => $validated['tanggal'] ?? null,
                'lokasi'         => isset($validated['lokasi']) && trim((string) $validated['lokasi']) !== '' ? trim((string) $validated['lokasi']) : null,
                'judul_kegiatan' => AuditDaftarHadir::JENIS_OPTIONS[$jenis],
                'scan_path'      => $newScanPath,
            ]);

            $dh->pesertas()->delete();
            $urutan = 1;
            foreach ((array) $request->input('pesertas', []) as $p) {
                $nama = trim((string) ($p['nama'] ?? ''));
                if ($nama === '') {
                    continue;
                }
                AuditDaftarHadirPeserta::create([
                    'audit_daftar_hadir_id' => $dh->id,
                    'nama'                  => $nama,
                    'jabatan'               => trim((string) ($p['jabatan'] ?? '')) ?: null,
                    'unit_instansi'         => trim((string) ($p['unit_instansi'] ?? '')) ?: null,
                    'peran'                 => in_array(($p['peran'] ?? ''), array_keys(AuditDaftarHadirPeserta::PERAN_OPTIONS), true) ? $p['peran'] : 'auditi',
                    'hadir'                 => isset($p['hadir']) ? (bool) $p['hadir'] : true,
                    'urutan'                => $urutan++,
                ]);
            }

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Lampiran Daftar Hadir',
                'tindakan'        => "Menyimpan Daftar Hadir {$jenis} sesi #{$sesi->id}",
                'data_lama'       => $old,
                'data_baru'       => ['daftar_hadir' => $dh->fresh()->toArray(), 'peserta_count' => $urutan - 1],
                'waktu_perubahan' => now(),
            ]);

            DB::commit();

            if ($oldScanToDelete && Storage::disk('attachments')->exists($oldScanToDelete)) {
                Storage::disk('attachments')->delete($oldScanToDelete);
            }

            return redirect()->route('admin.audit-sesi.laporan.lampiran', $sesi->id)
                ->with('success', 'Daftar Hadir ' . ucfirst($jenis) . ' berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->hasFile('scan') && $newScanPath && Storage::disk('attachments')->exists($newScanPath)) {
                Storage::disk('attachments')->delete($newScanPath);
            }

            return back()->withInput()->with('error', 'Gagal menyimpan daftar hadir: ' . $e->getMessage());
        }
    }

    /**
     * Prefill peserta Daftar Hadir dari Tim Audit & Pengesahan pada Rencana Audit sesi.
     */
    public function prefillDaftarHadir(int|string $id, string $jenis): RedirectResponse
    {
        if (! array_key_exists($jenis, AuditDaftarHadir::JENIS_OPTIONS)) {
            abort(404, 'Jenis daftar hadir tidak dikenali.');
        }

        $sesi = AuditSesi::with('perusahaan')->findOrFail($id);
        $rencana = AuditRencana::where('audit_sesi_id', $sesi->id)->with(['tims', 'pengesahans'])->first();

        if (! $rencana || ($rencana->tims->isEmpty() && $rencana->pengesahans->isEmpty())) {
            return redirect()->route('admin.audit-sesi.laporan.lampiran', $sesi->id)
                ->with('error', 'Data Tim Audit dan Pengesahan pada Rencana Audit belum diisi.');
        }

        $dh = AuditDaftarHadir::firstOrCreate(
            ['audit_sesi_id' => $sesi->id, 'jenis' => $jenis],
            ['judul_kegiatan' => AuditDaftarHadir::JENIS_OPTIONS[$jenis]]
        );

        $existingNames = $dh->pesertas()->pluck('nama')->map(fn ($n) => strtolower(trim((string) $n)))->all();
        $urutan = ((int) $dh->pesertas()->max('urutan')) + 1;
        $added = 0;

        foreach ($rencana->tims as $tim) {
            $nama = trim((string) $tim->nama);
            if ($nama === '' || in_array(strtolower($nama), $existingNames, true)) {
                continue;
            }
            $peran = $tim->peran === 'pemandu' ? 'pemandu' : ($tim->peran === 'pengamat' ? 'tamu' : 'auditor');
            AuditDaftarHadirPeserta::create([
                'audit_daftar_hadir_id' => $dh->id,
                'nama'                  => $nama,
                'jabatan'               => $tim->jabatan,
                'unit_instansi'         => 'Tim Audit Internal',
                'peran'                 => $peran,
                'hadir'                 => true,
                'urutan'                => $urutan++,
            ]);
            $existingNames[] = strtolower($nama);
            $added++;
        }

        foreach ($rencana->pengesahans as $peng) {
            $nama = trim((string) ($peng->nama ?? ''));
            if ($nama === '' || in_array(strtolower($nama), $existingNames, true)) {
                continue;
            }
            AuditDaftarHadirPeserta::create([
                'audit_daftar_hadir_id' => $dh->id,
                'nama'                  => $nama,
                'jabatan'               => $peng->jabatan,
                'unit_instansi'         => $sesi->perusahaan?->nama_perusahaan ?? $sesi->area_audit,
                'peran'                 => 'auditi',
                'hadir'                 => true,
                'urutan'                => $urutan++,
            ]);
            $existingNames[] = strtolower($nama);
            $added++;
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Lampiran Daftar Hadir',
            'tindakan'        => "Prefill {$added} peserta Daftar Hadir {$jenis} dari Rencana Audit sesi #{$sesi->id}",
            'data_lama'       => null,
            'data_baru'       => ['added' => $added],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.laporan.lampiran', $sesi->id)
            ->with('success', "Berhasil menambahkan {$added} peserta dari Rencana Audit ke Daftar Hadir " . ucfirst($jenis) . '.');
    }

    /**
     * Halaman cetak A4 Daftar Hadir Pembukaan / Penutupan.
     */
    public function cetakDaftarHadir(int|string $id, string $jenis): View
    {
        if (! array_key_exists($jenis, AuditDaftarHadir::JENIS_OPTIONS)) {
            abort(404, 'Jenis daftar hadir tidak dikenali.');
        }

        $query = AuditSesi::with('perusahaan');
        if (auth()->check() && auth()->user()->role === 'auditor') {
            $userArea = auth()->user()->area;
            if (empty($userArea)) {
                abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun.');
            }
            $query->forUserArea($userArea);
        }

        $sesi = $query->findOrFail($id);
        $dh = AuditDaftarHadir::with('pesertas')
            ->where('audit_sesi_id', $sesi->id)
            ->where('jenis', $jenis)
            ->first();

        if (! $dh) {
            if (auth()->user()->role === 'auditor') {
                abort(404, 'Daftar hadir belum disusun.');
            }
            $dh = AuditDaftarHadir::create([
                'audit_sesi_id'  => $sesi->id,
                'jenis'          => $jenis,
                'tanggal'        => $jenis === 'pembukaan' ? $sesi->tanggal_mulai : $sesi->tanggal_selesai,
                'judul_kegiatan' => AuditDaftarHadir::JENIS_OPTIONS[$jenis],
            ]);
            $dh->load('pesertas');
        }

        return view('laporan.cetak_daftar_hadir', compact('sesi', 'dh'));
    }

    /**
     * Simpan Respon Manajemen terhadap Laporan Audit.
     */
    public function saveResponManajemen(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $rm = AuditResponManajemen::firstOrCreate(['audit_sesi_id' => $sesi->id]);

        $validated = $request->validate([
            'tanggal_respon'        => 'nullable|date',
            'nama_penandatangan'    => 'nullable|string|max:150',
            'jabatan_penandatangan' => 'nullable|string|max:150',
            'isi_respon'            => 'nullable|string|max:20000',
            'dokumen'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'hapus_dokumen'         => 'nullable|boolean',
        ], [
            'dokumen.mimes' => 'Dokumen respon manajemen harus berformat PDF, JPG, JPEG, atau PNG.',
            'dokumen.max'   => 'Ukuran dokumen respon manajemen maksimal 5 MB.',
        ]);

        $old = $rm->toArray();
        $newPath = $rm->dokumen_path;
        $oldToDelete = null;

        if ($request->boolean('hapus_dokumen') && $newPath) {
            $oldToDelete = $newPath;
            $newPath = null;
        }
        if ($request->hasFile('dokumen')) {
            if ($rm->dokumen_path) {
                $oldToDelete = $rm->dokumen_path;
            }
            $newPath = $request->file('dokumen')->store('respon_manajemen', 'attachments');
        }

        $rm->update([
            'tanggal_respon'        => $validated['tanggal_respon'] ?? null,
            'nama_penandatangan'    => isset($validated['nama_penandatangan']) && trim((string) $validated['nama_penandatangan']) !== '' ? trim((string) $validated['nama_penandatangan']) : null,
            'jabatan_penandatangan' => isset($validated['jabatan_penandatangan']) && trim((string) $validated['jabatan_penandatangan']) !== '' ? trim((string) $validated['jabatan_penandatangan']) : null,
            'isi_respon'            => isset($validated['isi_respon']) && trim((string) $validated['isi_respon']) !== '' ? trim((string) $validated['isi_respon']) : null,
            'dokumen_path'          => $newPath,
        ]);

        if ($oldToDelete && Storage::disk('attachments')->exists($oldToDelete)) {
            Storage::disk('attachments')->delete($oldToDelete);
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Lampiran Respon Manajemen',
            'tindakan'        => "Menyimpan Respon Manajemen sesi #{$sesi->id}",
            'data_lama'       => $old,
            'data_baru'       => $rm->fresh()->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.laporan.lampiran', $sesi->id)
            ->with('success', 'Respon Manajemen berhasil disimpan.');
    }

    /**
     * Prefill isi Respon Manajemen dari MasterTemplateLaporan (bagian = respon_manajemen).
     */
    public function prefillResponManajemen(int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);
        $tpl = MasterTemplateLaporan::where('bagian', 'respon_manajemen')->where('is_active', true)->first();

        if (! $tpl) {
            return redirect()->route('admin.audit-sesi.laporan.lampiran', $sesi->id)
                ->with('error', 'Template Respon Manajemen belum tersedia di Master Template Laporan.');
        }

        $context = $this->workflow->buildTemplateContext($laporan);
        $rendered = $this->renderer->renderPlain($tpl->isi_template, $context);

        $rm = AuditResponManajemen::firstOrCreate(['audit_sesi_id' => $sesi->id]);
        $rm->update(['isi_respon' => $rendered]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Lampiran Respon Manajemen',
            'tindakan'        => "Prefill isi Respon Manajemen dari template pada sesi #{$sesi->id}",
            'data_lama'       => null,
            'data_baru'       => ['isi_respon_length' => strlen($rendered)],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.laporan.lampiran', $sesi->id)
            ->with('success', 'Isi Respon Manajemen berhasil diisi dari template.');
    }

    /**
     * Tambah entri Hasil Audit IUJP (khusus perusahaan jenis_usaha tambang / pengolahan_pemurnian).
     */
    public function storeIujp(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::with('perusahaan')->findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);

        if (! $laporan->isEligibleForIujp()) {
            abort(422, 'Lampiran Hasil Audit IUJP hanya berlaku untuk perusahaan Pertambangan atau Pengolahan/Pemurnian.');
        }

        $validated = $request->validate([
            'sumber_audit_sesi_id'   => 'nullable|integer|exists:audit_sesis,id',
            'nama_perusahaan_iujp'   => 'required_without:sumber_audit_sesi_id|nullable|string|max:200',
            'nomor_iujp'             => 'nullable|string|max:150',
            'bidang_usaha'           => 'nullable|string|max:200',
            'tanggal_audit'          => 'nullable|date',
            'skor_pencapaian'        => 'nullable|numeric|min:0|max:100',
            'jumlah_temuan_kritikal' => 'nullable|integer|min:0|max:10000',
            'jumlah_temuan_mayor'    => 'nullable|integer|min:0|max:10000',
            'jumlah_temuan_minor'    => 'nullable|integer|min:0|max:10000',
            'ringkasan'              => 'nullable|string|max:5000',
            'dokumen'                => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'nama_perusahaan_iujp.required_without' => 'Nama perusahaan IUJP wajib diisi bila tidak menarik dari sesi audit.',
            'dokumen.mimes'                         => 'Dokumen hasil audit IUJP harus berformat PDF, JPG, JPEG, atau PNG.',
            'dokumen.max'                           => 'Ukuran dokumen hasil audit IUJP maksimal 5 MB.',
        ]);

        $payload = [
            'audit_laporan_id'       => $laporan->id,
            'sumber_audit_sesi_id'   => $validated['sumber_audit_sesi_id'] ?? null,
            'nama_perusahaan_iujp'   => trim((string) ($validated['nama_perusahaan_iujp'] ?? '')),
            'nomor_iujp'             => isset($validated['nomor_iujp']) && trim((string) $validated['nomor_iujp']) !== '' ? trim((string) $validated['nomor_iujp']) : null,
            'bidang_usaha'           => isset($validated['bidang_usaha']) && trim((string) $validated['bidang_usaha']) !== '' ? trim((string) $validated['bidang_usaha']) : null,
            'tanggal_audit'          => $validated['tanggal_audit'] ?? null,
            'skor_pencapaian'        => $validated['skor_pencapaian'] ?? null,
            'jumlah_temuan_kritikal' => (int) ($validated['jumlah_temuan_kritikal'] ?? 0),
            'jumlah_temuan_mayor'    => (int) ($validated['jumlah_temuan_mayor'] ?? 0),
            'jumlah_temuan_minor'    => (int) ($validated['jumlah_temuan_minor'] ?? 0),
            'ringkasan'              => isset($validated['ringkasan']) && trim((string) $validated['ringkasan']) !== '' ? trim((string) $validated['ringkasan']) : null,
        ];

        // Tarik otomatis dari sesi IUJP jika dipilih
        if (! empty($validated['sumber_audit_sesi_id'])) {
            $sumber = AuditSesi::with('perusahaan.legalitas')->findOrFail((int) $validated['sumber_audit_sesi_id']);
            $rekap = $sumber->getRekapPerElemen();
            $skor = round((float) array_sum(array_column($rekap, 'skor_elemen')), 2);
            $temuan = $sumber->getTemuanPerKategori();

            if ($payload['nama_perusahaan_iujp'] === '') {
                $payload['nama_perusahaan_iujp'] = (string) ($sumber->perusahaan?->nama_perusahaan ?? $sumber->area_audit);
            }
            $payload['nomor_iujp'] = $payload['nomor_iujp'] ?: $sumber->perusahaan?->legalitas?->nomor_sk_izin;
            $payload['tanggal_audit'] = $payload['tanggal_audit'] ?: ($sumber->tanggal_selesai ? $sumber->tanggal_selesai->format('Y-m-d') : null);
            $payload['skor_pencapaian'] = $skor > 0 ? $skor : (float) $sumber->skor_akhir;
            $payload['jumlah_temuan_kritikal'] = (int) ($temuan['kritikal_count'] ?? 0);
            $payload['jumlah_temuan_mayor'] = (int) ($temuan['mayor_count'] ?? 0);
            $payload['jumlah_temuan_minor'] = (int) ($temuan['minor_count'] ?? 0);
        }

        if ($request->hasFile('dokumen')) {
            $payload['dokumen_path'] = $request->file('dokumen')->store('laporan_iujp', 'attachments');
        }

        $iujp = AuditLaporanIujp::create($payload);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Lampiran Hasil Audit IUJP',
            'tindakan'        => "Menambahkan hasil audit IUJP '{$iujp->nama_perusahaan_iujp}' pada Laporan #{$laporan->id}",
            'data_lama'       => null,
            'data_baru'       => $iujp->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.laporan.lampiran', $sesi->id)
            ->with('success', "Hasil audit IUJP '{$iujp->nama_perusahaan_iujp}' berhasil ditambahkan.");
    }

    /**
     * Hapus (soft delete) entri hasil audit IUJP.
     */
    public function destroyIujp(int|string $id, int|string $iujpId): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::where('audit_sesi_id', $sesi->id)->firstOrFail();
        $iujp = AuditLaporanIujp::where('audit_laporan_id', $laporan->id)->findOrFail($iujpId);
        $old = $iujp->toArray();
        $nama = $iujp->nama_perusahaan_iujp;
        $iujp->delete();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Lampiran Hasil Audit IUJP',
            'tindakan'        => "Menghapus (Soft Delete) hasil audit IUJP '{$nama}' pada Laporan #{$laporan->id}",
            'data_lama'       => $old,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.laporan.lampiran', $sesi->id)
            ->with('success', "Hasil audit IUJP '{$nama}' berhasil dihapus.");
    }
}
