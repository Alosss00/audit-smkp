<?php

namespace App\Http\Controllers\Auditor;

use App\Exports\AuditSesiExport;
use App\Http\Controllers\Controller;
use App\Models\AuditSesi;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AuditSesiController extends Controller
{
    /**
     * Display a listing of audit sessions scoped to the auditor/auditee's area.
     */
    public function index(Request $request)
    {
        $userArea = auth()->user()->area;
        if (empty($userArea)) {
            abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun. Hubungi Administrator.');
        }

        $query = AuditSesi::with(['user', 'perusahaan', 'departemen'])
            ->where('area_audit', 'like', '%' . $userArea . '%')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('perusahaan_id')) {
            $query->where('perusahaan_id', $request->perusahaan_id);
        }

        $auditSesis = $query->paginate(10);
        $perusahaans = Perusahaan::where('is_active', true)->orderBy('nama_perusahaan')->get();

        return view('auditor.audit.index', compact('auditSesis', 'userArea', 'perusahaans'));
    }

    /**
     * Display audit summary rekap (read-only) for auditor/auditee.
     */
    public function rekap($id)
    {
        $sesi      = $this->findAuditorSession($id);
        $rekap     = $sesi->getRekapPerElemen();
        $hierarki  = $sesi->getRekapHierarkis();
        $skorAkhir = $sesi->skor_akhir ?? $sesi->hitungSkorAkhir();

        return view('auditor.audit.rekap', compact('sesi', 'rekap', 'hierarki', 'skorAkhir'));
    }

    /**
     * Display printable report view according to Kepdirjen 185.
     */
    public function cetak($id)
    {
        $sesi      = $this->findAuditorSession($id);
        $rekap     = $sesi->getRekapPerElemen();
        $skorAkhir = $sesi->skor_akhir ?? $sesi->hitungSkorAkhir();

        return view('auditor.audit.cetak', compact('sesi', 'rekap', 'skorAkhir'));
    }

    /**
     * Export audit session rekap & criteria matrix to official Excel (.xlsx) file.
     */
    public function exportExcel($id)
    {
        $sesi = $this->findAuditorSession($id);
        $fileName = 'TT-MGT-FRS-026B_Audit_SMKP_' . str_replace(' ', '_', $sesi->area_audit) . '_' . $sesi->tanggal_mulai->format('Y-m-d') . '.xlsx';

        return Excel::download(new AuditSesiExport($sesi), $fileName);
    }

    /**
     * Helper to find audit session scoped to auditor's area.
     */
    private function findAuditorSession($id)
    {
        $userArea = auth()->user()->area;
        if (empty($userArea)) {
            abort(403, 'Akun Anda belum ditugaskan ke area kerja manapun. Hubungi Administrator.');
        }

        return AuditSesi::with(['user', 'perusahaan', 'departemen', 'auditDetails.kriteria.subElemen.elemen'])
            ->where('area_audit', 'like', '%' . $userArea . '%')
            ->findOrFail($id);
    }
}
