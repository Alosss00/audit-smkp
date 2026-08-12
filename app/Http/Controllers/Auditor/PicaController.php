<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Pica;
use Illuminate\Http\Request;

class PicaController extends Controller
{
    /**
     * Display a listing of PICA items scoped to the authenticated auditor.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $query = Pica::whereHas('auditDetail.auditSesi', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['auditDetail.auditSesi', 'auditDetail.kriteria.subElemen.elemen']);

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Search Query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi_temuan', 'like', "%{$search}%")
                  ->orWhere('pic_perbaikan', 'like', "%{$search}%")
                  ->orWhere('akar_masalah', 'like', "%{$search}%")
                  ->orWhereHas('auditDetail.auditSesi', function ($qs) use ($search) {
                      $qs->where('area_audit', 'like', "%{$search}%");
                  });
            });
        }

        $picas = $query->latest()->paginate(10);

        // Stats summary
        $baseQuery = Pica::whereHas('auditDetail.auditSesi', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'open' => (clone $baseQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'closed' => (clone $baseQuery)->where('status', 'closed')->count(),
        ];

        return view('auditor.pica.index', compact('picas', 'stats'));
    }

    /**
     * Update PICA record (Root cause, corrective actions, deadline, PIC, status & auditor verification).
     */
    public function update(Request $request, $id)
    {
        $userId = auth()->id();

        $pica = Pica::whereHas('auditDetail.auditSesi', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->findOrFail($id);

        $request->validate([
            'akar_masalah' => 'nullable|string',
            'tindakan_koreksi' => 'nullable|string',
            'tindakan_pencegahan' => 'nullable|string',
            'tenggat_waktu' => 'nullable|date',
            'pic_perbaikan' => 'nullable|string|max:255',
            'status' => 'required|in:open,in_progress,closed',
            'catatan_verifikasi_auditor' => 'required_if:status,closed|nullable|string',
        ], [
            'status.required' => 'Status PICA wajib dipilih.',
            'status.in' => 'Pilihan status tidak valid.',
            'catatan_verifikasi_auditor.required_if' => 'Catatan verifikasi auditor wajib diisi saat menutup (closed) PICA.',
        ]);

        $status = $request->status;

        // Auto-transition: open -> in_progress if root cause is provided and status is open
        if ($status === 'open' && !empty($request->akar_masalah)) {
            $status = 'in_progress';
        }

        $pica->update([
            'akar_masalah' => $request->akar_masalah,
            'tindakan_koreksi' => $request->tindakan_koreksi,
            'tindakan_pencegahan' => $request->tindakan_pencegahan,
            'tenggat_waktu' => $request->tenggat_waktu,
            'pic_perbaikan' => $request->pic_perbaikan,
            'status' => $status,
            'catatan_verifikasi_auditor' => $request->catatan_verifikasi_auditor,
        ]);

        return back()->with('success', 'Data PICA berhasil diperbarui!');
    }
}
