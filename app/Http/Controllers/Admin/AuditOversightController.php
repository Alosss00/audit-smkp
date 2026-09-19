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

        if ($request->filled('perusahaan_id')) {
            $query->where('perusahaan_id', $request->perusahaan_id);
        } elseif ($request->filled('area_selection')) {
            $sel = $request->area_selection;
            $pId = str_starts_with($sel, 'p:') ? substr($sel, 2) : $sel;
            $query->where('perusahaan_id', $pId);
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
                  });
            });
        }

        $auditSesis = $query->paginate(10);
        $perusahaans = \App\Models\Perusahaan::where('is_active', true)->orderBy('nama_perusahaan')->get();

        return view('admin.audits.index', compact('auditSesis', 'perusahaans'));
    }

    /**
     * Show audit session details for Administrator.
     */
    public function show($id)
    {
        $sesi = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen'])->findOrFail($id);
        $rekap = $sesi->getRekapPerElemen();
        $hierarki = $sesi->getRekapHierarkis();
        $skorAkhir = $sesi->hitungSkorAkhir();
        $gatingViolations = app(\App\Services\GatingRuleService::class)->evaluate($sesi);

        return view('admin.audits.show', compact('sesi', 'rekap', 'hierarki', 'skorAkhir', 'gatingViolations'));
    }
}
