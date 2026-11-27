<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditDokumenAcuan;
use App\Models\AuditLaporan;
use App\Models\AuditLaporanDistribusi;
use App\Models\AuditLog;
use App\Models\AuditRencana;
use App\Models\AuditRencanaJadwal;
use App\Models\AuditSesi;
use App\Services\LaporanAuditWorkflowService;
use App\Services\RadarChartSvg;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class AuditLaporanController extends Controller
{
    public function __construct(protected LaporanAuditWorkflowService $workflow)
    {
    }

    /**
     * Halaman kelola Laporan Audit Internal SMKP (status, pengesahan, snapshot, distribusi, jadwal).
     */
    public function show(int|string $id): View
    {
        $sesi = AuditSesi::with(['perusahaan', 'user', 'rencana.jadwals', 'dokumenAcuans.pengunggah'])->findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);
        $laporan->load(['pengaju', 'peninjau', 'pengesah', 'snapshots.pembuat', 'distribusis.pencatat']);

        $statusSelesai = $laporan->auditDinyatakanSelesai();
        $integrity = $laporan->verifyLatestSnapshotIntegrity();
        $kelengkapanDokumen = $sesi->getKelengkapanDokumenWajib();
        $isReadOnly = false;

        return view('admin.audit-sesi.laporan', compact(
            'sesi',
            'laporan',
            'statusSelesai',
            'integrity',
            'kelengkapanDokumen',
            'isReadOnly'
        ));
    }

    /**
     * Halaman read-only Laporan Audit untuk role auditor (area-scoped).
     */
    public function showForAuditor(int|string $id): View
    {
        $userArea = auth()->user()->area;
        if (empty($userArea)) {
            abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun. Hubungi Administrator.');
        }

        $sesi = AuditSesi::with(['perusahaan', 'user', 'rencana.jadwals', 'dokumenAcuans.pengunggah'])
            ->forUserArea($userArea)
            ->findOrFail($id);

        $laporan = AuditLaporan::where('audit_sesi_id', $sesi->id)
            ->with(['pengaju', 'peninjau', 'pengesah', 'snapshots.pembuat', 'distribusis.pencatat'])
            ->first();

        if (! $laporan) {
            abort(404, 'Laporan Audit untuk sesi ini belum disusun.');
        }

        $statusSelesai = $laporan->auditDinyatakanSelesai();
        $integrity = $laporan->verifyLatestSnapshotIntegrity();
        $kelengkapanDokumen = $sesi->getKelengkapanDokumenWajib();
        $isReadOnly = true;

        return view('admin.audit-sesi.laporan', compact(
            'sesi',
            'laporan',
            'statusSelesai',
            'integrity',
            'kelengkapanDokumen',
            'isReadOnly'
        ));
    }

    /**
     * Simpan metadata laporan (hanya saat status draft).
     */
    public function updateMetadata(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);

        if ($laporan->status !== 'draft') {
            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('error', 'Metadata laporan hanya dapat diubah saat status Draft.');
        }

        $validated = $request->validate([
            'nomor_laporan'         => 'nullable|string|max:120',
            'tanggal_laporan'       => 'nullable|date',
            'tempat_penerbitan'     => 'nullable|string|max:150',
            'penanggung_jawab_nama' => 'nullable|string|max:150',
            'batas_waktu_pelaporan' => 'nullable|date',
        ]);

        $old = $laporan->toArray();
        $laporan->update([
            'nomor_laporan'         => isset($validated['nomor_laporan']) && trim((string) $validated['nomor_laporan']) !== '' ? trim((string) $validated['nomor_laporan']) : null,
            'tanggal_laporan'       => $validated['tanggal_laporan'] ?? null,
            'tempat_penerbitan'     => isset($validated['tempat_penerbitan']) && trim((string) $validated['tempat_penerbitan']) !== '' ? trim((string) $validated['tempat_penerbitan']) : null,
            'penanggung_jawab_nama' => isset($validated['penanggung_jawab_nama']) && trim((string) $validated['penanggung_jawab_nama']) !== '' ? trim((string) $validated['penanggung_jawab_nama']) : null,
            'batas_waktu_pelaporan' => $validated['batas_waktu_pelaporan'] ?? $laporan->batas_waktu_pelaporan,
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Laporan Audit',
            'tindakan'        => "Memperbarui metadata Laporan Audit #{$laporan->id} (sesi #{$sesi->id})",
            'data_lama'       => $old,
            'data_baru'       => $laporan->fresh()->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
            ->with('success', 'Metadata laporan berhasil disimpan.');
    }

    /**
     * Ajukan tinjauan (draft -> ditinjau).
     */
    public function submitReview(int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);

        try {
            $this->workflow->ajukanTinjauan($laporan, auth()->user());

            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('success', 'Laporan Audit berhasil diajukan untuk ditinjau.');
        } catch (InvalidArgumentException $e) {
            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Sahkan laporan (ditinjau -> disahkan) & buat snapshot SHA-256.
     */
    public function approve(int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);

        try {
            $snapshot = $this->workflow->sahkan($laporan, auth()->user());

            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('success', 'Laporan Audit berhasil disahkan dan snapshot SHA-256 (' . substr($snapshot->hash_sha256, 0, 12) . '...) telah dikunci.');
        } catch (InvalidArgumentException $e) {
            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Catat distribusi laporan (disahkan -> didistribusikan).
     */
    public function storeDistribusi(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);

        $validated = $request->validate([
            'penerima_nama'      => 'required|string|max:150',
            'penerima_jabatan'   => 'nullable|string|max:150',
            'penerima_instansi'  => 'nullable|string|max:200',
            'jenis_penerima'     => ['required', Rule::in(array_keys(AuditLaporanDistribusi::JENIS_PENERIMA_OPTIONS))],
            'tanggal_distribusi' => 'required|date',
            'metode'             => ['required', Rule::in(array_keys(AuditLaporanDistribusi::METODE_OPTIONS))],
            'nomor_tanda_terima' => 'nullable|string|max:100',
            'catatan'            => 'nullable|string|max:2000',
            'bukti'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'penerima_nama.required'      => 'Nama penerima distribusi wajib diisi.',
            'jenis_penerima.required'     => 'Jenis penerima wajib dipilih.',
            'tanggal_distribusi.required' => 'Tanggal distribusi wajib diisi.',
            'metode.required'             => 'Metode distribusi wajib dipilih.',
            'bukti.mimes'                 => 'Berkas bukti distribusi harus berformat PDF, JPG, JPEG, atau PNG.',
            'bukti.max'                   => 'Ukuran berkas bukti distribusi maksimal 5 MB.',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('distribusi_laporan', 'attachments');
        }

        try {
            $this->workflow->catatDistribusi($laporan, [
                'penerima_nama'      => trim($validated['penerima_nama']),
                'penerima_jabatan'   => isset($validated['penerima_jabatan']) && trim((string) $validated['penerima_jabatan']) !== '' ? trim((string) $validated['penerima_jabatan']) : null,
                'penerima_instansi'  => isset($validated['penerima_instansi']) && trim((string) $validated['penerima_instansi']) !== '' ? trim((string) $validated['penerima_instansi']) : null,
                'jenis_penerima'     => $validated['jenis_penerima'],
                'tanggal_distribusi' => $validated['tanggal_distribusi'],
                'metode'             => $validated['metode'],
                'nomor_tanda_terima' => isset($validated['nomor_tanda_terima']) && trim((string) $validated['nomor_tanda_terima']) !== '' ? trim((string) $validated['nomor_tanda_terima']) : null,
                'catatan'            => isset($validated['catatan']) && trim((string) $validated['catatan']) !== '' ? trim((string) $validated['catatan']) : null,
                'bukti_path'         => $buktiPath,
            ], auth()->user());

            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('success', 'Catatan distribusi laporan berhasil disimpan.');
        } catch (InvalidArgumentException $e) {
            if ($buktiPath && Storage::disk('attachments')->exists($buktiPath)) {
                Storage::disk('attachments')->delete($buktiPath);
            }

            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Hapus (soft delete) catatan distribusi.
     */
    public function destroyDistribusi(int|string $id, int|string $distId): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::where('audit_sesi_id', $sesi->id)->firstOrFail();
        $dist = AuditLaporanDistribusi::where('audit_laporan_id', $laporan->id)->findOrFail($distId);
        $old = $dist->toArray();
        $dist->delete();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Distribusi Laporan Audit',
            'tindakan'        => "Menghapus (Soft Delete) catatan distribusi #{$dist->id} pada Laporan Audit #{$laporan->id}",
            'data_lama'       => $old,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
            ->with('success', 'Catatan distribusi berhasil dihapus.');
    }

    /**
     * Tandai laporan telah disampaikan/dilaporkan ke instansi (KaIT / ESDM).
     */
    public function markReported(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);

        $validated = $request->validate([
            'tanggal_dilaporkan' => 'required|date',
        ], [
            'tanggal_dilaporkan.required' => 'Tanggal penyampaian laporan wajib diisi.',
        ]);

        try {
            $this->workflow->catatPelaporan($laporan, $validated['tanggal_dilaporkan'], auth()->user());

            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('success', 'Status laporan diperbarui menjadi Dilaporkan.');
        } catch (InvalidArgumentException $e) {
            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Buka revisi laporan (khusus Administrator).
     */
    public function reopenRevision(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);

        $validated = $request->validate([
            'catatan_revisi' => 'required|string|max:2000',
        ], [
            'catatan_revisi.required' => 'Catatan alasan revisi wajib diisi.',
        ]);

        try {
            $updated = $this->workflow->bukaRevisi($laporan, trim($validated['catatan_revisi']), auth()->user());

            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('success', "Revisi ke-{$updated->revisi_ke} dibuka. Laporan kembali ke status Draft dan snapshot sebelumnya tetap tersimpan.");
        } catch (InvalidArgumentException $e) {
            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Perbarui status_pelaksanaan tiap baris jadwal pada Rencana Audit sesi.
     */
    public function updateJadwalStatus(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $rencana = AuditRencana::where('audit_sesi_id', $sesi->id)->first();

        if (! $rencana) {
            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('error', 'Rencana Audit belum disusun untuk sesi ini.');
        }

        $validated = $request->validate([
            'statuses'   => 'required|array',
            'statuses.*' => ['required', Rule::in(array_keys(AuditRencanaJadwal::STATUS_PELAKSANAAN_OPTIONS))],
        ]);

        $updatedCount = 0;
        foreach ($validated['statuses'] as $jadwalId => $newStatus) {
            $jadwal = AuditRencanaJadwal::where('audit_rencana_id', $rencana->id)->find((int) $jadwalId);
            if ($jadwal && $jadwal->status_pelaksanaan !== $newStatus) {
                $jadwal->update(['status_pelaksanaan' => $newStatus]);
                $updatedCount++;
            }
        }

        if ($updatedCount > 0) {
            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Laporan Audit',
                'tindakan'        => "Memperbarui status pelaksanaan {$updatedCount} kegiatan jadwal pada sesi #{$sesi->id}",
                'data_lama'       => null,
                'data_baru'       => $validated['statuses'],
                'waktu_perubahan' => now(),
            ]);
        }

        return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
            ->with('success', "Status pelaksanaan jadwal ({$updatedCount} perubahan) berhasil disimpan.");
    }

    /**
     * Simpan penyesuaian teks narasi (teks_override) — hanya diperbolehkan saat status Draft.
     */
    public function updateTeksOverride(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);

        if ($laporan->status !== 'draft') {
            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('error', 'Penyesuaian narasi hanya dapat diubah saat laporan berstatus Draft.');
        }

        $validated = $request->validate([
            'teks_override'                => 'nullable|array',
            'teks_override.latar_belakang' => 'nullable|string|max:20000',
            'teks_override.tujuan_audit'   => 'nullable|string|max:20000',
            'teks_override.penutup'        => 'nullable|string|max:20000',
        ]);

        $old = $laporan->teks_override;
        $cleaned = [];
        foreach (['latar_belakang', 'tujuan_audit', 'penutup'] as $key) {
            $val = trim((string) ($validated['teks_override'][$key] ?? ''));
            if ($val !== '') {
                $cleaned[$key] = $val;
            }
        }

        $laporan->update(['teks_override' => ! empty($cleaned) ? $cleaned : null]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Laporan Audit',
            'tindakan'        => "Memperbarui penyesuaian narasi (teks_override) Laporan Audit #{$laporan->id}",
            'data_lama'       => $old,
            'data_baru'       => $cleaned,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
            ->with('success', 'Penyesuaian narasi laporan berhasil disimpan.');
    }

    /**
     * Verifikasi integritas hash SHA-256 snapshot terbaru.
     */
    public function verifyHash(int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $laporan = AuditLaporan::getOrCreateForSesi($sesi);
        $result = $laporan->verifyLatestSnapshotIntegrity();

        if (! $result['has_snapshot']) {
            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('info', 'Belum ada snapshot pengesahan untuk diverifikasi.');
        }

        if ($result['valid']) {
            return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
                ->with('success', 'Verifikasi SHA-256 VALID (Revisi #' . $result['revisi_ke'] . '): ' . $result['hash_stored']);
        }

        return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
            ->with('error', 'PERINGATAN INTEGRITAS: Hash SHA-256 snapshot tidak cocok dengan payload tersimpan!');
    }

    /**
     * Halaman cetak Laporan Audit Internal SMKP (A4 Portrait).
     * - Sebelum disahkan: dirender dari data hidup + watermark DRAFT.
     * - Setelah disahkan: dirender sepenuhnya dari snapshot JSON tersimpan.
     */
    public function cetak(int|string $id, RadarChartSvg $radarSvg): View
    {
        $query = AuditSesi::with(['perusahaan.legalitas', 'user']);

        if (auth()->check() && auth()->user()->role === 'auditor') {
            $userArea = auth()->user()->area;
            if (empty($userArea)) {
                abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun. Hubungi Administrator.');
            }
            $query->forUserArea($userArea);
        }

        $sesi = $query->findOrFail($id);
        $laporan = AuditLaporan::where('audit_sesi_id', $sesi->id)->first();

        if (! $laporan) {
            if (auth()->user()->role === 'auditor') {
                abort(404, 'Laporan Audit untuk sesi ini belum disusun.');
            }
            $laporan = AuditLaporan::getOrCreateForSesi($sesi);
        }

        $isFromSnapshot = false;
        $snapshot = null;
        $integrity = null;

        if ($laporan->isDisahkanAtauLebih()) {
            $snapshot = $laporan->latestSnapshot()->first();
            if ($snapshot) {
                $payload = $snapshot->payload;
                $isFromSnapshot = true;
                $integrity = $laporan->verifyLatestSnapshotIntegrity();
            } else {
                $payload = $this->workflow->buildCanonicalSnapshotPayload($laporan);
            }
        } else {
            $payload = $this->workflow->buildCanonicalSnapshotPayload($laporan);
        }

        $rekapElemen = $payload['pencapaian']['rekap_elemen'] ?? [];
        $radarChartSvg = $radarSvg->render($rekapElemen);

        return view('laporan.cetak_laporan_audit', compact(
            'sesi',
            'laporan',
            'payload',
            'isFromSnapshot',
            'snapshot',
            'integrity',
            'radarChartSvg'
        ));
    }

    /**
     * Unggah Dokumen Acuan Wajib (RKAB, SOP Audit, Manual SMKP, Dokumen Lainnya).
     */
    public function storeDokumenAcuan(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);

        $validated = $request->validate([
            'jenis_dokumen'   => ['required', Rule::in(array_keys(AuditDokumenAcuan::JENIS_OPTIONS))],
            'judul'           => 'required|string|max:200',
            'nomor_dokumen'   => 'nullable|string|max:100',
            'tanggal_dokumen' => 'nullable|date',
            'catatan'         => 'nullable|string|max:2000',
            'berkas'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'jenis_dokumen.required' => 'Jenis dokumen wajib dipilih.',
            'judul.required'         => 'Judul dokumen acuan wajib diisi.',
            'berkas.mimes'           => 'Berkas dokumen acuan harus berformat PDF, JPG, JPEG, atau PNG.',
            'berkas.max'             => 'Ukuran berkas dokumen acuan maksimal 5 MB.',
        ]);

        $filePath = null;
        if ($request->hasFile('berkas')) {
            $filePath = $request->file('berkas')->store('dokumen_acuan', 'attachments');
        }

        $doc = AuditDokumenAcuan::create([
            'audit_sesi_id'   => $sesi->id,
            'jenis_dokumen'   => $validated['jenis_dokumen'],
            'judul'           => trim($validated['judul']),
            'nomor_dokumen'   => isset($validated['nomor_dokumen']) && trim((string) $validated['nomor_dokumen']) !== '' ? trim((string) $validated['nomor_dokumen']) : null,
            'tanggal_dokumen' => $validated['tanggal_dokumen'] ?? null,
            'file_path'       => $filePath,
            'catatan'         => isset($validated['catatan']) && trim((string) $validated['catatan']) !== '' ? trim((string) $validated['catatan']) : null,
            'diunggah_oleh'   => auth()->id(),
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Dokumen Acuan Audit',
            'tindakan'        => "Menambahkan dokumen acuan ({$doc->jenis_dokumen}): {$doc->judul} pada sesi #{$sesi->id}",
            'data_lama'       => null,
            'data_baru'       => $doc->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
            ->with('success', "Dokumen acuan '{$doc->judul}' berhasil disimpan.");
    }

    /**
     * Hapus (soft delete) Dokumen Acuan Wajib.
     */
    public function destroyDokumenAcuan(int|string $id, int|string $docId): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $doc = AuditDokumenAcuan::where('audit_sesi_id', $sesi->id)->findOrFail($docId);
        $old = $doc->toArray();
        $judul = $doc->judul;
        $doc->delete();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Dokumen Acuan Audit',
            'tindakan'        => "Menghapus (Soft Delete) dokumen acuan '{$judul}' pada sesi #{$sesi->id}",
            'data_lama'       => $old,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.laporan.show', $sesi->id)
            ->with('success', "Dokumen acuan '{$judul}' berhasil dihapus.");
    }

    /**
     * Menu Laporan Audit untuk role Auditor: HANYA menampilkan laporan berstatus disahkan/didistribusikan/dilaporkan
     * pada area kerja auditor yang login.
     */
    public function indexForAuditor(): View
    {
        $userArea = auth()->user()->area;
        if (empty($userArea)) {
            abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun. Hubungi Administrator.');
        }

        $laporans = AuditLaporan::with(['auditSesi.perusahaan', 'latestSnapshot'])
            ->whereIn('status', ['disahkan', 'didistribusikan', 'dilaporkan'])
            ->whereHas('auditSesi', fn ($q) => $q->forUserArea($userArea))
            ->orderByDesc('tanggal_pengesahan')
            ->orderByDesc('id')
            ->get();

        return view('auditor.laporan.index', compact('laporans'));
    }
}
