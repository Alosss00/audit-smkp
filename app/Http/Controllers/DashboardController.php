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
        // Global PICA Stats Summary (Agregasi berbasis Sub-Elemen per Sesi Audit)
        $allPicas = \App\Models\Pica::with(['auditDetail.kriteria', 'auditDetail.auditSesi'])->get();
        
        $groupedSubTemuan = $allPicas->groupBy(function($p) {
            $sesiId = $p->auditDetail->audit_sesi_id ?? 0;
            $subId = $p->auditDetail->kriteria->sub_elemen_id ?? 0;
            return "{$sesiId}_{$subId}";
        });

        $subTotal = $groupedSubTemuan->count();
        $subOpen = 0;
        $subInProgress = 0;
        $subClosed = 0;

        foreach ($groupedSubTemuan as $subGroup) {
            if ($subGroup->contains('status', 'open')) {
                $subOpen++;
            } elseif ($subGroup->contains('status', 'in_progress')) {
                $subInProgress++;
            } else {
                $subClosed++;
            }
        }

        $stats = [
            'total_elemens'      => Elemen::count(),
            'total_sub_elemens'  => SubElemen::count(),
            'total_kriterias'    => Kriteria::count(),
            'total_users'        => User::count(),
            'total_audits'       => AuditSesi::count(),
            'audits_selesai'     => AuditSesi::where('status', 'selesai')->count(),
            'audits_berjalan'    => AuditSesi::where('status', 'berjalan')->count(),
            'total_pica'         => $subTotal,
            'open_pica'          => $subOpen,
            'in_progress_pica'   => $subInProgress,
            'closed_pica'        => $subClosed,
            'total_pica_kriteria'      => $allPicas->count(),
            'open_pica_kriteria'       => $allPicas->where('status', 'open')->count(),
            'in_progress_pica_kriteria'=> $allPicas->where('status', 'in_progress')->count(),
            'closed_pica_kriteria'     => $allPicas->where('status', 'closed')->count(),
            'kritikal_pica'      => $allPicas->where('kategori_temuan', 'kritikal')->count(),
            'mayor_pica'         => $allPicas->where('kategori_temuan', 'mayor')->count(),
            'minor_pica'         => $allPicas->where('kategori_temuan', 'minor')->count(),
        ];

        $elemens = Elemen::orderBy('kode_elemen')->get();
        $findingLabels = [];
        $findingCounts = [];

        // 1. Average Compliance Percentage per Elemen across all audit sessions
        $allSessions = AuditSesi::with(['auditDetails.kriteria.subElemen'])->get();
        $totalSessionsCount = $allSessions->count();

        $elementLabels = [];
        $elementScores = [];
        $elementColors = [];
        $elementFullNames = [];

        foreach ($elemens as $el) {
            $elementLabels[] = 'Elemen ' . $el->kode_elemen;
            $elementFullNames[] = 'Elemen ' . $el->kode_elemen . ': ' . $el->nama_elemen;

            if ($totalSessionsCount === 0) {
                $elementScores[] = 0;
                $elementColors[] = 'rgba(148, 163, 184, 0.75)';
                continue;
            }

            $sessionPercentages = [];
            foreach ($allSessions as $session) {
                $details = $session->auditDetails->filter(function ($d) use ($el) {
                    return $d->kriteria
                        && $d->kriteria->subElemen
                        && $d->kriteria->subElemen->elemen_id == $el->id;
                });

                $elAktual = 0;
                $elMaks = 0;
                foreach ($details as $d) {
                    if (!$d->is_na) {
                        $elAktual += (float) $d->nilai;
                        $elMaks += (float) ($d->kriteria->nilai_maksimal ?? 4);
                    }
                }

                if ($elMaks > 0) {
                    $sessionPercentages[] = ($elAktual / $elMaks) * 100;
                }
            }

            $avgScore = count($sessionPercentages) > 0
                ? round(array_sum($sessionPercentages) / count($sessionPercentages), 2)
                : 0;

            $elementScores[] = $avgScore;

            if ($avgScore >= 80) {
                $elementColors[] = 'rgba(34, 197, 94, 0.75)'; // Green (>= 80%)
            } elseif ($avgScore >= 70) {
                $elementColors[] = 'rgba(234, 179, 8, 0.75)'; // Yellow (70-79%)
            } else {
                $elementColors[] = 'rgba(239, 68, 68, 0.75)'; // Red (< 70%)
            }
        }

        // 2. Audit Findings Frequency per Elemen (100% scale per elemen)
        $findingsPerElemen = [];
        $totalAllFindings = 0;
        $findingTotalsPerElemen = [];
        $findingPercentages = [];

        foreach ($elemens as $el) {
            $findingLabels[] = 'Elemen ' . $el->kode_elemen;

            // Total non-NA criteria assessed across sessions for this element (Basis 100% elemen)
            $totalAssessedInElement = AuditDetail::whereHas('kriteria.subElemen', function ($q) use ($el) {
                $q->where('elemen_id', $el->id);
            })->where('is_na', false)->count();

            // Total criteria with findings in this element
            $count = AuditDetail::whereHas('kriteria.subElemen', function ($q) use ($el) {
                $q->where('elemen_id', $el->id);
            })
            ->where(function ($q) {
                $q->whereNotNull('catatan')->where('catatan', '!=', '')
                  ->orWhere(function ($q2) {
                      $q2->where('is_na', false)->whereRaw('nilai < (SELECT nilai_maksimal FROM kriterias WHERE kriterias.id = audit_details.kriteria_id)');
                  });
            })->count();

            $pct = $totalAssessedInElement > 0 ? round(($count / $totalAssessedInElement) * 100, 1) : 0;

            $findingCounts[] = $count;
            $findingTotalsPerElemen[] = $totalAssessedInElement;
            $findingPercentages[] = $pct;
            $totalAllFindings += $count;

            $findingsPerElemen[] = [
                'kode_elemen'    => $el->kode_elemen,
                'nama_elemen'    => $el->nama_elemen,
                'total_findings' => $count,
                'total_assessed' => $totalAssessedInElement,
                'percentage'     => $pct,
            ];
        }

        usort($findingsPerElemen, function ($a, $b) {
            return $b['percentage'] <=> $a['percentage'] ?: $b['total_findings'] <=> $a['total_findings'];
        });

        $topFindings = array_slice($findingsPerElemen, 0, 5);

        return view('admin.dashboard', compact(
            'stats',
            'elementLabels',
            'elementScores',
            'elementColors',
            'elementFullNames',
            'findingLabels',
            'findingCounts',
            'findingTotalsPerElemen',
            'findingPercentages',
            'totalAllFindings',
            'topFindings'
        ));
    }

    /**
     * Auditor (Auditee / PIC Area) Dashboard scoped to user's assigned area.
     */
    public function auditor()
    {
        $userArea = auth()->user()->area;
        if (empty($userArea) && !auth()->user()->isAdmin()) {
            abort(403, 'Akun Anda belum ditugaskan ke area manapun. Hubungi Administrator.');
        }

        // Base AuditSesi query filtered by user's assigned area
        $auditQuery = AuditSesi::query();
        if (!empty($userArea)) {
            $auditQuery->where('area_audit', $userArea);
        }

        $recentAudits = (clone $auditQuery)->latest()->take(5)->get();
        $allAuditorSessions = (clone $auditQuery)->latest()->get();

        // PICA Stats Summary for Auditor (Agregasi berbasis Sub-Elemen per Sesi Audit)
        $basePicas = \App\Models\Pica::whereHas('auditDetail.auditSesi', function ($q) use ($userArea) {
            if (!empty($userArea)) {
                $q->where('area_audit', $userArea);
            }
        })->with(['auditDetail.kriteria', 'auditDetail.auditSesi'])->get();

        $groupedSubTemuan = $basePicas->groupBy(function($p) {
            $sesiId = $p->auditDetail->audit_sesi_id ?? 0;
            $subId = $p->auditDetail->kriteria->sub_elemen_id ?? 0;
            return "{$sesiId}_{$subId}";
        });

        $subTotal = $groupedSubTemuan->count();
        $subOpen = 0;
        $subInProgress = 0;
        $subClosed = 0;

        foreach ($groupedSubTemuan as $subGroup) {
            if ($subGroup->contains('status', 'open')) {
                $subOpen++;
            } elseif ($subGroup->contains('status', 'in_progress')) {
                $subInProgress++;
            } else {
                $subClosed++;
            }
        }

        $stats = [
            'total_sesi'               => (clone $auditQuery)->count(),
            'total_pica'               => $subTotal,
            'open_pica'                => $subOpen,
            'in_progress'              => $subInProgress,
            'closed_pica'              => $subClosed,
            'total_pica_kriteria'      => $basePicas->count(),
            'open_pica_kriteria'       => $basePicas->where('status', 'open')->count(),
            'in_progress_pica_kriteria'=> $basePicas->where('status', 'in_progress')->count(),
            'closed_pica_kriteria'     => $basePicas->where('status', 'closed')->count(),
            'kritikal_pica'            => $basePicas->where('kategori_temuan', 'kritikal')->count(),
            'mayor_pica'               => $basePicas->where('kategori_temuan', 'mayor')->count(),
            'minor_pica'               => $basePicas->where('kategori_temuan', 'minor')->count(),
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

        // Findings Frequency per Elemen (100% scale per elemen)
        $elemens = Elemen::orderBy('kode_elemen')->get();
        $findingLabels = [];
        $findingCounts = [];
        $findingTotalsPerElemen = [];
        $findingPercentages = [];

        $findingsPerElemen = [];
        $totalAllFindings = 0;
        foreach ($elemens as $el) {
            $findingLabels[] = 'Elemen ' . $el->kode_elemen;

            // Total non-NA criteria assessed across sessions in user's area for this element (Basis 100% elemen)
            $totalAssessedInElement = AuditDetail::whereHas('auditSesi', function ($q) use ($userArea) {
                if (!empty($userArea)) {
                    $q->where('area_audit', $userArea);
                }
            })
            ->whereHas('kriteria.subElemen', function ($q) use ($el) {
                $q->where('elemen_id', $el->id);
            })->where('is_na', false)->count();

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

            $pct = $totalAssessedInElement > 0 ? round(($count / $totalAssessedInElement) * 100, 1) : 0;

            $findingCounts[] = $count;
            $findingTotalsPerElemen[] = $totalAssessedInElement;
            $findingPercentages[] = $pct;
            $totalAllFindings += $count;

            $findingsPerElemen[] = [
                'kode_elemen'    => $el->kode_elemen,
                'nama_elemen'    => $el->nama_elemen,
                'total_findings' => $count,
                'total_assessed' => $totalAssessedInElement,
                'percentage'     => $pct,
            ];
        }

        usort($findingsPerElemen, function ($a, $b) {
            return $b['percentage'] <=> $a['percentage'] ?: $b['total_findings'] <=> $a['total_findings'];
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
            'findingTotalsPerElemen',
            'findingPercentages',
            'totalAllFindings',
            'topFindings',
            'userArea'
        ));
    }
}
