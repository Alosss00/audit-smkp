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
        $query = AuditSesi::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('area_audit', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $auditSesis = $query->paginate(10);

        return view('admin.audits.index', compact('auditSesis'));
    }

    /**
     * Show audit session details for Administrator.
     */
    public function show($id)
    {
        $sesi = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen'])->findOrFail($id);
        $rekap = $sesi->getRekapPerElemen();
        $skorAkhir = $sesi->skor_akhir ?? $sesi->hitungSkorAkhir();

        return view('admin.audits.show', compact('sesi', 'rekap', 'skorAkhir'));
    }
}
