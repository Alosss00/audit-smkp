<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditSesi;
use App\Models\Pica;
use App\Models\User;
use Illuminate\Http\Request;

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

        // Filter by Auditor (user_id)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Search Query (area_audit or deskripsi_temuan / akar_masalah / auditor name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('area_audit', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('auditDetails.pica', function ($qp) use ($search) {
                      $qp->where('deskripsi_temuan', 'like', "%{$search}%")
                        ->orWhere('akar_masalah', 'like', "%{$search}%");
                  });
            });
        }

        $auditSesis = $query->latest()->paginate(10);

        // Global Stats Summary
        $stats = [
            'total' => Pica::count(),
            'open' => Pica::where('status', 'open')->count(),
            'in_progress' => Pica::where('status', 'in_progress')->count(),
            'closed' => Pica::where('status', 'closed')->count(),
            'overdue' => Pica::where('status', '!=', 'closed')
                ->whereNotNull('tenggat_waktu')
                ->where('tenggat_waktu', '<', now()->startOfDay())
                ->count(),
        ];

        // Auditor list for filter dropdown
        $auditors = User::where('role', 'auditor')->orderBy('name')->get();

        return view('admin.pica.index', compact('auditSesis', 'stats', 'auditors'));
    }

    /**
     * Update PICA record by Administrator (Lead Auditor).
     */
    public function update(Request $request, $id)
    {
        $pica = Pica::findOrFail($id);
        $originalData = $pica->getOriginal();

        $request->validate([
            'akar_masalah'               => 'nullable|string',
            'tindakan_koreksi'           => 'nullable|string',
            'tindakan_pencegahan'        => 'nullable|string',
            'tenggat_waktu'              => 'nullable|date',
            'status'                     => 'required|in:open,in_progress,closed',
            'catatan_verifikasi_auditor' => 'required_if:status,closed|nullable|string',
        ], [
            'status.required'                        => 'Status PICA wajib dipilih.',
            'status.in'                              => 'Pilihan status tidak valid.',
            'catatan_verifikasi_auditor.required_if' => 'Catatan verifikasi auditor wajib diisi saat menutup (closed) PICA.',
        ]);

        $status = $request->status;

        // Auto-transition: open -> in_progress if root cause is provided and status is open
        if ($status === 'open' && !empty($request->akar_masalah)) {
            $status = 'in_progress';
        }

        $pica->update([
            'akar_masalah'               => $request->akar_masalah,
            'tindakan_koreksi'           => $request->tindakan_koreksi,
            'tindakan_pencegahan'        => $request->tindakan_pencegahan,
            'tenggat_waktu'              => $request->tenggat_waktu,
            'status'                     => $status,
            'catatan_verifikasi_auditor' => $request->catatan_verifikasi_auditor,
        ]);

        if ($pica->wasChanged()) {
            \App\Models\AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'PICA',
                'tindakan'        => "Update PICA #{$pica->id} (Status: {$pica->status})",
                'data_lama'       => $originalData,
                'data_baru'       => $pica->getChanges(),
                'waktu_perubahan' => now(),
            ]);
        }

        return back()->with('success', 'Data PICA berhasil diperbarui oleh Administrator!');
    }
}
