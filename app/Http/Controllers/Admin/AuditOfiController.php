<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AuditOfi;
use App\Models\AuditSesi;
use App\Models\Kriteria;
use App\Models\SubElemen;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditOfiController extends Controller
{
    public function index(int|string $id): View
    {
        $sesi = AuditSesi::with(['perusahaan', 'user'])->findOrFail($id);
        $ofis = AuditOfi::where('audit_sesi_id', $sesi->id)
            ->with(['subElemen.elemen', 'kriteria', 'pembuat'])
            ->orderBy('nomor_urut')
            ->get();
        $subElemens = SubElemen::with('elemen')->where('is_na', false)->orderBy('elemen_id')->orderBy('kode_sub')->get();
        $kriterias = Kriteria::orderBy('kode_kriteria')->get(['id', 'sub_elemen_id', 'kode_kriteria', 'deskripsi']);
        $isReadOnly = false;

        return view('admin.audit-sesi.ofi', compact('sesi', 'ofis', 'subElemens', 'kriterias', 'isReadOnly'));
    }

    public function indexForAuditor(int|string $id): View
    {
        $userArea = auth()->user()->area;
        if (empty($userArea)) {
            abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun. Hubungi Administrator.');
        }

        $sesi = AuditSesi::with(['perusahaan', 'user'])
            ->forUserArea($userArea)
            ->findOrFail($id);

        $ofis = AuditOfi::where('audit_sesi_id', $sesi->id)
            ->with(['subElemen.elemen', 'kriteria', 'pembuat'])
            ->orderBy('nomor_urut')
            ->get();
        $subElemens = collect();
        $kriterias = collect();
        $isReadOnly = true;

        return view('admin.audit-sesi.ofi', compact('sesi', 'ofis', 'subElemens', 'kriterias', 'isReadOnly'));
    }

    public function store(Request $request, int|string $id): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);

        $validated = $request->validate([
            'sub_elemen_id' => 'nullable|integer|exists:sub_elemens,id',
            'kriteria_id'   => 'nullable|integer|exists:kriterias,id',
            'deskripsi'     => 'required|string|max:5000',
            'rekomendasi'   => 'required|string|max:5000',
            'referensi'     => 'nullable|string|max:255',
        ], [
            'deskripsi.required'   => 'Deskripsi peluang perbaikan (OFI) wajib diisi.',
            'rekomendasi.required' => 'Rekomendasi perbaikan wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $ofi = new AuditOfi([
                'sub_elemen_id' => $validated['sub_elemen_id'] ?? null,
                'kriteria_id'   => $validated['kriteria_id'] ?? null,
                'deskripsi'     => trim($validated['deskripsi']),
                'rekomendasi'   => trim($validated['rekomendasi']),
                'referensi'     => ! empty($validated['referensi']) ? trim($validated['referensi']) : null,
                'dibuat_oleh'   => auth()->id(),
            ]);
            $ofi->audit_sesi_id = $sesi->id;
            $ofi->nomor_urut = AuditOfi::nextNomorUrutForSesi($sesi->id);
            $ofi->save();

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Formulir OFI',
                'tindakan'        => "Menambahkan OFI #{$ofi->nomor_urut} pada sesi audit #{$sesi->id}",
                'data_lama'       => null,
                'data_baru'       => $ofi->toArray(),
                'waktu_perubahan' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.audit-sesi.ofi.index', $sesi->id)
                ->with('success', "Peluang Perbaikan (OFI #{$ofi->nomor_urut}) berhasil ditambahkan.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan OFI: ' . $e->getMessage());
        }
    }

    public function update(Request $request, int|string $id, int|string $ofiId): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $ofi = AuditOfi::where('audit_sesi_id', $sesi->id)->findOrFail($ofiId);
        $oldData = $ofi->toArray();

        $validated = $request->validate([
            'sub_elemen_id' => 'nullable|integer|exists:sub_elemens,id',
            'kriteria_id'   => 'nullable|integer|exists:kriterias,id',
            'deskripsi'     => 'required|string|max:5000',
            'rekomendasi'   => 'required|string|max:5000',
            'referensi'     => 'nullable|string|max:255',
        ], [
            'deskripsi.required'   => 'Deskripsi peluang perbaikan (OFI) wajib diisi.',
            'rekomendasi.required' => 'Rekomendasi perbaikan wajib diisi.',
        ]);

        $ofi->update([
            'sub_elemen_id' => $validated['sub_elemen_id'] ?? null,
            'kriteria_id'   => $validated['kriteria_id'] ?? null,
            'deskripsi'     => trim($validated['deskripsi']),
            'rekomendasi'   => trim($validated['rekomendasi']),
            'referensi'     => ! empty($validated['referensi']) ? trim($validated['referensi']) : null,
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Formulir OFI',
            'tindakan'        => "Memperbarui OFI #{$ofi->nomor_urut} pada sesi audit #{$sesi->id}",
            'data_lama'       => $oldData,
            'data_baru'       => $ofi->fresh()->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.ofi.index', $sesi->id)
            ->with('success', "Peluang Perbaikan (OFI #{$ofi->nomor_urut}) berhasil diperbarui.");
    }

    public function destroy(int|string $id, int|string $ofiId): RedirectResponse
    {
        $sesi = AuditSesi::findOrFail($id);
        $ofi = AuditOfi::where('audit_sesi_id', $sesi->id)->findOrFail($ofiId);
        $oldData = $ofi->toArray();
        $nomor = $ofi->nomor_urut;
        $ofi->delete();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Formulir OFI',
            'tindakan'        => "Menghapus (soft delete) OFI #{$nomor} pada sesi audit #{$sesi->id}",
            'data_lama'       => $oldData,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.audit-sesi.ofi.index', $sesi->id)
            ->with('success', "OFI #{$nomor} berhasil dihapus.");
    }

    public function cetak(int|string $id): View
    {
        $query = AuditSesi::with(['perusahaan', 'user']);

        if (auth()->check() && auth()->user()->role === 'auditor') {
            $userArea = auth()->user()->area;
            if (empty($userArea)) {
                abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun. Hubungi Administrator.');
            }
            $query->forUserArea($userArea);
        }

        $sesi = $query->findOrFail($id);
        $ofis = AuditOfi::where('audit_sesi_id', $sesi->id)
            ->with(['subElemen.elemen', 'kriteria', 'pembuat'])
            ->orderBy('nomor_urut')
            ->get();
        $noDokumen = config('audit.no_dokumen_ofi', 'TT-MGT-FRS-026G');

        return view('laporan.cetak_ofi', compact('sesi', 'ofis', 'noDokumen'));
    }
}
