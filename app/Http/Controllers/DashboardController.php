<?php

namespace App\Http\Controllers;

use App\Models\AuditDetail;
use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\SubElemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Admin Dashboard with visual chart analytics & area audit score comparison.
     */
    public function admin()
    {
        $stats = [
            'total_elemens' => Elemen::count(),
            'total_sub_elemens' => SubElemen::count(),
            'total_kriterias' => Kriteria::count(),
            'total_users' => User::count(),
            'total_audits' => AuditSesi::count(),
            'audits_selesai' => AuditSesi::where('status', 'selesai')->count(),
            'audits_berjalan' => AuditSesi::where('status', 'berjalan')->count(),
        ];

        $elemens = Elemen::orderBy('kode_elemen')->get();
        $findingLabels = [];
        $findingCounts = [];

        // 1. Comparison of Final Compliance Score per Area Audit
        $allSessions = AuditSesi::with('user')->latest()->get();
        $areaLabels = [];
        $areaScores = [];
        $areaColors = [];

        foreach ($allSessions as $session) {
            $skor = (float) ($session->skor_akhir ?? $session->hitungSkorAkhir());
            $areaLabels[] = $session->area_audit;
            $areaScores[] = round($skor, 2);

            if ($skor >= 80) {
                $areaColors[] = 'rgba(34, 197, 94, 0.75)'; // Green (>= 80%)
            } elseif ($skor >= 70) {
                $areaColors[] = 'rgba(234, 179, 8, 0.75)'; // Yellow (70-79%)
            } else {
                $areaColors[] = 'rgba(239, 68, 68, 0.75)'; // Red (< 70%)
            }
        }

        // 2. Audit Findings Frequency per Elemen
        $findingsPerElemen = [];
        foreach ($elemens as $el) {
            $findingLabels[] = 'Elemen ' . $el->kode_elemen;

            $count = AuditDetail::whereHas('kriteria.subElemen', function ($q) use ($el) {
                $q->where('elemen_id', $el->id);
            })
            ->where(function ($q) {
                $q->whereNotNull('catatan')->where('catatan', '!=', '')
                  ->orWhere(function ($q2) {
                      $q2->where('is_na', false)->whereRaw('nilai < (SELECT nilai_maksimal FROM kriterias WHERE kriterias.id = audit_details.kriteria_id)');
                  });
            })->count();

            $findingCounts[] = $count;

            $findingsPerElemen[] = [
                'kode_elemen' => $el->kode_elemen,
                'nama_elemen' => $el->nama_elemen,
                'total_findings' => $count,
            ];
        }

        usort($findingsPerElemen, function ($a, $b) {
            return $b['total_findings'] <=> $a['total_findings'];
        });

        $topFindings = array_slice($findingsPerElemen, 0, 5);

        return view('admin.dashboard', compact(
            'stats',
            'areaLabels',
            'areaScores',
            'areaColors',
            'findingLabels',
            'findingCounts',
            'topFindings'
        ));
    }

    /**
     * Auditor Dashboard with area audit score comparison & personal audit findings breakdown.
     */
    public function auditor()
    {
        $userId = auth()->id();

        $stats = [
            'total_sesi' => AuditSesi::where('user_id', $userId)->count(),
            'draft' => AuditSesi::where('user_id', $userId)->where('status', 'draft')->count(),
            'berjalan' => AuditSesi::where('user_id', $userId)->where('status', 'berjalan')->count(),
            'selesai' => AuditSesi::where('user_id', $userId)->where('status', 'selesai')->count(),
        ];

        $recentAudits = AuditSesi::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $allAuditorSessions = AuditSesi::where('user_id', $userId)
            ->latest()
            ->get();

        $areaLabels = [];
        $areaScores = [];
        $areaColors = [];

        foreach ($allAuditorSessions as $session) {
            $skor = (float) ($session->skor_akhir ?? $session->hitungSkorAkhir());
            $areaLabels[] = $session->area_audit;
            $areaScores[] = round($skor, 2);

            if ($skor >= 80) {
                $areaColors[] = 'rgba(34, 197, 94, 0.75)'; // Green (>= 80%)
            } elseif ($skor >= 70) {
                $areaColors[] = 'rgba(234, 179, 8, 0.75)'; // Yellow (70-79%)
            } else {
                $areaColors[] = 'rgba(239, 68, 68, 0.75)'; // Red (< 70%)
            }
        }

        $elemens = Elemen::orderBy('kode_elemen')->get();
        $findingLabels = [];
        $findingCounts = [];

        // Auditor Findings Frequency per Elemen
        $findingsPerElemen = [];
        foreach ($elemens as $el) {
            $findingLabels[] = 'Elemen ' . $el->kode_elemen;

            $count = AuditDetail::whereHas('auditSesi', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereHas('kriteria.subElemen', function ($q) use ($el) {
                $q->where('elemen_id', $el->id);
            })
            ->where(function ($q) {
                $q->whereNotNull('catatan')->where('catatan', '!=', '')
                  ->orWhere(function ($q2) {
                      $q2->where('is_na', false)->whereRaw('nilai < (SELECT nilai_maksimal FROM kriterias WHERE kriterias.id = audit_details.kriteria_id)');
                  });
            })->count();

            $findingCounts[] = $count;

            $findingsPerElemen[] = [
                'kode_elemen' => $el->kode_elemen,
                'nama_elemen' => $el->nama_elemen,
                'total_findings' => $count,
            ];
        }

        usort($findingsPerElemen, function ($a, $b) {
            return $b['total_findings'] <=> $a['total_findings'];
        });

        $topFindings = array_slice($findingsPerElemen, 0, 5);

        return view('auditor.dashboard', compact(
            'stats',
            'recentAudits',
            'areaLabels',
            'areaScores',
            'areaColors',
            'findingLabels',
            'findingCounts',
            'topFindings'
        ));
    }
}
