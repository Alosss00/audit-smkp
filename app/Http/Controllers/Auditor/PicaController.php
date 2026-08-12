<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\AuditSesi;
use App\Models\Pica;
use Illuminate\Http\Request;

class PicaController extends Controller
{
    /**
     * Display a listing of PICA items grouped by Audit Session for authenticated auditor.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $query = AuditSesi::where('user_id', $userId)
            ->whereHas('auditDetails.pica')
            ->with([
                'user',
                'auditDetails' => function ($q) {
                    $q->whereHas('pica');
                },
                'auditDetails.pica',
                'auditDetails.kriteria.subElemen.elemen'
            ]);

        // Filter by Status (sessions having at least one PICA with matching status)
        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('auditDetails.pica', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        // Filter by Search Query (area_audit or deskripsi_temuan / pic / akar_masalah)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('area_audit', 'like', "%{$search}%")
                  ->orWhereHas('auditDetails.pica', function ($qp) use ($search) {
                      $qp->where('deskripsi_temuan', 'like', "%{$search}%")
                        ->orWhere('pic_perbaikan', 'like', "%{$search}%")
                        ->orWhere('akar_masalah', 'like', "%{$search}%");
                  });
            });
        }

        $auditSesis = $query->latest()->paginate(10);

        // Stats summary
        $basePicaQuery = Pica::whereHas('auditDetail.auditSesi', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        $stats = [
            'total' => (clone $basePicaQuery)->count(),
            'open' => (clone $basePicaQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $basePicaQuery)->where('status', 'in_progress')->count(),
            'closed' => (clone $basePicaQuery)->where('status', 'closed')->count(),
        ];

        return view('auditor.pica.index', compact('auditSesis', 'stats'));
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
