<?php

namespace App\Http\Controllers\Auditor;

use App\Exports\AuditSesiExport;
use App\Http\Controllers\Controller;
use App\Models\AuditDetail;
use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Pica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AuditSesiController extends Controller
{
    /**
     * Display a listing of audit sessions scoped to the auditor/auditee's area.
     */
    public function index(Request $request)
    {
        $userArea = auth()->user()->area;
        $query = AuditSesi::query()->latest();

        if (!empty($userArea)) {
            $query->where('area_audit', $userArea);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $auditSesis = $query->paginate(10);

        return view('auditor.audit.index', compact('auditSesis', 'userArea'));
    }

    /**
     * Display audit summary rekap for auditor/auditee.
     */
    public function rekap($id)
    {
        $userArea = auth()->user()->area;
        $query = AuditSesi::query();

        if (!empty($userArea)) {
            $query->where('area_audit', $userArea);
        }

        $sesi = $query->findOrFail($id);
        $rekap = $sesi->getRekapPerElemen();
        $skorAkhir = $sesi->skor_akhir ?? $sesi->hitungSkorAkhir();

        return view('auditor.audit.rekap', compact('sesi', 'rekap', 'skorAkhir'));
    }

    /**
     * Display printable report view according to Kepdirjen 185.
     */
    public function cetak($id)
    {
        $userArea = auth()->user()->area;
        $query = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen']);

        if (!empty($userArea) && !auth()->user()->isAdmin()) {
            $query->where('area_audit', $userArea);
        }

        $sesi = $query->findOrFail($id);
        $rekap = $sesi->getRekapPerElemen();
        $skorAkhir = $sesi->skor_akhir ?? $sesi->hitungSkorAkhir();

        return view('auditor.audit.cetak', compact('sesi', 'rekap', 'skorAkhir'));
    }

    /**
     * Export audit session rekap & criteria matrix to official Excel (.xlsx) file (TT-MGT-FRS-026B).
     */
    public function exportExcel($id)
    {
        $userArea = auth()->user()->area;
        $query = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen']);

        if (!empty($userArea) && !auth()->user()->isAdmin()) {
            $query->where('area_audit', $userArea);
        }

        $sesi = $query->findOrFail($id);

        $fileName = 'TT-MGT-FRS-026B_Audit_SMKP_' . str_replace(' ', '_', $sesi->area_audit) . '_' . $sesi->tanggal_mulai->format('Y-m-d') . '.xlsx';

        return Excel::download(new AuditSesiExport($sesi), $fileName);
    }
}
