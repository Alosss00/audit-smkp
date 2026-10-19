<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditSesi;
use App\Models\Pica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PicaController extends Controller
{
    /**
     * Display global listing of all PICA items grouped by Audit Session for Administrator oversight.
     */
    public function index(Request $request)
    {
        $query = AuditSesi::whereHas('auditDetails.pica')
            ->with([
                'user',
                'perusahaan',
                'auditDetails' => function ($q) {
                    $q->whereHas('pica');
                },
                'auditDetails.pica',
                'auditDetails.kriteria.subElemen.elemen',
                'auditDetails.kriteria.subElemen.kriterias'
            ]);

        // Filter by Perusahaan (perusahaan_id)
        if ($request->filled('perusahaan_id')) {
            $query->where('perusahaan_id', $request->perusahaan_id);
        }

        // Filter by Tahun Periode
        if ($request->filled('tahun_periode')) {
            $query->where('tahun_periode', $request->tahun_periode);
        }

        // Filter by Status (sessions having at least one PICA with matching status)
        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('auditDetails.pica', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        // Filter by Auditor (user_id)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Search Query (area_audit or deskripsi_temuan / akar_masalah / auditor name / perusahaan name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('area_audit', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('perusahaan', function ($qp) use ($search) {
                      $qp->where('nama_perusahaan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('auditDetails.pica', function ($qp) use ($search) {
                      $qp->where('deskripsi_temuan', 'like', "%{$search}%")
                        ->orWhere('akar_masalah', 'like', "%{$search}%");
                  });
            });
        }

        $auditSesis = $query->latest()->paginate(10)->withQueryString();

        // Stats Summary (Agregasi berbasis Sub-Elemen per Sesi Audit)
        $picaQuery = Pica::with(['auditDetail.kriteria', 'auditDetail.auditSesi']);
        if ($request->filled('perusahaan_id')) {
            $perusahaanId = $request->perusahaan_id;
            $picaQuery->whereHas('auditDetail.auditSesi', function ($q) use ($perusahaanId) {
                $q->where('perusahaan_id', $perusahaanId);
            });
        }
        if ($request->filled('tahun_periode')) {
            $tahunPeriode = $request->tahun_periode;
            $picaQuery->whereHas('auditDetail.auditSesi', function ($q) use ($tahunPeriode) {
                $q->where('tahun_periode', $tahunPeriode);
            });
        }
        $allPicas = $picaQuery->get();
        
        $groupedSubTemuan = $allPicas->groupBy(function($p) {
            $sesiId = $p->auditDetail->audit_sesi_id ?? 0;
            $subId = $p->auditDetail->kriteria->sub_elemen_id ?? 0;
            return "{$sesiId}_{$subId}";
        });

        $subTotal = $groupedSubTemuan->count();
        $subOpen = 0;
        $subInProgress = 0;
        $subClosed = 0;

        foreach ($groupedSubTemuan as $subGroup) {
            if ($subGroup->contains('status', 'open')) {
                $subOpen++;
            } elseif ($subGroup->contains('status', 'in_progress')) {
                $subInProgress++;
            } else {
                $subClosed++;
            }
        }

        $stats = [
            'total'               => $subTotal,
            'open'                => $subOpen,
            'in_progress'         => $subInProgress,
            'closed'              => $subClosed,
            'total_kriteria'      => $allPicas->count(),
            'open_kriteria'       => $allPicas->where('status', 'open')->count(),
            'in_progress_kriteria'=> $allPicas->where('status', 'in_progress')->count(),
            'closed_kriteria'     => $allPicas->where('status', 'closed')->count(),
            'overdue'             => $allPicas->filter(fn($p) => $p->status !== 'closed' && $p->tenggat_waktu && $p->tenggat_waktu->isPast())->count(),
        ];

        // Perusahaan list for filter dropdown & navigation
        $perusahaans = \App\Models\Perusahaan::where('is_active', true)->orderBy('nama_perusahaan')->get();

        // Available audit years for filter
        $tahunPeriodes = AuditSesi::select('tahun_periode')->whereNotNull('tahun_periode')->distinct()->pluck('tahun_periode')->sortDesc();

        // Auditor list for filter dropdown
        $auditors = User::where('role', 'auditor')->orderBy('name')->get();

        return view('admin.pica.index', compact('auditSesis', 'stats', 'auditors', 'perusahaans', 'tahunPeriodes'));
    }

    /**
     * Update PICA record by Administrator (Lead Auditor).
     */
    public function update(Request $request, $id)
    {
        $pica = Pica::with('auditDetail.kriteria', 'auditDetail.auditSesi')->findOrFail($id);
        $originalData = $pica->getOriginal();

        $request->validate([
            'deskripsi_ketidaksesuaian'  => 'nullable|string',
            'kategori_temuan'            => 'nullable|in:kritikal,mayor,minor',
            'justifikasi_kategori'       => 'required_if:kategori_temuan,kritikal|nullable|string',
            'akar_masalah'               => 'nullable|string',
            'tindakan_koreksi'           => 'nullable|string',
            'tindakan_pencegahan'        => 'nullable|string',
            'pic_perbaikan'              => 'nullable|string',
            'tenggat_waktu'              => 'nullable|date',
            'status'                     => 'required|in:open,in_progress,closed',
            'catatan_verifikasi_auditor' => 'required_if:status,closed|nullable|string',
        ], [
            'status.required'                    => 'Status PICA wajib dipilih.',
            'status.in'                          => 'Pilihan status tidak valid.',
            'kategori_temuan.in'                => 'Pilihan kategori temuan tidak valid.',
            'justifikasi_kategori.required_if' => 'Justifikasi wajib diisi ketika menetapkan kategori temuan Kritikal.',
            'catatan_verifikasi_auditor.required_if' => 'Catatan verifikasi auditor wajib diisi saat menutup (closed) PICA.',
        ]);

        $status = $request->status;

        // Auto-transition: open -> in_progress if root cause is provided and status is open
        if ($status === 'open' && !empty($request->akar_masalah)) {
            $status = 'in_progress';
        }

        $kategoriTemuan = $request->input('kategori_temuan', $pica->kategori_temuan);
        $justifikasi    = $request->input('justifikasi_kategori', $pica->justifikasi_kategori);
        $isManual       = $pica->kategori_ditetapkan_manual;

        if ($request->has('kategori_ditetapkan_manual')) {
            $isManual = $request->boolean('kategori_ditetapkan_manual');
        } elseif ($request->filled('kategori_temuan')) {
            if ($request->kategori_temuan === 'kritikal' || $request->kategori_temuan !== $pica->kategori_temuan) {
                $isManual = true;
            }
        }

        if (!$isManual) {
            $subElemenId = $pica->auditDetail->kriteria->sub_elemen_id ?? null;
            $sesi        = $pica->auditDetail->auditSesi ?? null;
            if ($subElemenId && $sesi) {
                $kategoriTemuan = $sesi->hitungKategoriMayorPath1($subElemenId);
                $justifikasi    = null;
            }
        }

        $pica->update([
            'deskripsi_ketidaksesuaian'  => $request->deskripsi_ketidaksesuaian,
            'kategori_temuan'            => $kategoriTemuan,
            'kategori_ditetapkan_manual' => $isManual,
            'justifikasi_kategori'       => $justifikasi,
            'akar_masalah'               => $request->akar_masalah,
            'tindakan_koreksi'           => $request->tindakan_koreksi,
            'tindakan_pencegahan'        => $request->tindakan_pencegahan,
            'pic_perbaikan'              => $request->pic_perbaikan,
            'tenggat_waktu'              => $request->tenggat_waktu,
            'status'                     => $status,
            'catatan_verifikasi_auditor' => $request->catatan_verifikasi_auditor,
        ]);

        if ($pica->wasChanged()) {
            \App\Models\AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'PICA',
                'tindakan'        => "Update PICA #{$pica->id} (Kategori: {$pica->kategori_temuan}, Status: {$pica->status})",
                'data_lama'       => $originalData,
                'data_baru'       => $pica->getChanges(),
                'waktu_perubahan' => now(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data PICA ' . ($pica->auditDetail->kriteria->kode_kriteria ?? '') . ' berhasil disimpan!',
                'pica' => [
                    'id'              => $pica->id,
                    'status'          => $pica->status,
                    'kategori_temuan' => $pica->kategori_temuan,
                    'tenggat_waktu'   => $pica->tenggat_waktu ? $pica->tenggat_waktu->format('Y-m-d') : null,
                ]
            ]);
        }

        $sesiId = $pica->auditDetail->audit_sesi_id ?? null;
        return back()->with('success', 'Data PICA berhasil diperbarui oleh Administrator!')
                     ->with('open_pica_modal', $sesiId);
    }

    /**
     * Batch update all PICA records for an entire Audit Session.
     */
    public function batchUpdate(Request $request)
    {
        $picaItems = $request->input('picas', []);
        if (empty($picaItems) || !is_array($picaItems)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data PICA yang dikirim untuk disimpan.'
                ], 422);
            }
            return back()->with('error', 'Tidak ada data PICA yang dikirim untuk disimpan.');
        }

        $errors = [];
        // Conditional validation
        foreach ($picaItems as $item) {
            $picaId = $item['id'] ?? null;
            if (!$picaId) continue;

            $status = $item['status'] ?? 'open';
            $kategori = $item['kategori_temuan'] ?? null;
            $justifikasi = trim($item['justifikasi_kategori'] ?? '');
            $catatanVerif = trim($item['catatan_verifikasi_auditor'] ?? '');

            if ($kategori === 'kritikal' && empty($justifikasi)) {
                $errors["picas.{$picaId}.justifikasi_kategori"] = "Justifikasi wajib diisi untuk temuan berstatus Kritikal (PICA ID #{$picaId}).";
            }

            if ($status === 'closed' && empty($catatanVerif)) {
                $errors["picas.{$picaId}.catatan_verifikasi_auditor"] = "Catatan verifikasi auditor wajib diisi jika status diubah ke Closed (PICA ID #{$picaId}).";
            }
        }

        if (!empty($errors)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terdapat data yang belum lengkap (misal: Catatan verifikasi saat Closed, atau Justifikasi saat Kritikal).',
                    'errors' => $errors
                ], 422);
            }
            return back()->withErrors($errors)->withInput();
        }

        DB::beginTransaction();
        try {
            $updatedCount = 0;
            $updatedList = [];

            foreach ($picaItems as $item) {
                $picaId = $item['id'] ?? null;
                if (!$picaId) continue;

                $pica = Pica::with('auditDetail.kriteria', 'auditDetail.auditSesi')->find($picaId);
                if (!$pica) continue;

                $originalData = $pica->getOriginal();
                $status = $item['status'] ?? $pica->status;
                $akarMasalah = $item['akar_masalah'] ?? null;

                if ($status === 'open' && !empty($akarMasalah)) {
                    $status = 'in_progress';
                }

                $kategoriTemuan = $item['kategori_temuan'] ?? $pica->kategori_temuan;
                $justifikasi = $item['justifikasi_kategori'] ?? $pica->justifikasi_kategori;
                $isManual = $pica->kategori_ditetapkan_manual;

                if (isset($item['kategori_ditetapkan_manual'])) {
                    $isManual = filter_var($item['kategori_ditetapkan_manual'], FILTER_VALIDATE_BOOLEAN);
                } elseif (!empty($item['kategori_temuan'])) {
                    if ($item['kategori_temuan'] === 'kritikal' || $item['kategori_temuan'] !== $pica->kategori_temuan) {
                        $isManual = true;
                    }
                }

                if (!$isManual) {
                    $subElemenId = $pica->auditDetail->kriteria->sub_elemen_id ?? null;
                    $sesi = $pica->auditDetail->auditSesi ?? null;
                    if ($subElemenId && $sesi) {
                        $kategoriTemuan = $sesi->hitungKategoriMayorPath1($subElemenId);
                        $justifikasi = null;
                    }
                }

                $pica->update([
                    'deskripsi_ketidaksesuaian'  => $item['deskripsi_ketidaksesuaian'] ?? null,
                    'kategori_temuan'            => $kategoriTemuan,
                    'kategori_ditetapkan_manual' => $isManual,
                    'justifikasi_kategori'       => $justifikasi,
                    'akar_masalah'               => $akarMasalah,
                    'tindakan_koreksi'           => $item['tindakan_koreksi'] ?? null,
                    'tindakan_pencegahan'        => $item['tindakan_pencegahan'] ?? null,
                    'pic_perbaikan'              => $item['pic_perbaikan'] ?? null,
                    'tenggat_waktu'              => !empty($item['tenggat_waktu']) ? $item['tenggat_waktu'] : null,
                    'status'                     => $status,
                    'catatan_verifikasi_auditor' => $item['catatan_verifikasi_auditor'] ?? null,
                ]);

                if ($pica->wasChanged()) {
                    $updatedCount++;
                    \App\Models\AuditLog::create([
                        'user_id'         => auth()->id(),
                        'modul'           => 'PICA',
                        'tindakan'        => "Batch Update PICA #{$pica->id} (Kategori: {$pica->kategori_temuan}, Status: {$pica->status})",
                        'data_lama'       => $originalData,
                        'data_baru'       => $pica->getChanges(),
                        'waktu_perubahan' => now(),
                    ]);
                }

                $updatedList[$pica->id] = [
                    'id'              => $pica->id,
                    'status'          => $pica->status,
                    'kategori_temuan' => $pica->kategori_temuan,
                    'pic_perbaikan'   => $pica->pic_perbaikan,
                ];
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Seluruh data PICA pada sesi audit ini berhasil disimpan! ({$updatedCount} data diperbarui).",
                    'updated_count' => $updatedCount,
                    'total_count' => count($picaItems),
                    'picas' => $updatedList
                ]);
            }

            $sesiId = $request->input('sesi_id');
            return back()->with('success', "Seluruh isian PICA ({$updatedCount} diperbarui) berhasil disimpan!")
                         ->with('open_pica_modal', $sesiId);

        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan batch PICA: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal menyimpan batch PICA: ' . $e->getMessage());
        }
    }
}
