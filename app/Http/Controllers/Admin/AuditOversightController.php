<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditSesi;
use Illuminate\Http\Request;

class AuditOversightController extends Controller
{
    /**
     * Display listing of all audit sessions for Administrator.
     */
    public function index(Request $request)
    {
        $query = AuditSesi::with(['user', 'perusahaan'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('area_selection')) {
            $sel = $request->area_selection;
            if (str_starts_with($sel, 'p:')) {
                $query->where('perusahaan_id', substr($sel, 2));
            } elseif (str_starts_with($sel, 'd:')) {
                $query->where('departemen_id', substr($sel, 2));
            }
        }

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
                  ->orWhereHas('departemen', function ($qd) use ($search) {
                      $qd->where('nama_departemen', 'like', "%{$search}%");
                  });
            });
        }

        $auditSesis = $query->paginate(10);
        $perusahaans = \App\Models\Perusahaan::where('is_active', true)->orderBy('nama_perusahaan')->get();
        $departemens = \App\Models\Departemen::where('is_active', true)->orderBy('nama_departemen')->get();

        return view('admin.audits.index', compact('auditSesis', 'perusahaans', 'departemens'));
    }

    /**
     * Show audit session details for Administrator.
     */
    public function show($id)
    {
        $sesi = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen'])->findOrFail($id);
        $rekap = $sesi->getRekapPerElemen();
        $hierarki = $sesi->getRekapHierarkis();
        $skorAkhir = $sesi->skor_akhir ?? $sesi->hitungSkorAkhir();

        return view('admin.audits.show', compact('sesi', 'rekap', 'hierarki', 'skorAkhir'));
    }
}
