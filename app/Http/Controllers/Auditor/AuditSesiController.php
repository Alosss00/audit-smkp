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

        $query = AuditSesi::with(['user', 'perusahaan'])
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
        $skorAkhir = $sesi->hitungSkorAkhir();
        $gatingViolations = app(\App\Services\GatingRuleService::class)->evaluate($sesi);

        return view('auditor.audit.rekap', compact('sesi', 'rekap', 'hierarki', 'skorAkhir', 'gatingViolations'));
    }

    /**
     * Display printable report view according to Kepdirjen 185.
     */
    public function cetak($id)
    {
        $sesi      = $this->findAuditorSession($id);
        $rekap     = $sesi->getRekapPerElemen();
        $hierarki  = $sesi->getRekapHierarkis();
        $skorAkhir = $sesi->hitungSkorAkhir();

        return view('auditor.audit.cetak', compact('sesi', 'rekap', 'hierarki', 'skorAkhir'));
    }

    /**
     * Export audit session rekap & criteria matrix to official Excel (.xlsx) file.
     */
    public function exportExcel($id)
    {
        $sesi = $this->findAuditorSession($id);
        $safeArea = preg_replace('/[^A-Za-z0-9_\-]/', '_', $sesi->area_audit);
        $fileName = 'TT-MGT-FRS-026B_Audit_SMKP_' . $safeArea . '_' . ($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y-m-d') : date('Y-m-d')) . '.xlsx';

        return AuditSesiExport::downloadTemplateWithScores($sesi, $fileName);
    }

    /**
     * Display comprehensive audit detail report (Tree Matrix, Best Practices, PICA findings) for Auditor.
     */
    public function laporanDetail($id)
    {
        $sesi           = $this->findAuditorSession($id);
        $rekapElemen    = $sesi->getRekapPerElemen();
        $hierarki       = $sesi->buildMatrixTree();
        $praktekBaik    = $sesi->getSubElemenPraktekTerbaik();
        $temuanKategori = $sesi->getTemuanPerKategori();
        $skorAkhir      = $sesi->hitungSkorAkhir();
        $isReadOnly     = true;

        return view('laporan.detail', compact('sesi', 'rekapElemen', 'hierarki', 'praktekBaik', 'temuanKategori', 'skorAkhir', 'isReadOnly'));
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

        return AuditSesi::with(['user', 'perusahaan', 'auditDetails.kriteria.subElemen.elemen'])
            ->where('area_audit', 'like', '%' . $userArea . '%')
            ->findOrFail($id);
    }
}
