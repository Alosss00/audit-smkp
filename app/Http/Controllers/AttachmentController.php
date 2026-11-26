<?php

namespace App\Http\Controllers;

use App\Models\AuditDetail;
use App\Models\AuditLaporanDistribusi;
use App\Models\AuditLaporanProfil;
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
                $sesi = $pica->auditDetail?->auditSesi;
                if (!$sesi || !$sesi->matchesUserArea($user->area)) {
                    abort(403, 'Anda tidak memiliki hak akses untuk melihat bukti temuan area ini.');
                }
            }

            $path = $pica->bukti_perbaikan;
            if (!$path) {
                abort(404, 'File bukti perbaikan tidak ditemukan.');
            }

            $fullPath = $this->resolveFilePath($path);
            if (!$fullPath) {
                abort(404, 'File bukti perbaikan tidak ditemukan pada storage.');
            }

            return $this->buildSecureFileResponse($fullPath);
        }

        if ($type === 'lampiran') {
            $detail = AuditDetail::with('auditSesi')->findOrFail($id);

            // Scoping check for Auditor Perusahaan
            if ($user->role === 'auditor') {
                $sesi = $detail->auditSesi;
                if (!$sesi || !$sesi->matchesUserArea($user->area)) {
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

            if (!$matchedPath) {
                abort(404, 'File lampiran kriteria tidak ditemukan.');
            }

            $fullPath = $this->resolveFilePath($matchedPath);
            if (!$fullPath) {
                abort(404, 'File lampiran kriteria tidak ditemukan pada storage.');
            }

            return $this->buildSecureFileResponse($fullPath);
        }

        if ($type === 'struktur_organisasi') {
            $profil = AuditLaporanProfil::with('laporan.auditSesi')->findOrFail($id);

            if ($user->role === 'auditor') {
                $sesi = $profil->laporan?->auditSesi;
                if (!$sesi || !$sesi->matchesUserArea($user->area)) {
                    abort(403, 'Anda tidak memiliki hak akses untuk melihat berkas struktur organisasi sesi audit ini.');
                }
            }

            $path = $profil->struktur_organisasi_path;
            if (!$path) {
                abort(404, 'Berkas struktur organisasi belum diunggah.');
            }

            $fullPath = $this->resolveFilePath($path);
            if (!$fullPath) {
                abort(404, 'Berkas struktur organisasi tidak ditemukan pada storage.');
            }

            return $this->buildSecureFileResponse($fullPath);
        }

        if ($type === 'distribusi_laporan') {
            $distribusi = AuditLaporanDistribusi::with('laporan.auditSesi')->findOrFail($id);

            if ($user->role === 'auditor') {
                $sesi = $distribusi->laporan?->auditSesi;
                if (!$sesi || !$sesi->matchesUserArea($user->area)) {
                    abort(403, 'Anda tidak memiliki hak akses untuk melihat bukti distribusi laporan sesi audit ini.');
                }
            }

            $path = $distribusi->bukti_path;
            if (!$path) {
                abort(404, 'Berkas bukti distribusi tidak ditemukan.');
            }

            $fullPath = $this->resolveFilePath($path);
            if (!$fullPath) {
                abort(404, 'Berkas bukti distribusi tidak ditemukan pada storage.');
            }

            return $this->buildSecureFileResponse($fullPath);
        }

        abort(404, 'Tipe berkas tidak dikenali.');
    }

    /**
     * Resolve absolute file path, prioritizing private attachments disk with fallback to public disk.
     */
    protected function resolveFilePath(string $relativePath): ?string
    {
        if (Storage::disk('attachments')->exists($relativePath)) {
            return Storage::disk('attachments')->path($relativePath);
        }

        if (Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->path($relativePath);
        }

        return null;
    }

    /**
     * Build secure BinaryFileResponse with proper Content-Type, X-Content-Type-Options, and Content-Disposition.
     */
    protected function buildSecureFileResponse(string $fullPath): BinaryFileResponse
    {
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $basename = basename($fullPath);

        $mimeType = @mime_content_type($fullPath);
        if (!$mimeType || $mimeType === 'application/x-empty') {
            $extensionMap = [
                'pdf'  => 'application/pdf',
                'png'  => 'image/png',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif'  => 'image/gif',
                'webp' => 'image/webp',
                'svg'  => 'image/svg+xml',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'zip'  => 'application/zip',
                'txt'  => 'text/plain',
            ];
            $mimeType = $extensionMap[$extension] ?? 'application/octet-stream';
        }

        $inlineExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $disposition = in_array($extension, $inlineExtensions, true)
            ? 'inline; filename="' . addcslashes($basename, '"') . '"'
            : 'attachment; filename="' . addcslashes($basename, '"') . '"';

        $headers = [
            'Content-Type'           => $mimeType,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition'    => $disposition,
        ];

        return response()->file($fullPath, $headers);
    }
}

