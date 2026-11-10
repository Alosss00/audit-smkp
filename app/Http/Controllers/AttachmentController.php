<?php

namespace App\Http\Controllers;

use App\Models\AuditDetail;
use App\Models\Pica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentController extends Controller
{
    /**
     * Safely deliver uploaded audit attachments and PICA proofs behind authentication & authorization.
     */
    public function show(Request $request, string $type, int|string $id, ?string $filename = null): BinaryFileResponse
    {
        $user = auth()->user();

        if ($type === 'pica') {
            $pica = Pica::with('auditDetail.auditSesi')->findOrFail($id);

            // Scoping check for Auditor Perusahaan
            if ($user->role === 'auditor') {
                $userArea = $user->area;
                $sesiArea = $pica->auditDetail->auditSesi->area_audit ?? '';
                $sesiComp = $pica->auditDetail->auditSesi->perusahaan->nama_perusahaan ?? '';

                if ($sesiArea !== $userArea && $sesiComp !== $userArea) {
                    abort(403, 'Anda tidak memiliki hak akses untuk melihat bukti temuan area ini.');
                }
            }

            $path = $pica->bukti_perbaikan;
            if (!$path || !Storage::disk('public')->exists($path)) {
                abort(404, 'File bukti perbaikan tidak ditemukan.');
            }

            return response()->file(Storage::disk('public')->path($path));
        }

        if ($type === 'lampiran') {
            $detail = AuditDetail::with('auditSesi')->findOrFail($id);

            // Scoping check for Auditor Perusahaan
            if ($user->role === 'auditor') {
                $userArea = $user->area;
                $sesiArea = $detail->auditSesi->area_audit ?? '';
                $sesiComp = $detail->auditSesi->perusahaan->nama_perusahaan ?? '';

                if ($sesiArea !== $userArea && $sesiComp !== $userArea) {
                    abort(403, 'Anda tidak memiliki hak akses untuk melihat lampiran sesi audit ini.');
                }
            }

            // Find matching path in lampiran_array
            $matchedPath = null;
            foreach ($detail->lampiran_array as $p) {
                if ($filename && basename($p) === $filename) {
                    $matchedPath = $p;
                    break;
                }
            }

            // If no exact filename provided or not matched, use first attachment
            if (!$matchedPath && !empty($detail->lampiran_array)) {
                $matchedPath = $detail->lampiran_array[0];
            }

            if (!$matchedPath || !Storage::disk('public')->exists($matchedPath)) {
                abort(404, 'File lampiran kriteria tidak ditemukan.');
            }

            return response()->file(Storage::disk('public')->path($matchedPath));
        }

        abort(404, 'Tipe berkas tidak dikenali.');
    }
}

