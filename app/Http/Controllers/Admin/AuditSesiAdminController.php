<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AuditSesiExport;
use App\Http\Controllers\Controller;
use App\Models\AuditDetail;
use App\Models\AuditLog;
use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Perusahaan;
use App\Models\Pica;
use App\Services\GatingRuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AuditSesiAdminController extends Controller
{
    /**
     * Display listing of all audit sessions for Administrator.
     */
    public function index(Request $request)
    {
        $query = AuditSesi::with(['user', 'perusahaan'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('perusahaan_id')) {
            $query->where('perusahaan_id', $request->perusahaan_id);
        } elseif ($request->filled('area_selection')) {
            $sel = $request->area_selection;
            $pId = str_starts_with($sel, 'p:') ? substr($sel, 2) : $sel;
            $query->where('perusahaan_id', $pId);
        }

        $auditSesis = $query->paginate(10);
        $trashedSesis = AuditSesi::onlyTrashed()->with(['user', 'perusahaan'])->latest()->get();
        $perusahaans = Perusahaan::where('is_active', true)->orderBy('nama_perusahaan')->get();

        return view('admin.audit-sesi.index', compact('auditSesis', 'trashedSesis', 'perusahaans'));
    }

    /**
     * Show the form for creating a new audit session.
     */
    public function create()
    {
        $perusahaans = Perusahaan::where('is_active', true)->orderBy('nama_perusahaan')->get();
        return view('admin.audit-sesi.create', compact('perusahaans'));
    }

    /**
     * Store a newly created audit session.
     */
    public function store(Request $request)
    {
        // Support both perusahaan_id directly or area_selection
        if ($request->filled('area_selection') && !$request->filled('perusahaan_id')) {
            $sel = $request->area_selection;
            $request->merge([
                'perusahaan_id' => str_starts_with($sel, 'p:') ? substr($sel, 2) : $sel,
            ]);
        }

        $request->validate([
            'perusahaan_id'   => 'required|exists:perusahaans,id',
            'tahun_periode'   => 'required|integer|min:2000|max:2100',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'perusahaan_id.required'          => 'Perusahaan area audit wajib dipilih.',
            'perusahaan_id.exists'            => 'Perusahaan yang dipilih tidak valid.',
            'tahun_periode.required'          => 'Tahun periode audit wajib dipilih.',
            'tahun_periode.integer'           => 'Tahun periode audit harus berupa angka tahun.',
            'tanggal_mulai.required'          => 'Tanggal mulai sesi wajib diisi.',
            'tanggal_selesai.required'        => 'Tanggal selesai sesi wajib diisi.',
            'tanggal_selesai.after_or_equal'  => 'Tanggal selesai sesi harus sama atau setelah tanggal mulai.',
        ]);

        $perusahaan = Perusahaan::findOrFail($request->perusahaan_id);
        $areaAudit = $perusahaan->nama_perusahaan;

        DB::beginTransaction();
        try {
            $sesi = AuditSesi::create([
                'user_id'         => auth()->id(),
                'perusahaan_id'   => $perusahaan->id,
                'tahun_periode'   => (int) $request->tahun_periode,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'area_audit'      => $areaAudit,
                'status'          => 'draft',
                'skor_akhir'      => 0,
            ]);

            $subElemens = \App\Models\SubElemen::all();
            foreach ($subElemens as $sub) {
                $sub->syncDefaultKriteria();
            }

            $kriterias = Kriteria::with('subElemen')->get();
            foreach ($kriterias as $kriteria) {
                $isNaDefault = (bool) ($kriteria->is_na || ($kriteria->subElemen && $kriteria->subElemen->is_na));
                AuditDetail::create([
                    'audit_sesi_id' => $sesi->id,
                    'kriteria_id'   => $kriteria->id,
                    'nilai'         => 0,
                    'is_na'         => $isNaDefault,
                    'catatan'       => null,
                    'lampiran'      => null,
                ]);
            }

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Sesi Audit',
                'tindakan'        => "Membuat sesi audit baru: {$sesi->area_audit}",
                'data_lama'       => null,
                'data_baru'       => $sesi->toArray(),
                'waktu_perubahan' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.audit-sesi.matrix', $sesi->id)
                ->with('success', 'Sesi audit baru berhasil dibuat! Silakan isi matriks penilaian.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat sesi audit: ' . $e->getMessage());
        }
    }

    /**
     * Display matrix scoring view for admin.
     */
    public function matrix($id)
    {
        $sesi = AuditSesi::with('user')->findOrFail($id);

        $subElemens = \App\Models\SubElemen::all();
        foreach ($subElemens as $sub) {
            $sub->syncDefaultKriteria();
        }

        $existingDetailKriteriaIds = AuditDetail::where('audit_sesi_id', $sesi->id)->pluck('kriteria_id')->toArray();
        $missingKriterias = Kriteria::whereNotIn('id', $existingDetailKriteriaIds)->get();
        foreach ($missingKriterias as $k) {
            $isNaDefault = (bool) ($k->is_na || ($k->subElemen && $k->subElemen->is_na));
            AuditDetail::create([
                'audit_sesi_id' => $sesi->id,
                'kriteria_id'   => $k->id,
                'nilai'         => 0,
                'is_na'         => $isNaDefault,
                'catatan'       => null,
                'lampiran'      => null,
            ]);
        }

        $elemens = Elemen::with(['subElemens.kriterias' => function ($query) use ($sesi) {
            $query->with(['auditDetails' => function ($q) use ($sesi) {
                $q->where('audit_sesi_id', $sesi->id);
            }]);
        }])->orderBy('kode_elemen')->get();

        $rekap = $sesi->getRekapPerElemen();
        $gatingRules = \App\Models\KriteriaGatingRule::active()->with(['kriteriaHulu', 'kriteriaHilir'])->get();
        $gatingViolations = app(GatingRuleService::class)->evaluate($sesi);

        return view('auditor.audit.matrix', compact('sesi', 'elemens', 'rekap', 'gatingRules', 'gatingViolations'));
    }

    /**
     * Update matrix scores with AuditLog tracking.
     */
    public function updateMatrix(Request $request, $id)
    {
        $sesi = AuditSesi::findOrFail($id);

        if ($sesi->status === 'selesai') {
            return back()->with('error', 'Sesi audit ini telah difinalisasi dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'details'                  => 'required|array',
            'details.*.nilai'          => 'nullable|integer|min:0',
            'details.*.is_na'          => 'nullable|boolean',
            'details.*.catatan'        => 'nullable',
            'details.*.catatans'       => 'nullable|array',
            'details.*.catatans.*'     => 'nullable|string',
            'details.*.lampiran'       => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'details.*.lampirans'      => 'nullable|array',
            'details.*.lampirans.*'    => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'details.*.hapus_lampiran' => 'nullable|array',
        ]);

        // Cross-Check / Scoring Gating Validation
        $gatingService = app(GatingRuleService::class);
        $hardBlockMessages = $gatingService->getHardBlockMessages($sesi, $request->details);

        if (!empty($hardBlockMessages)) {
            return back()->withInput()->with('error', 'Penyimpanan ditolak karena melanggar aturan Cross-Check / Gating Logic: ' . implode(' | ', $hardBlockMessages));
        }

        $softFlagWarnings = $gatingService->getSoftFlagWarnings($sesi, $request->details);

        DB::beginTransaction();
        try {
            $changedDetails = [];

            // Preload all details for this session with kriteria & pica to avoid N+1 queries in loop
            $existingDetails = AuditDetail::where('audit_sesi_id', $sesi->id)
                ->with(['kriteria', 'pica'])
                ->get()
                ->keyBy('id');

            foreach ($request->details as $detailId => $data) {
                $detail = $existingDetails->get($detailId);
                if ($detail) {
                    $originalValues = $detail->getOriginal();

                    $isNa  = isset($data['is_na']) && $data['is_na'] == 1;
                    $nilai = $isNa ? 0 : (int) ($data['nilai'] ?? 0);
                    $max   = $detail->kriteria ? (int) $detail->kriteria->nilai_maksimal : 4;

                    if ($nilai > $max) {
                        $nilai = $max;
                    }

                    // Collect catatans (supports both array and single string)
                    $catatanList = [];
                    if (!empty($data['catatans']) && is_array($data['catatans'])) {
                        foreach ($data['catatans'] as $c) {
                            if (is_string($c) && trim($c) !== '') {
                                $catatanList[] = trim($c);
                            }
                        }
                    } elseif (isset($data['catatan']) && is_string($data['catatan']) && trim($data['catatan']) !== '') {
                        $catatanList[] = trim($data['catatan']);
                    }

                    $catatanFinal = null;
                    if (count($catatanList) === 1) {
                        $catatanFinal = $catatanList[0];
                    } elseif (count($catatanList) > 1) {
                        $formattedLines = [];
                        foreach ($catatanList as $idx => $txt) {
                            $formattedLines[] = ($idx + 1) . '. ' . $txt;
                        }
                        $catatanFinal = implode("\n", $formattedLines);
                    }

                    // Existing Lampirans
                    $existingLampirans = $detail->lampiran_array;
                    if (!empty($data['hapus_lampiran']) && is_array($data['hapus_lampiran'])) {
                        foreach ($data['hapus_lampiran'] as $delPath) {
                            if (Storage::disk('public')->exists($delPath)) {
                                Storage::disk('public')->delete($delPath);
                            }
                            $existingLampirans = array_diff($existingLampirans, [$delPath]);
                        }
                    }

                    // Upload new Lampirans
                    if (!empty($data['lampirans']) && is_array($data['lampirans'])) {
                        foreach ($data['lampirans'] as $file) {
                            if ($file && $file->isValid()) {
                                $existingLampirans[] = $file->store('lampiran', 'public');
                            }
                        }
                    } elseif (isset($data['lampiran']) && $data['lampiran'] instanceof \Illuminate\Http\UploadedFile && $data['lampiran']->isValid()) {
                        $existingLampirans[] = $data['lampiran']->store('lampiran', 'public');
                    }

                    $existingLampirans = array_values(array_unique(array_filter($existingLampirans)));
                    $lampiranFinal = null;
                    if (count($existingLampirans) === 1) {
                        $lampiranFinal = $existingLampirans[0];
                    } elseif (count($existingLampirans) > 1) {
                        $lampiranFinal = json_encode($existingLampirans);
                    }

                    $updatePayload = [
                        'nilai'    => $nilai,
                        'is_na'    => $isNa,
                        'catatan'  => $catatanFinal,
                        'lampiran' => $lampiranFinal,
                    ];

                    $detail->update($updatePayload);

                    if ($detail->wasChanged()) {
                        $changedDetails[$detailId] = [
                            'lama' => $originalValues,
                            'baru' => $detail->getChanges(),
                        ];
                    }

                    // PICA Auto-Trigger & Clean Logic
                    if (!$isNa && $nilai < $max) {
                        $pica            = $detail->pica;
                        $deskripsiTemuan = !empty($data['catatan'])
                            ? $data['catatan']
                            : ($detail->catatan ?? 'Ketidaksesuaian kriteria ' . ($detail->kriteria ? $detail->kriteria->kode_kriteria : '') . ' (Skor ' . $nilai . ' / ' . $max . ')');

                        if (!$pica) {
                            Pica::create([
                                'audit_detail_id'  => $detail->id,
                                'deskripsi_temuan' => $deskripsiTemuan,
                                'status'           => 'open',
                            ]);
                        } else {
                            $pica->update(['deskripsi_temuan' => $deskripsiTemuan]);
                        }
                    } else {
                        $pica = Pica::where('audit_detail_id', $detail->id)->first();
                        if ($pica && $pica->status === 'open' && empty($pica->akar_masalah)) {
                            $pica->delete();
                        }
                    }
                }
            }

            if ($sesi->status === 'draft') {
                $sesi->status = 'berjalan';
            }

            // Refresh session details to ensure rekap calculation reflects updated values
            $sesi->unsetRelation('auditDetails');

            // Tahap 2: Auto-Klasifikasi PICA (Jalur Mayor #1) per Sub-Elemen
            // Dilakukan setelah seluruh detail ter-update agar persentase sub-elemen akurat
            $subElemenIds = Pica::whereHas('auditDetail', function ($q) use ($sesi) {
                    $q->where('audit_sesi_id', $sesi->id);
                })
                ->whereIn('status', ['open', 'in_progress'])
                ->where('kategori_ditetapkan_manual', false)
                ->get()
                ->pluck('auditDetail.kriteria.sub_elemen_id')
                ->filter()
                ->unique();

            foreach ($subElemenIds as $subElemenId) {
                $kategoriMayor = $sesi->hitungKategoriMayorPath1($subElemenId);

                Pica::whereHas('auditDetail.kriteria', fn ($q) => $q->where('sub_elemen_id', $subElemenId))
                    ->whereHas('auditDetail', fn ($q) => $q->where('audit_sesi_id', $sesi->id))
                    ->where('kategori_ditetapkan_manual', false)
                    ->update(['kategori_temuan' => $kategoriMayor]);
            }

            $sesi->hitungSkorAkhir();
            DB::commit();

            if (!empty($changedDetails)) {
                AuditLog::create([
                    'user_id'         => auth()->id(),
                    'modul'           => 'Sesi Audit',
                    'tindakan'        => "Update matriks penilaian: {$sesi->area_audit}",
                    'data_lama'       => ['details' => array_column($changedDetails, 'lama')],
                    'data_baru'       => ['details' => array_column($changedDetails, 'baru')],
                    'waktu_perubahan' => now(),
                ]);
            }

            if ($request->has('save_and_rekap')) {
                $redirect = redirect()->route('admin.audit-sesi.rekap', $sesi->id)
                    ->with('success', 'Matriks penilaian berhasil disimpan!');
                if (!empty($softFlagWarnings)) {
                    $redirect->with('warning', 'Peringatan Inkonsistensi Gating: ' . implode(' | ', $softFlagWarnings));
                }
                return $redirect;
            }

            $back = back()->with('success', 'Matriks penilaian audit berhasil diperbarui!');
            if (!empty($softFlagWarnings)) {
                $back->with('warning', 'Peringatan Inkonsistensi Gating: ' . implode(' | ', $softFlagWarnings));
            }
            return $back;
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display audit summary rekap for admin.
     */
    public function rekap($id)
    {
        $sesi      = AuditSesi::with('user')->findOrFail($id);
        $rekap     = $sesi->getRekapPerElemen();
        $hierarki  = $sesi->getRekapHierarkis();
        $skorAkhir = $sesi->hitungSkorAkhir();
        $gatingViolations = app(GatingRuleService::class)->evaluate($sesi);

        return view('admin.audit-sesi.rekap', compact('sesi', 'rekap', 'hierarki', 'skorAkhir', 'gatingViolations'));
    }

    /**
     * Display printable report.
     */
    public function cetak($id)
    {
        $sesi = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen', 'perusahaan'])->findOrFail($id);
        $rekap     = $sesi->getRekapPerElemen();
        $hierarki  = $sesi->getRekapHierarkis();
        $skorAkhir = $sesi->hitungSkorAkhir();

        return view('auditor.audit.cetak', compact('sesi', 'rekap', 'hierarki', 'skorAkhir'));
    }

    /**
     * Export to Excel.
     */
    public function exportExcel($id)
    {
        $sesi = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen', 'perusahaan'])->findOrFail($id);
        $safeArea = preg_replace('/[^A-Za-z0-9_\-]/', '_', $sesi->area_audit);
        $fileName = 'TT-MGT-FRS-026B_Audit_SMKP_' . $safeArea . '_' . ($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y-m-d') : date('Y-m-d')) . '.xlsx';

        return AuditSesiExport::downloadTemplateWithScores($sesi, $fileName);
    }

    /**
     * Display comprehensive audit detail report (Tree Matrix, Best Practices, PICA findings).
     */
    public function laporanDetail($id)
    {
        $sesi           = AuditSesi::with(['user', 'perusahaan', 'auditDetails.kriteria.subElemen.elemen'])->findOrFail($id);
        $rekapElemen    = $sesi->getRekapPerElemen();
        $hierarki       = $sesi->buildMatrixTree();
        $praktekBaik    = $sesi->getSubElemenPraktekTerbaik();
        $temuanKategori = $sesi->getTemuanPerKategori();
        $skorAkhir      = $sesi->hitungSkorAkhir();
        $isReadOnly     = false;

        return view('laporan.detail', compact('sesi', 'rekapElemen', 'hierarki', 'praktekBaik', 'temuanKategori', 'skorAkhir', 'isReadOnly'));
    }

    /**
     * Finalize audit session with AuditLog.
     */
    public function finalisasi($id)
    {
        $sesi = AuditSesi::findOrFail($id);

        if ($sesi->status === 'selesai') {
            return back()->with('info', 'Sesi audit ini sudah dalam status selesai.');
        }

        $originalStatus = $sesi->status;
        $sesi->hitungSkorAkhir();
        $sesi->status = 'selesai';
        $sesi->save();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Sesi Audit',
            'tindakan'        => "Finalisasi sesi audit: {$sesi->area_audit} (skor: {$sesi->skor_akhir}%)",
            'data_lama'       => ['status' => $originalStatus],
            'data_baru'       => ['status' => 'selesai', 'skor_akhir' => $sesi->skor_akhir],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.rekap', $sesi->id)
            ->with('success', 'Sesi audit berhasil difinalisasi! Data telah terkunci.');
    }

    /**
     * Soft delete audit session (draft/berjalan only).
     */
    public function destroy($id)
    {
        $sesi = AuditSesi::findOrFail($id);
        $sesiData = $sesi->toArray();

        if ($sesi->status === 'selesai') {
            return back()->with('error', 'Sesi audit yang sudah selesai tidak dapat dihapus.');
        }

        $sesi->delete();

        \App\Models\AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Sesi Audit',
            'tindakan'        => "Menonaktifkan (Soft Delete) Sesi Audit: {$sesiData['area_audit']} ({$sesiData['tanggal_mulai']} s/d {$sesiData['tanggal_selesai']})",
            'data_lama'       => $sesiData,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.index')
            ->with('success', 'Sesi audit berhasil dipindahkan ke kotak sampah (Soft Delete).');
    }

    /**
     * Restore soft-deleted audit session.
     */
    public function restore($id)
    {
        $sesi = AuditSesi::onlyTrashed()->findOrFail($id);
        $sesi->restore();

        \App\Models\AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Sesi Audit',
            'tindakan'        => "Memulihkan (Restore Point) Sesi Audit: {$sesi->area_audit}",
            'data_lama'       => null,
            'data_baru'       => $sesi->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.index')
            ->with('success', 'Sesi audit berhasil diaktifkan kembali!');
    }

    /**
     * Permanently delete audit session and its details.
     */
    public function forceDelete($id)
    {
        $sesi = AuditSesi::onlyTrashed()->findOrFail($id);
        $sesiData = $sesi->toArray();

        try {
            $sesi->forceDelete();

            \App\Models\AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Sesi Audit',
                'tindakan'        => "Menghapus Permanen (Force Delete) Sesi Audit: {$sesiData['area_audit']}",
                'data_lama'       => $sesiData,
                'data_baru'       => null,
                'waktu_perubahan' => now(),
            ]);

            return redirect()->route('admin.audit-sesi.index')
                ->with('success', 'Sesi audit berhasil dihapus secara permanen!');
        } catch (\Exception $e) {
            return redirect()->route('admin.audit-sesi.index')
                ->with('error', 'Gagal menghapus permanen sesi audit: ' . $e->getMessage());
        }
    }
}
