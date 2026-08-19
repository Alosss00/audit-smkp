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
        if (!auth()->user()->isAdmin()) {
            $userArea = auth()->user()->area;
            if (empty($userArea)) {
                abort(403, 'Akun Anda belum ditugaskan ke area manapun. Hubungi Administrator.');
            }
        } else {
            $userArea = null;
        }

        $query = AuditSesi::with(['user', 'perusahaan'])->latest();

        if (!empty($userArea)) {
            $query->where('area_audit', $userArea);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('perusahaan_id')) {
            $query->where('perusahaan_id', $request->perusahaan_id);
        }

        $auditSesis = $query->paginate(10);
        $perusahaans = \App\Models\Perusahaan::where('is_active', true)->orderBy('nama_perusahaan')->get();

        return view('auditor.audit.index', compact('auditSesis', 'userArea', 'perusahaans'));
    }

    /**
     * Display audit summary rekap for auditor/auditee.
     */
    public function rekap($id)
    {
        if (!auth()->user()->isAdmin()) {
            $userArea = auth()->user()->area;
            if (empty($userArea)) {
                abort(403, 'Akun Anda belum ditugaskan ke area manapun. Hubungi Administrator.');
            }
        } else {
            $userArea = null;
        }

        $query = AuditSesi::query();

        if (!empty($userArea)) {
            $query->where('area_audit', $userArea);
        }

        $sesi = $query->findOrFail($id);
        $rekap = $sesi->getRekapPerElemen();
        $hierarki = $sesi->getRekapHierarkis();
        $skorAkhir = $sesi->skor_akhir ?? $sesi->hitungSkorAkhir();

        return view('auditor.audit.rekap', compact('sesi', 'rekap', 'hierarki', 'skorAkhir'));
    }

    /**
     * Display printable report view according to Kepdirjen 185.
     */
    public function cetak($id)
    {
        if (!auth()->user()->isAdmin()) {
            $userArea = auth()->user()->area;
            if (empty($userArea)) {
                abort(403, 'Akun Anda belum ditugaskan ke area manapun. Hubungi Administrator.');
            }
        } else {
            $userArea = null;
        }

        $query = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen']);

        if (!empty($userArea)) {
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
        if (!auth()->user()->isAdmin()) {
            $userArea = auth()->user()->area;
            if (empty($userArea)) {
                abort(403, 'Akun Anda belum ditugaskan ke area manapun. Hubungi Administrator.');
            }
        } else {
            $userArea = null;
        }

        $query = AuditSesi::with(['user', 'auditDetails.kriteria.subElemen.elemen']);

        if (!empty($userArea)) {
            $query->where('area_audit', $userArea);
        }

        $sesi = $query->findOrFail($id);

        $fileName = 'TT-MGT-FRS-026B_Audit_SMKP_' . str_replace(' ', '_', $sesi->area_audit) . '_' . $sesi->tanggal_mulai->format('Y-m-d') . '.xlsx';

        return Excel::download(new AuditSesiExport($sesi), $fileName);
    }
}
