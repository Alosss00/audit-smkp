<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AuditSesiExport;
use App\Http\Controllers\Auditor\AuditSesiController as AuditorAuditSesiController;
use App\Models\AuditDetail;
use App\Models\AuditLog;
use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Pica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AuditSesiAdminController extends AuditorAuditSesiController
{
    /**
     * Display listing of audit sessions owned by the admin user.
     */
    public function index(Request $request)
    {
        $query = AuditSesi::where('user_id', auth()->id())->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $auditSesis = $query->paginate(10);

        return view('admin.audit-sesi.index', compact('auditSesis'));
    }

    /**
     * Show the form for creating a new audit session.
     */
    public function create()
    {
        return view('admin.audit-sesi.create');
    }

    /**
     * Store a newly created audit session.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'area_audit'      => 'required|string|max:255',
        ], [
            'tanggal_mulai.required'           => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required'         => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal'   => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'area_audit.required'              => 'Area audit wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $sesi = AuditSesi::create([
                'user_id'         => auth()->id(),
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'area_audit'      => $request->area_audit,
                'status'          => 'draft',
                'skor_akhir'      => 0,
            ]);

            $kriterias = Kriteria::all();
            foreach ($kriterias as $kriteria) {
                AuditDetail::create([
                    'audit_sesi_id' => $sesi->id,
                    'kriteria_id'   => $kriteria->id,
                    'nilai'         => 0,
                    'is_na'         => false,
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
        $sesi = AuditSesi::where('user_id', auth()->id())->findOrFail($id);

        $elemens = Elemen::with(['subElemens.kriterias' => function ($query) use ($sesi) {
            $query->with(['auditDetails' => function ($q) use ($sesi) {
                $q->where('audit_sesi_id', $sesi->id);
            }]);
        }])->orderBy('kode_elemen')->get();

        $rekap = $sesi->getRekapPerElemen();

        return view('auditor.audit.matrix', compact('sesi', 'elemens', 'rekap'));
    }

    /**
     * Update matrix scores with AuditLog tracking.
     */
    public function updateMatrix(Request $request, $id)
    {
        $sesi = AuditSesi::where('user_id', auth()->id())->findOrFail($id);

        if ($sesi->status === 'selesai') {
            return back()->with('error', 'Sesi audit ini telah difinalisasi dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'details'            => 'required|array',
            'details.*.nilai'    => 'nullable|integer|min:0',
            'details.*.is_na'    => 'nullable|boolean',
            'details.*.catatan'  => 'nullable|string',
            'details.*.lampiran' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $changedDetails = [];

            foreach ($request->details as $detailId => $data) {
                $detail = AuditDetail::where('audit_sesi_id', $sesi->id)->find($detailId);
                if ($detail) {
                    $originalValues = $detail->getOriginal();

                    $isNa  = isset($data['is_na']) && $data['is_na'] == 1;
                    $nilai = $isNa ? 0 : (int) ($data['nilai'] ?? 0);
                    $max   = $detail->kriteria ? (int) $detail->kriteria->nilai_maksimal : 4;

                    if ($nilai > $max) {
                        $nilai = $max;
                    }

                    $updatePayload = [
                        'nilai'   => $nilai,
                        'is_na'   => $isNa,
                        'catatan' => $data['catatan'] ?? null,
                    ];

                    if (isset($data['lampiran']) && $data['lampiran']->isValid()) {
                        if ($detail->lampiran && Storage::disk('public')->exists($detail->lampiran)) {
                            Storage::disk('public')->delete($detail->lampiran);
                        }
                        $updatePayload['lampiran'] = $data['lampiran']->store('lampiran', 'public');
                    }

                    $detail->update($updatePayload);

                    if ($detail->wasChanged()) {
                        $changedDetails[$detailId] = [
                            'lama' => $originalValues,
                            'baru' => $detail->getChanges(),
                        ];
                    }

                    // PICA Auto-Trigger & Clean Logic
                    if (!$isNa && $nilai < $max) {
                        $pica            = Pica::where('audit_detail_id', $detail->id)->first();
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
                return redirect()->route('admin.audit-sesi.rekap', $sesi->id)
                    ->with('success', 'Matriks penilaian berhasil disimpan!');
            }

            return back()->with('success', 'Matriks penilaian audit berhasil diperbarui!');
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
        $sesi      = AuditSesi::where('user_id', auth()->id())->findOrFail($id);
        $rekap     = $sesi->getRekapPerElemen();
        $skorAkhir = $sesi->hitungSkorAkhir();

        return view('admin.audit-sesi.rekap', compact('sesi', 'rekap', 'skorAkhir'));
    }

    /**
     * Display printable report.
     */
    public function cetak($id)
    {
        $sesi = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen'])->findOrFail($id);

        if (!auth()->user()->isAdmin() && $sesi->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $rekap     = $sesi->getRekapPerElemen();
        $skorAkhir = $sesi->skor_akhir ?? $sesi->hitungSkorAkhir();

        return view('auditor.audit.cetak', compact('sesi', 'rekap', 'skorAkhir'));
    }

    /**
     * Export to Excel.
     */
    public function exportExcel($id)
    {
        $sesi = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen'])->findOrFail($id);

        if (!auth()->user()->isAdmin() && $sesi->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $fileName = 'TT-MGT-FRS-026B_Audit_SMKP_' . str_replace(' ', '_', $sesi->area_audit) . '_' . $sesi->tanggal_mulai->format('Y-m-d') . '.xlsx';

        return Excel::download(new AuditSesiExport($sesi), $fileName);
    }

    /**
     * Finalize audit session with AuditLog.
     */
    public function finalisasi($id)
    {
        $sesi = AuditSesi::where('user_id', auth()->id())->findOrFail($id);

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
     * Remove audit session (draft/berjalan only).
     */
    public function destroy($id)
    {
        $sesi = AuditSesi::where('user_id', auth()->id())->findOrFail($id);

        if ($sesi->status === 'selesai') {
            return back()->with('error', 'Sesi audit yang sudah selesai tidak dapat dihapus.');
        }

        $sesi->delete();

        return redirect()->route('admin.audit-sesi.index')
            ->with('success', 'Sesi audit berhasil dihapus.');
    }
}
