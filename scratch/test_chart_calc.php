<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$elemens = App\Models\Elemen::orderBy('kode_elemen')->get();
$allSessions = App\Models\AuditSesi::with(['auditDetails.kriteria.subElemen.elemen', 'perusahaan'])->get();

$elementFullNames = [];
$accumulatedScores = [];
$accumulatedFindingCounts = [];
$accumulatedFindingTotals = [];
$accumulatedFindingPercentages = [];
$accumulatedFindingsPerElemen = [];

foreach ($elemens as $el) {
    $elementFullNames[] = 'Elemen ' . $el->kode_elemen . ': ' . $el->nama_elemen;
    
    $totalAktual = 0;
    $totalMaks = 0;
    $totalAssessed = 0;
    $totalFindings = 0;

    foreach ($allSessions as $session) {
        $details = $session->auditDetails->filter(function ($d) use ($el) {
            return $d->kriteria && $d->kriteria->subElemen && $d->kriteria->subElemen->elemen_id == $el->id;
        });

        foreach ($details as $d) {
            if (!$d->is_na) {
                $maxVal = (float)($d->kriteria->nilai_maksimal ?? 4);
                $val = (float)$d->nilai;
                
                $totalAktual += $val;
                $totalMaks += $maxVal;
                $totalAssessed++;

                if ($val < $maxVal) {
                    $totalFindings++;
                }
            }
        }
    }

    $avgScore = $totalMaks > 0 ? round(($totalAktual / $totalMaks) * 100, 2) : 0;
    $findingPct = $totalAssessed > 0 ? round(($totalFindings / $totalAssessed) * 100, 1) : 0;

    $accumulatedScores[] = $avgScore;
    $accumulatedFindingCounts[] = $totalFindings;
    $accumulatedFindingTotals[] = $totalAssessed;
    $accumulatedFindingPercentages[] = $findingPct;

    $accumulatedFindingsPerElemen[] = [
        'kode_elemen' => $el->kode_elemen,
        'nama_elemen' => $el->nama_elemen,
        'total_findings' => $totalFindings,
        'total_assessed' => $totalAssessed,
        'percentage' => $findingPct
    ];
}

usort($accumulatedFindingsPerElemen, function ($a, $b) {
    return $b['percentage'] <=> $a['percentage'] ?: $b['total_findings'] <=> $a['total_findings'];
});

echo "Accumulated Scores: " . json_encode($accumulatedScores) . "\n";
echo "Accumulated Finding %: " . json_encode($accumulatedFindingPercentages) . "\n";
echo "Top Accumulated Findings: " . json_encode(array_slice($accumulatedFindingsPerElemen, 0, 7), JSON_PRETTY_PRINT) . "\n";
