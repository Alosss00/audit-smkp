<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AuditSesi;
use App\Models\Pica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PicaController extends Controller
{
    /**
     * Display a listing of PICA items for the user's area.
     */
    public function index(Request $request)
    {
        $userArea = auth()->user()->area;
        if (empty($userArea)) {
            abort(403, 'Akun Anda belum ditugaskan ke area manapun. Hubungi Administrator.');
        }

        $query = AuditSesi::whereHas('auditDetails.pica')
            ->where('area_audit', $userArea)
            ->with([
                'user',
                'auditDetails' => function ($q) {
                    $q->whereHas('pica');
                },
                'auditDetails.pica',
                'auditDetails.kriteria.subElemen.elemen'
            ]);

        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('auditDetails.pica', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('area_audit', 'like', "%{$search}%")
                  ->orWhereHas('auditDetails.pica', function ($qp) use ($search) {
                      $qp->where('deskripsi_temuan', 'like', "%{$search}%")
                        ->orWhere('akar_masalah', 'like', "%{$search}%");
                  });
            });
        }

        $auditSesis = $query->latest()->paginate(10);

        $basePicaQuery = Pica::whereHas('auditDetail.auditSesi', function ($q) use ($userArea) {
            $q->where('area_audit', $userArea);
        });

        $stats = [
            'total'       => (clone $basePicaQuery)->count(),
            'open'        => (clone $basePicaQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $basePicaQuery)->where('status', 'in_progress')->count(),
            'closed'      => (clone $basePicaQuery)->where('status', 'closed')->count(),
        ];

        return view('auditor.pica.index', compact('auditSesis', 'stats', 'userArea'));
    }

    /**
     * Show edit form for Auditee/PIC area to fill root cause and upload proof.
     */
    public function edit($id)
    {
        $userArea = auth()->user()->area;
        if (empty($userArea)) {
            abort(403, 'Akun Anda belum ditugaskan ke area manapun. Hubungi Administrator.');
        }

        $pica = Pica::whereHas('auditDetail.auditSesi', function ($q) use ($userArea) {
            $q->where('area_audit', $userArea);
        })->with(['auditDetail.kriteria.subElemen.elemen', 'auditDetail.auditSesi'])->findOrFail($id);

        $lastLog = AuditLog::where('modul', 'PICA')
            ->where('tindakan', 'like', "%PICA #{$pica->id}%")
            ->with('user')
            ->latest('waktu_perubahan')
            ->first();

        return view('auditor.pica.edit', compact('pica', 'lastLog'));
    }

    /**
     * Update PICA record by Auditee/PIC Area.
     * Field-level authorization: STRICTLY strip status, tenggat_waktu, catatan_verifikasi_auditor!
     */
    public function update(Request $request, $id)
    {
        $userArea = auth()->user()->area;
        if (empty($userArea)) {
            abort(403, 'Akun Anda belum ditugaskan ke area manapun. Hubungi Administrator.');
        }

        $pica = Pica::whereHas('auditDetail.auditSesi', function ($q) use ($userArea) {
            $q->where('area_audit', $userArea);
        })->findOrFail($id);

        $request->validate([
            'akar_masalah'        => 'nullable|string',
            'tindakan_koreksi'    => 'nullable|string',
            'tindakan_pencegahan' => 'nullable|string',
            'bukti_perbaikan'     => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx,zip|max:5012',
        ]);

        $originalData = $pica->getOriginal();

        $updatePayload = [
            'akar_masalah'        => $request->akar_masalah,
            'tindakan_koreksi'    => $request->tindakan_koreksi,
            'tindakan_pencegahan' => $request->tindakan_pencegahan,
        ];

        // Handle proof attachment upload
        if ($request->hasFile('bukti_perbaikan') && $request->file('bukti_perbaikan')->isValid()) {
            if ($pica->bukti_perbaikan && Storage::disk('public')->exists($pica->bukti_perbaikan)) {
                Storage::disk('public')->delete($pica->bukti_perbaikan);
            }
            $updatePayload['bukti_perbaikan'] = $request->file('bukti_perbaikan')->store('bukti_pica', 'public');
        }

        // Auto-transition from open to in_progress if root cause is provided
        if ($pica->status === 'open' && !empty($request->akar_masalah)) {
            $updatePayload['status'] = 'in_progress';
        }

        // Note: status, tenggat_waktu, catatan_verifikasi_auditor are EXPLICITLY NOT in $updatePayload!

        $pica->update($updatePayload);

        if ($pica->wasChanged()) {
            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'PICA',
                'tindakan'        => "Responden mengisi/update PICA #{$pica->id}",
                'data_lama'       => $originalData,
                'data_baru'       => $pica->getChanges(),
                'waktu_perubahan' => now(),
            ]);
        }

        return redirect()->route('auditor.pica.index')
            ->with('success', 'Tindak lanjut PICA berhasil diperbarui!');
    }
}
