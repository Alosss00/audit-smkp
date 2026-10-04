<?php
$title = 'Dashboard Auditor Internal Perusahaan — SMKP Minerba';

ob_start();
?>
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-clipboard-check-fill text-info me-2"></i>Dashboard Auditor Internal Perusahaan
        </h2>
        <p class="text-muted mb-0">Selamat datang, <strong><?php echo e(auth()->user()->name); ?></strong>. Area Kerja: <span class="badge bg-primary fs-6"><?php echo e($userArea ?? 'Semua Area'); ?></span></p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Sesi Audit Area</div>
                    <h3 class="fw-bold text-slate-800 mb-0"><?php echo e($stats['total_sesi']); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">PICA Status Open</div>
                    <h3 class="fw-bold text-slate-800 mb-0"><?php echo e($stats['open_pica']); ?></h3>
                    <small class="text-muted" style="font-size: 0.72rem;"><?php echo number_format($stats['open_pica_kriteria']); ?> Kriteria Open</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">PICA In Progress</div>
                    <h3 class="fw-bold text-slate-800 mb-0"><?php echo e($stats['in_progress']); ?></h3>
                    <small class="text-muted" style="font-size: 0.72rem;"><?php echo number_format($stats['in_progress_pica_kriteria']); ?> Kriteria In Progress</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">PICA Closed</div>
                    <h3 class="fw-bold text-slate-800 mb-0"><?php echo e($stats['closed_pica']); ?></h3>
                    <small class="text-muted" style="font-size: 0.72rem;"><?php echo number_format($stats['closed_pica_kriteria']); ?> Kriteria Selesai</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visual Charts Section -->
<div class="row g-4 mb-4">
    <!-- Bar Chart Comparison of Compliance Scores Across Area Audits -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold text-slate-800 mb-0"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Perbandingan Nilai Audit Area</h5>
                    <small class="text-muted">Grafik evaluasi pencapaian skor akhir SMKP area Anda</small>
                </div>
            </div>
            <div style="height: 220px;">
                <canvas id="auditorBarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Horizontal Bar Chart Audit Findings Frequency -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Frekuensi Temuan Audit per Elemen</h5>
                    <p class="text-muted small mb-0">Grafik persentase akumulasi temuan ketidaksesuaian pada area kerja Anda.</p>
                </div>
                <?php if (isset($totalAllFindings) && $totalAllFindings > 0): ?>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                        Total: <?php echo number_format($totalAllFindings); ?> Temuan
                    </span>
                <?php endif; ?>
            </div>
            <div style="height: 220px;">
                <canvas id="auditorFindingsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Audit Sessions Table -->
<div class="card card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Sesi Audit Area Terbaru</h5>
        <a href="<?php echo route('auditor.audit-sesi.index'); ?>" class="btn btn-sm btn-outline-secondary rounded-3">Lihat Semua</a>
    </div>

    <?php if ($recentAudits->isEmpty()): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox display-4 d-block mb-3 opacity-50"></i>
            <p class="mb-0">Belum ada sesi audit pada area kerja Anda.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Periode Audit</th>
                        <th>Area Audit</th>
                        <th>Status</th>
                        <th>Progres Penilaian</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAudits as $audit): ?>
                        <tr>
                            <td><?php echo e($audit->tanggal_mulai->format('d M Y')); ?> - <?php echo e($audit->tanggal_selesai->format('d M Y')); ?></td>
                            <td class="fw-semibold"><?php echo e($audit->area_audit); ?></td>
                            <td>
                                <?php if ($audit->status === 'draft'): ?>
                                    <span class="badge bg-secondary">Draft</span>
                                <?php elseif ($audit->status === 'berjalan'): ?>
                                    <span class="badge bg-warning text-dark">Berjalan</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $progress = $audit->hitungProgressPenilaian();
                                ?>
                                <div class="d-flex align-items-center gap-2" style="min-width: 110px;">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar <?php echo $progress == 100 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-danger'); ?>" 
                                             role="progressbar" style="width: <?php echo $progress; ?>%"></div>
                                    </div>
                                    <span class="fw-bold <?php echo $progress == 100 ? 'text-success' : ($progress >= 50 ? 'text-warning' : 'text-danger'); ?>">
                                        <?php echo number_format($progress, 1); ?>%
                                    </span>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group gap-1">
                                    <a href="<?php echo route('auditor.audit-sesi.rekap', $audit->id); ?>" class="btn btn-sm btn-outline-info text-dark rounded-2">
                                        <i class="bi bi-bar-chart-line me-1"></i> Rekap
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();

ob_start();
?>
<script nonce="<?php echo e($cspNonce ?? ''); ?>">
    document.addEventListener('DOMContentLoaded', function() {
        // Bar Chart Comparison Across Area Audits
        const ctxBar = document.getElementById('auditorBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($areaLabels); ?>,
                datasets: [{
                    label: 'Skor Akhir Pencapaian (%)',
                    data: <?php echo json_encode($areaScores); ?>,
                    backgroundColor: <?php echo json_encode($areaColors); ?>,
                    borderColor: 'rgba(15, 23, 42, 0.1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Horizontal Bar Chart Findings (Skala 100% per Elemen)
        const ctxFindings = document.getElementById('auditorFindingsChart').getContext('2d');
        const rawFindingCounts = <?php echo json_encode($findingCounts); ?>;
        const rawFindingTotals = <?php echo json_encode($findingTotalsPerElemen ?? []); ?>;
        new Chart(ctxFindings, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($findingLabels); ?>,
                datasets: [{
                    label: 'Persentase Temuan per Elemen (%)',
                    data: <?php echo json_encode($findingPercentages); ?>,
                    backgroundColor: 'rgba(239, 68, 68, 0.75)',
                    borderColor: '#ef4444',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const count = rawFindingCounts[context.dataIndex] || 0;
                                const total = rawFindingTotals[context.dataIndex] || 0;
                                return `Persentase Temuan: ${context.parsed.x}% (${count}/${total} Kriteria)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
<?php
$scripts = ob_get_clean();

echo view('layouts.app', get_defined_vars())->render();
