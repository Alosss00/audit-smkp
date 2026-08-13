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
            'total_elemens'     => Elemen::count(),
            'total_sub_elemens' => SubElemen::count(),
            'total_kriterias'   => Kriteria::count(),
            'total_users'       => User::count(),
            'total_audits'      => AuditSesi::count(),
            'audits_selesai'    => AuditSesi::where('status', 'selesai')->count(),
            'audits_berjalan'   => AuditSesi::where('status', 'berjalan')->count(),
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
                'kode_elemen'    => $el->kode_elemen,
                'nama_elemen'    => $el->nama_elemen,
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
     * Auditor (Auditee / PIC Area) Dashboard scoped to user's assigned area.
     */
    public function auditor()
    {
        $userArea = auth()->user()->area;

        // Base AuditSesi query filtered by user's assigned area (if area is set)
        $auditQuery = AuditSesi::query();
        if (!empty($userArea)) {
            $auditQuery->where('area_audit', $userArea);
        }

        $recentAudits = (clone $auditQuery)->latest()->take(5)->get();
        $allAuditorSessions = (clone $auditQuery)->latest()->get();

        // Base Pica query filtered by user's area
        $picaQuery = \App\Models\Pica::whereHas('auditDetail.auditSesi', function ($q) use ($userArea) {
            if (!empty($userArea)) {
                $q->where('area_audit', $userArea);
            }
        });

        $stats = [
            'total_sesi'  => (clone $auditQuery)->count(),
            'total_pica'  => (clone $picaQuery)->count(),
            'open_pica'   => (clone $picaQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $picaQuery)->where('status', 'in_progress')->count(),
            'closed_pica' => (clone $picaQuery)->where('status', 'closed')->count(),
        ];

        $areaLabels = [];
        $areaScores = [];
        $areaColors = [];

        foreach ($allAuditorSessions as $session) {
            $skor = (float) ($session->skor_akhir ?? $session->hitungSkorAkhir());
            $areaLabels[] = $session->area_audit;
            $areaScores[] = round($skor, 2);

            if ($skor >= 80) {
                $areaColors[] = 'rgba(34, 197, 94, 0.75)';
            } elseif ($skor >= 70) {
                $areaColors[] = 'rgba(234, 179, 8, 0.75)';
            } else {
                $areaColors[] = 'rgba(239, 68, 68, 0.75)';
            }
        }

        $elemens = Elemen::orderBy('kode_elemen')->get();
        $findingLabels = [];
        $findingCounts = [];

        $findingsPerElemen = [];
        foreach ($elemens as $el) {
            $findingLabels[] = 'Elemen ' . $el->kode_elemen;

            $count = AuditDetail::whereHas('auditSesi', function ($q) use ($userArea) {
                if (!empty($userArea)) {
                    $q->where('area_audit', $userArea);
                }
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
                'kode_elemen'    => $el->kode_elemen,
                'nama_elemen'    => $el->nama_elemen,
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
            'topFindings',
            'userArea'
        ));
    }
}
