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
     * Admin Dashboard with visual chart analytics & audit findings breakdown.
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
        $chartLabels = [];
        $chartData = [];
        $findingLabels = [];
        $findingCounts = [];

        $allSessions = AuditSesi::whereIn('status', ['berjalan', 'selesai'])->get();

        // 1. Average Compliance Percentage per Elemen
        foreach ($elemens as $el) {
            $chartLabels[] = 'Elemen ' . $el->kode_elemen;

            if ($allSessions->count() > 0) {
                $totalPercentageSum = 0;
                foreach ($allSessions as $session) {
                    $rekap = $session->getRekapPerElemen();
                    foreach ($rekap as $row) {
                        if ($row['elemen_id'] == $el->id) {
                            $totalPercentageSum += $row['persentase'];
                        }
                    }
                }
                $chartData[] = round($totalPercentageSum / $allSessions->count(), 2);
            } else {
                $chartData[] = 0;
            }
        }

        // 2. Audit Findings Frequency per Elemen
        $findingsPerElemen = [];
        foreach ($elemens as $el) {
            $findingLabels[] = 'Elemen ' . $el->kode_elemen;

            // Count audit details belonging to this element that have findings (catatan != null or nilai < nilai_maksimal)
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

        // Sort elements by highest number of findings
        usort($findingsPerElemen, function ($a, $b) {
            return $b['total_findings'] <=> $a['total_findings'];
        });

        $topFindings = array_slice($findingsPerElemen, 0, 5);

        return view('admin.dashboard', compact(
            'stats',
            'chartLabels',
            'chartData',
            'findingLabels',
            'findingCounts',
            'topFindings'
        ));
    }

    /**
     * Auditor Dashboard with visual chart analytics & personal audit findings breakdown.
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

        $latestAudit = AuditSesi::where('user_id', $userId)->latest()->first();
        $chartLabels = [];
        $chartData = [];
        $findingLabels = [];
        $findingCounts = [];

        $elemens = Elemen::orderBy('kode_elemen')->get();

        if ($latestAudit) {
            $rekap = $latestAudit->getRekapPerElemen();
            foreach ($rekap as $row) {
                $chartLabels[] = 'Elemen ' . $row['kode_elemen'];
                $chartData[] = $row['persentase'];
            }
        } else {
            foreach ($elemens as $el) {
                $chartLabels[] = 'Elemen ' . $el->kode_elemen;
                $chartData[] = 0;
            }
        }

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
            'latestAudit',
            'chartLabels',
            'chartData',
            'findingLabels',
            'findingCounts',
            'topFindings'
        ));
    }
}
