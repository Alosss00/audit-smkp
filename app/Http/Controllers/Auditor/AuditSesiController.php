<?php

namespace App\Http\Controllers\Auditor;

use App\Exports\AuditSesiExport;
use App\Http\Controllers\Controller;
use App\Models\AuditDetail;
use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Pica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AuditSesiController extends Controller
{
    /**
     * Display a listing of audit sessions.
     */
    public function index(Request $request)
    {
        $query = AuditSesi::where('user_id', auth()->id())->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $auditSesis = $query->paginate(10);

        return view('auditor.audit.index', compact('auditSesis'));
    }

    /**
     * Show the form for creating a new audit session.
     */
    public function create()
    {
        return view('auditor.audit.create');
    }

    /**
     * Store a newly created audit session in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_audit' => 'required|date',
            'area_audit' => 'required|string|max:255',
        ], [
            'tanggal_audit.required' => 'Tanggal audit wajib diisi.',
            'area_audit.required' => 'Area audit wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $sesi = AuditSesi::create([
                'user_id' => auth()->id(),
                'tanggal_audit' => $request->tanggal_audit,
                'area_audit' => $request->area_audit,
                'status' => 'draft',
                'skor_akhir' => 0.00,
            ]);

            $kriterias = Kriteria::all();
            foreach ($kriterias as $kriteria) {
                AuditDetail::create([
                    'audit_sesi_id' => $sesi->id,
                    'kriteria_id' => $kriteria->id,
                    'nilai' => 0.00,
                    'is_na' => false,
                    'catatan' => null,
                    'lampiran' => null,
                ]);
            }

            DB::commit();

            return redirect()->route('auditor.audit-sesi.matrix', $sesi->id)
                ->with('success', 'Sesi audit baru berhasil dibuat! Silakan isi matriks penilaian.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat sesi audit: ' . $e->getMessage());
        }
    }

    /**
     * Display matrix scoring view.
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
     * Update matrix scores, notes, and finding proof attachments.
     */
    public function updateMatrix(Request $request, $id)
    {
        $sesi = AuditSesi::where('user_id', auth()->id())->findOrFail($id);

        if ($sesi->status === 'selesai') {
            return back()->with('error', 'Sesi audit ini telah difinalisasi dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'details' => 'required|array',
            'details.*.nilai' => 'nullable|numeric|min:0',
            'details.*.is_na' => 'nullable|boolean',
            'details.*.catatan' => 'nullable|string',
            'details.*.lampiran' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->details as $detailId => $data) {
                $detail = AuditDetail::where('audit_sesi_id', $sesi->id)->find($detailId);
                if ($detail) {
                    $isNa = isset($data['is_na']) && $data['is_na'] == 1;
                    $nilai = $isNa ? 0.00 : (float) ($data['nilai'] ?? 0);
                    $max = $detail->kriteria ? (float) $detail->kriteria->nilai_maksimal : 4.00;

                    if ($nilai > $max) {
                        $nilai = $max;
                    }

                    $updatePayload = [
                        'nilai' => $nilai,
                        'is_na' => $isNa,
                        'catatan' => $data['catatan'] ?? null,
                    ];

                    if (isset($data['lampiran']) && $data['lampiran']->isValid()) {
                        if ($detail->lampiran && Storage::disk('public')->exists($detail->lampiran)) {
                            Storage::disk('public')->delete($detail->lampiran);
                        }

                        $filePath = $data['lampiran']->store('lampiran', 'public');
                        $updatePayload['lampiran'] = $filePath;
                    }

                    $detail->update($updatePayload);

                    // PICA Auto-Trigger & Clean Logic
                    if (!$isNa && $nilai < $max) {
                        $pica = Pica::where('audit_detail_id', $detail->id)->first();
                        $deskripsiTemuan = !empty($data['catatan']) 
                            ? $data['catatan'] 
                            : ($detail->catatan ?? "Ketidaksesuaian kriteria " . ($detail->kriteria ? $detail->kriteria->kode_kriteria : '') . " (Skor " . number_format($nilai, 2) . " / " . number_format($max, 2) . ")");

                        if (!$pica) {
                            Pica::create([
                                'audit_detail_id' => $detail->id,
                                'deskripsi_temuan' => $deskripsiTemuan,
                                'status' => 'open',
                            ]);
                        } else {
                            $pica->update([
                                'deskripsi_temuan' => $deskripsiTemuan,
                            ]);
                        }
                    } else {
                        $pica = Pica::where('audit_detail_id', $detail->id)->first();
                        if ($pica) {
                            if ($pica->status === 'open' && empty($pica->akar_masalah)) {
                                $pica->delete();
                            }
                        }
                    }
                }
            }

            if ($sesi->status === 'draft') {
                $sesi->status = 'berjalan';
            }

            $sesi->hitungSkorAkhir();

            DB::commit();

            if ($request->has('save_and_rekap')) {
                return redirect()->route('auditor.audit-sesi.rekap', $sesi->id)
                    ->with('success', 'Matriks penilaian berhasil disimpan! Berikut adalah rekapitulasi nilainya.');
            }

            return back()->with('success', 'Matriks penilaian audit berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan matriks: ' . $e->getMessage());
        }
    }

    /**
     * Display audit summary rekap.
     */
    public function rekap($id)
    {
        $sesi = AuditSesi::where('user_id', auth()->id())->findOrFail($id);
        $rekap = $sesi->getRekapPerElemen();
        $skorAkhir = $sesi->hitungSkorAkhir();

        return view('auditor.audit.rekap', compact('sesi', 'rekap', 'skorAkhir'));
    }

    /**
     * Display printable report view according to Kepdirjen 185.
     */
    public function cetak($id)
    {
        $sesi = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen'])->findOrFail($id);

        if (!auth()->user()->isAdmin() && $sesi->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $rekap = $sesi->getRekapPerElemen();
        $skorAkhir = $sesi->skor_akhir ?? $sesi->hitungSkorAkhir();

        return view('auditor.audit.cetak', compact('sesi', 'rekap', 'skorAkhir'));
    }

    /**
     * Export audit session rekap & criteria matrix to official Excel (.xlsx) file (TT-MGT-FRS-026B).
     */
    public function exportExcel($id)
    {
        $sesi = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen'])->findOrFail($id);

        if (!auth()->user()->isAdmin() && $sesi->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $fileName = 'TT-MGT-FRS-026B_Audit_SMKP_' . str_replace(' ', '_', $sesi->area_audit) . '_' . $sesi->tanggal_audit->format('Y-m-d') . '.xlsx';

        return Excel::download(new AuditSesiExport($sesi), $fileName);
    }

    /**
     * Finalize audit session.
     */
    public function finalisasi($id)
    {
        $sesi = AuditSesi::where('user_id', auth()->id())->findOrFail($id);

        if ($sesi->status === 'selesai') {
            return back()->with('info', 'Sesi audit ini sudah dalam status selesai.');
        }

        $sesi->hitungSkorAkhir();
        $sesi->status = 'selesai';
        $sesi->save();

        return redirect()->route('auditor.audit-sesi.rekap', $sesi->id)
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

        return redirect()->route('auditor.audit-sesi.index')
            ->with('success', 'Sesi audit berhasil dihapus.');
    }
}
