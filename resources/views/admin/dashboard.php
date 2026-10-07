<?php
$title = 'Dashboard Auditor Internal SMKP — SMKP Minerba';

ob_start();
?>
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-speedometer2 text-danger me-2"></i>Dashboard Auditor Internal SMKP
        </h2>
        <p class="text-muted mb-0">Selamat datang, <strong><?php echo e(auth()->user()->name); ?></strong>. Pengawasan audit internal, pembuatan sesi, penilaian matriks, dan otoritas PICA SMKP Minerba.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
        <a href="<?php echo route('admin.audit-sesi.create'); ?>" class="btn btn-danger rounded-3 shadow-sm px-3 py-2 fw-semibold">
            <i class="bi bi-plus-circle me-1"></i> Buat Sesi Audit
        </a>
        <a href="<?php echo route('admin.pica.index'); ?>" class="btn btn-outline-danger rounded-3 px-3 py-2 fw-semibold">
            <i class="bi bi-tools me-1"></i> Monitoring PICA
        </a>
    </div>
</div>

<!-- Global Filter Bar: Perusahaan & Periode Audit -->
<div class="card card-custom p-3 mb-4 border-0 shadow-sm bg-white">
    <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" id="globalDashboardFilterForm" class="row g-3 align-items-center">
        <div class="col-md-5">
            <label for="filterPerusahaan" class="form-label small fw-semibold text-slate-600 mb-1">
                <i class="bi bi-building me-1 text-primary"></i>Perusahaan / Area Audit:
            </label>
            <select name="perusahaan_id" id="filterPerusahaan" class="form-select form-select-sm rounded-3 border-slate-300 shadow-none fw-semibold" onchange="this.form.submit()">
                <option value="semua" <?php echo ($selectedPerusahaan === 'semua' || empty($selectedPerusahaan)) ? 'selected' : ''; ?>>🏢 Semua Perusahaan (Gabungan)</option>
                <?php if(!empty($availablePerusahaans)): ?>
                    <?php foreach($availablePerusahaans as $p): ?>
                        <option value="<?php echo e($p->id); ?>" <?php echo ((string)$selectedPerusahaan === (string)$p->id) ? 'selected' : ''; ?>>
                            🏢 <?php echo e($p->nama_perusahaan); ?>

                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="col-md-5">
            <label for="filterTahunPeriode" class="form-label small fw-semibold text-slate-600 mb-1">
                <i class="bi bi-calendar-range me-1 text-primary"></i>Periode Audit (Tahun):
            </label>
            <select name="tahun_periode" id="filterTahunPeriode" class="form-select form-select-sm rounded-3 border-slate-300 shadow-none fw-semibold" onchange="this.form.submit()">
                <option value="semua" <?php echo ($selectedTahun === 'semua' || empty($selectedTahun)) ? 'selected' : ''; ?>>🗓️ Semua Tahun Periode</option>
                <?php if(!empty($availableYears)): ?>
                    <?php foreach($availableYears as $yr): ?>
                        <option value="<?php echo e($yr); ?>" <?php echo ((string)$selectedTahun === (string)$yr) ? 'selected' : ''; ?>>
                            📅 Tahun Periode <?php echo e($yr); ?>

                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end gap-2 pt-3 pt-md-0">
            <button type="submit" class="btn btn-sm btn-primary flex-fill rounded-3 py-1.5 fw-semibold" title="Terapkan Filter">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <?php if(($selectedPerusahaan && $selectedPerusahaan !== 'semua') || ($selectedTahun && $selectedTahun !== 'semua')): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-sm btn-outline-secondary rounded-3 py-1.5 px-2.5" title="Reset Filter ke Semua Data">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Stat Cards: Lead Auditor & Oversight KPIs -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total Sesi Audit</div>
                    <h3 class="fw-bold text-slate-800 mb-0"><?php echo e($stats['total_audits']); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-octagon-fill"></i>
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
                    <h3 class="fw-bold text-slate-800 mb-0"><?php echo e($stats['in_progress_pica']); ?></h3>
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

<!-- Visual Chart Analytics Section 1: Average Compliance & Session Status -->
<div class="row g-4 mb-4">
    <!-- Bar Chart Percentage per Elemen -->
    <div class="col-lg-8">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-slate-800" id="elementChartTitle">
                        <i class="bi bi-bar-chart-fill me-2 text-primary"></i>Pencapaian Nilai Audit per Elemen
                    </h5>
                    <p class="text-muted small mb-0" id="elementChartSubtitle">Grafik rata-rata persentase pencapaian nilai per elemen SMKP.</p>
                </div>
            </div>
            <div style="height: 380px;">
                <canvas id="elementBarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart Status -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-pie-chart-fill me-2 text-info"></i>Status Sesi Audit</h5>
            <p class="text-muted small mb-3">Distribusi status sesi audit internal yang terdaftar.</p>
            <div style="height: 350px; position: relative;">
                <canvas id="statusDoughnutChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Visual Chart Analytics Section: Multi-Year Grouped Bar Chart per Elemen (Batch Grafik per Tahun Periode) -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card card-custom p-4 border-start border-4 border-info">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1 text-slate-800">
                        <i class="bi bi-bar-chart-steps me-2 text-info"></i>Perbandingan Pencapaian Nilai Audit per Elemen Antar Tahun Periode
                    </h5>
                    <p class="text-muted small mb-0">Grafik komparasi batch (grouped bar & garis kurva tren) nilai audit per Elemen SMKP (I - VII) dari tahun ke tahun.</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <label for="multiYearElementFilter" class="small fw-semibold text-muted mb-0 d-flex align-items-center">
                        <i class="bi bi-funnel text-info me-1"></i>Filter Elemen:
                    </label>
                    <select id="multiYearElementFilter" class="form-select form-select-sm rounded-3 border-slate-300 fw-semibold shadow-none" style="min-width: 250px;">
                        <option value="all">🏢 Semua Elemen (I - VII)</option>
                        <?php if(!empty($elemens)): ?>
                            <?php foreach($elemens as $el): ?>
                                <option value="<?php echo e($el->id); ?>">
                                    📌 Elemen <?php echo e($el->kode_elemen); ?>: <?php echo e($el->nama_elemen); ?>

                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div style="height: 420px; position: relative;">
                <canvas id="multiYearGroupedBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Visual Chart Analytics Section 2: Accumulated Analytics Across All Audit Sessions -->
<div class="row g-4 mb-4">
    <!-- Top Left: Radar Chart Akumulasi -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100 border-start border-4 border-primary">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div>
                    <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-heptagon-fill me-2 text-primary"></i>Pencapaian Nilai Akumulasi per Elemen</h5>
                    <p class="text-muted small mb-0">Persentase rata-rata akumulasi pencapaian nilai per elemen SMKP (Gabungan Semua Perusahaan & Sesi Audit).</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 text-nowrap ms-2" data-bs-toggle="modal" data-bs-target="#radarChartModal">
                    <i class="bi bi-arrows-angle-expand me-1"></i>Perbesar
                </button>
            </div>
            <div style="min-height: 520px; height: 60vh; max-height: 750px; display: flex; justify-content: center; align-items: center;" class="mt-3">
                <canvas id="accumulatedRadarChart" style="max-width: 100%; width: 100%; height: 100%;"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Right: Top Elemen Paling Sering Ditemui Temuan (Akumulasi Gabungan) -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top Elemen Paling Sering Ditemui Temuan (Akumulasi Gabungan)</h5>
                    <p class="text-muted small mb-0">Peringkat elemen SMKP yang paling banyak memiliki catatan ketidaksesuaian kriteria dari seluruh sesi audit.</p>
                </div>
            </div>

            <div class="list-group list-group-flush border-0 mt-3">
                <?php if(!empty($accumulatedChartData['topAccumulatedFindings'])): ?>
                    <?php foreach($accumulatedChartData['topAccumulatedFindings'] as $index => $top): ?>
                        <div class="list-group-item d-flex align-items-center justify-content-between px-0 py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge <?php echo $index == 0 ? 'bg-danger' : ($index == 1 ? 'bg-warning text-dark' : 'bg-secondary'); ?> rounded-circle p-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                    <?php echo $index + 1; ?>
                                </span>
                                <div>
                                    <strong class="text-slate-800 d-block small">Elemen <?php echo e($top['kode_elemen']); ?>: <?php echo e($top['nama_elemen']); ?></strong>
                                </div>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 font-monospace fw-bold">
                                <?php echo e($top['percentage']); ?>% <small class="text-muted">(<?php echo e($top['total_findings']); ?>/<?php echo e($top['total_assessed']); ?>)</small>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">Belum ada data temuan audit akumulasi.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Full Width: Horizontal Bar Chart Findings Akumulasi -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card card-custom p-4 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-exclamation-octagon-fill me-2 text-danger"></i>Frekuensi Temuan Audit Akumulasi</h5>
                    <p class="text-muted small mb-0">Persentase temuan ketidaksesuaian/catatan audit per elemen SMKP (Gabungan Seluruh Sesi Audit).</p>
                </div>
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                    Total: <?php echo e(number_format($accumulatedChartData['totalAccumulatedFindings'] ?? 0)); ?> Temuan
                </span>
            </div>
            <div style="height: 320px;">
                <canvas id="accumulatedFindingsBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Modal Fullscreen / Extra Large Radar Chart -->
<div class="modal fade" id="radarChartModal" tabindex="-1" aria-labelledby="radarChartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg style-radius-16">
            <div class="modal-header text-white border-0 py-3" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
                <h5 class="modal-title fw-bold text-white" id="radarChartModalLabel">
                    <i class="bi bi-heptagon-fill me-2 text-info"></i>Grafik Pencapaian Nilai Akumulasi per Elemen (Ukuran Penuh)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <p class="text-muted small mb-3">Tampilan grafik radar pencapaian nilai akumulasi per elemen SMKP dengan resolusi besar dan kejelasan maksimal.</p>
                <div style="height: 720px; display: flex; justify-content: center; align-items: center;">
                    <canvas id="modalRadarChart" style="max-width: 100%; width: 100%; height: 100%;"></canvas>
                </div>
            </div>
            <div class="modal-footer bg-white border-top-0 py-2">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php if (auth()->user()->hasMasterDataAccess()): ?>
<!-- Master Data Quick Navigation -->
<h5 class="fw-bold mb-3"><i class="bi bi-grid-fill me-2 text-primary"></i>Kelola Master Data & User</h5>
<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Master Elemen</h5>
                <span class="badge bg-primary rounded-pill"><?php echo e($stats['total_elemens']); ?></span>
            </div>
            <p class="text-muted small">Kelola data elemen SMKP, kode elemen, nama elemen, dan bobot persen.</p>
            <a href="<?php echo route('admin.elemens.index'); ?>" class="btn btn-outline-primary btn-sm rounded-3 mt-auto">
                <i class="bi bi-gear me-1"></i> Kelola Elemen
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Sub-Elemen</h5>
                <span class="badge bg-info text-dark rounded-pill"><?php echo e($stats['total_sub_elemens']); ?></span>
            </div>
            <p class="text-muted small">Kelola turunan sub-elemen berdasarkan masing-masing elemen regulasi.</p>
            <a href="<?php echo route('admin.sub-elemens.index'); ?>" class="btn btn-outline-info text-dark btn-sm rounded-3 mt-auto">
                <i class="bi bi-gear me-1"></i> Kelola Sub-Elemen
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Master Kriteria</h5>
                <span class="badge bg-success rounded-pill"><?php echo e($stats['total_kriterias']); ?></span>
            </div>
            <p class="text-muted small">Kelola kriteria pertanyaan audit beserta deskripsi dan nilai maksimal.</p>
            <a href="<?php echo route('admin.kriterias.index'); ?>" class="btn btn-outline-success btn-sm rounded-3 mt-auto">
                <i class="bi bi-gear me-1"></i> Kelola Kriteria
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Kelola Users</h5>
                <span class="badge bg-warning text-dark rounded-pill"><?php echo e($stats['total_users']); ?></span>
            </div>
            <p class="text-muted small">Kelola data pengguna, role Administrator, Auditor SMKP, dan Auditor Perusahaan.</p>
            <a href="<?php echo route('admin.users.index'); ?>" class="btn btn-outline-warning text-dark btn-sm rounded-3 mt-auto">
                <i class="bi bi-gear me-1"></i> Kelola User
            </a>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Auditor SMKP Quick Action Links -->
<h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge-fill me-2 text-primary"></i>Akses Cepat Penilaian & Monitoring</h5>
<div class="row g-4">
    <div class="col-md-6 col-lg-4">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Kelola Sesi Audit</h5>
                <span class="badge bg-primary rounded-pill"><?php echo e($stats['total_audits']); ?> Sesi</span>
            </div>
            <p class="text-muted small">Kelola seluruh sesi evaluasi, input matriks penilaian kriteria, dan rekapitulasi nilai.</p>
            <a href="<?php echo route('admin.audit-sesi.index'); ?>" class="btn btn-outline-primary btn-sm rounded-3 mt-auto">
                <i class="bi bi-journal-check me-1"></i> Buka Sesi Audit
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Monitoring Audit</h5>
                <span class="badge bg-success rounded-pill"><?php echo e($stats['audits_selesai']); ?> Selesai</span>
            </div>
            <p class="text-muted small">Pantau status pelaksanaan audit internal lintas perusahaan dan unduh laporan.</p>
            <a href="<?php echo route('admin.rekap-audit.index'); ?>" class="btn btn-outline-success btn-sm rounded-3 mt-auto">
                <i class="bi bi-shield-check me-1"></i> Buka Monitoring
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Tindak Lanjut Temuan (PICA)</h5>
                <span class="badge bg-danger rounded-pill"><?php echo e($stats['open_pica'] + $stats['in_progress_pica']); ?> Aktif</span>
            </div>
            <p class="text-muted small">Verifikasi rencana koreksi/pencegahan ketidaksesuaian dan kelola penutupan temuan PICA.</p>
            <a href="<?php echo route('admin.pica.index'); ?>" class="btn btn-outline-danger btn-sm rounded-3 mt-auto">
                <i class="bi bi-tools me-1"></i> Buka Tindak Lanjut PICA
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();

ob_start();
?>
<script nonce="<?php echo e($cspNonce ?? ''); ?>">
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Bar Chart Average Compliance per Elemen & Multi-Year Trend Line Chart
        const ctxBar = document.getElementById('elementBarChart').getContext('2d');
        const elementFullNames = <?php echo json_encode($elementFullNames); ?>;
        const trendData = <?php echo json_encode($elementTrendData ?? []); ?>;

        const barLabels = elementFullNames.map(name => {
            const parts = name.split(': ');
            return [parts[0], parts[1] || ''];
        });

        const barTextPlugin = {
            id: 'barTextPlugin',
            afterDatasetsDraw(chart, args, pluginOptions) {
                const { ctx, data } = chart;
                if (!chart.getDatasetMeta(0) || !chart.getDatasetMeta(0).data) return;
                chart.getDatasetMeta(0).data.forEach((bar, index) => {
                    const value = data.datasets[0].data[index];
                    ctx.save();
                    ctx.font = 'bold 12px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    
                    let yPos = bar.y + 15;
                    ctx.fillStyle = '#ffffff'; 
                    
                    if (bar.height < 25) {
                        yPos = bar.y - 12;
                        ctx.fillStyle = '#1e293b';
                    }
                    
                    ctx.fillText(value + '%', bar.x, yPos);
                    ctx.restore();
                });
            }
        };

        let mainChartInstance = null;

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{
                    label: 'Rata-Rata Pencapaian (%)',
                    data: <?php echo json_encode($elementScores); ?>,
                    backgroundColor: <?php echo json_encode($elementColors); ?>,
                    borderColor: 'rgba(15, 23, 42, 0.1)',
                    borderWidth: 1,
                    borderRadius: 8,
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
                            stepSize: 10,
                            callback: function(value) { return value + '%'; }
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                const index = context[0].dataIndex;
                                return elementFullNames[index] || context[0].label;
                            },
                            label: function(context) {
                                return 'Rata-Rata Pencapaian: ' + context.raw + '%';
                            }
                        }
                    }
                }
            },
            plugins: [barTextPlugin]
        });

        // 2. Doughnut Chart Status
        <?php
            $cntBerjalan = (int)($stats['audits_berjalan'] ?? 0);
            $cntSelesai = (int)($stats['audits_selesai'] ?? 0);
            $cntTotal = (int)($stats['total_audits'] ?? 0);
            $cntDraft = max(0, $cntTotal - $cntBerjalan - $cntSelesai);
        ?>
        const ctxDoughnut = document.getElementById('statusDoughnutChart').getContext('2d');
        const countBerjalan = <?php echo $cntBerjalan; ?>;
        const countSelesai = <?php echo $cntSelesai; ?>;
        const countDraft = <?php echo $cntDraft; ?>;

        const doughnutTextPlugin = {
            id: 'doughnutTextPlugin',
            afterDatasetsDraw(chart) {
                const { ctx, data } = chart;
                const meta = chart.getDatasetMeta(0);
                if (!meta || !meta.data) return;

                const total = data.datasets[0].data.reduce((a, b) => a + Number(b), 0);

                meta.data.forEach((element, index) => {
                    const value = data.datasets[0].data[index];
                    if (value > 0) {
                        const { x, y } = element.getCenterPoint();
                        ctx.save();
                        ctx.font = 'bold 14px "Plus Jakarta Sans", sans-serif';
                        ctx.fillStyle = '#ffffff';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(value, x, y);
                        ctx.restore();
                    }
                });

                if (chart.chartArea) {
                    const centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                    const centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = 'bold 22px "Plus Jakarta Sans", sans-serif';
                    ctx.fillStyle = '#0f172a';
                    ctx.fillText(total, centerX, centerY - 8);
                    ctx.font = '600 11px "Plus Jakarta Sans", sans-serif';
                    ctx.fillStyle = '#64748b';
                    ctx.fillText('Total Sesi', centerX, centerY + 12);
                    ctx.restore();
                }
            }
        };

        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: [
                    'Berjalan (' + countBerjalan + ')',
                    'Selesai (' + countSelesai + ')',
                    'Draft (' + countDraft + ')'
                ],
                datasets: [{
                    data: [countBerjalan, countSelesai, countDraft],
                    backgroundColor: ['#f59e0b', '#10b981', '#64748b'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12,
                                weight: '600',
                                family: "'Plus Jakarta Sans', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                const rawLabel = context.label ? context.label.split(' (')[0] : '';
                                return ` ${rawLabel}: ${val} Sesi (${pct}%)`;
                            }
                        }
                    }
                }
            },
            plugins: [doughnutTextPlugin]
        });

        // 3. Accumulated Radar Chart
        const accumulatedData = <?php echo json_encode($accumulatedChartData ?? []); ?>;
        if (accumulatedData && accumulatedData.elementFullNames) {
            function wrapText(str, maxLength) {
                let res = [];
                while (str.length > maxLength) {
                    let found = false;
                    for (let i = maxLength - 1; i >= 0; i--) {
                        if (str.charAt(i) === ' ' || str.charAt(i) === '-') {
                            res.push(str.slice(0, i));
                            str = str.slice(i + 1);
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        res.push(str.slice(0, maxLength));
                        str = str.slice(maxLength);
                    }
                }
                res.push(str);
                return res;
            }

            const rawElementNames = accumulatedData.elementFullNames || [];
            const accumulatedScores = accumulatedData.accumulatedScores || [];
            
            const radarLabels = rawElementNames.map((name, i) => {
                let lines = wrapText(name, 26);
                lines.push('(' + (accumulatedScores[i] || 0) + '%)');
                return lines;
            });

            const ctxRadar = document.getElementById('accumulatedRadarChart');
            if (ctxRadar) {
                new Chart(ctxRadar.getContext('2d'), {
                    type: 'radar',
                    data: {
                        labels: radarLabels,
                        datasets: [{
                            label: 'Pencapaian Akumulasi (%)',
                            data: accumulatedScores,
                            backgroundColor: 'rgba(2, 132, 199, 0.28)',
                            borderColor: '#0284c7',
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#ef4444',
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            borderWidth: 2.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 100,
                                pointLabels: {
                                    font: { size: 13, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                                    color: '#1e293b'
                                },
                                ticks: {
                                    stepSize: 10,
                                    backdropColor: 'rgba(255, 255, 255, 0.85)',
                                    font: { size: 10, weight: 'bold' },
                                    callback: function(value) { return value + '%'; }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) { return 'Pencapaian Akumulasi: ' + context.raw + '%'; }
                                }
                            }
                        }
                    }
                });
            }

            // Modal Fullscreen Radar Chart Initialization
            const radarModalEl = document.getElementById('radarChartModal');
            if (radarModalEl) {
                let modalRadarChartInstance = null;
                const modalLabels = rawElementNames.map((name, i) => {
                    let lines = wrapText(name, 35);
                    lines.push('(' + (accumulatedScores[i] || 0) + '%)');
                    return lines;
                });

                radarModalEl.addEventListener('shown.bs.modal', function () {
                    const ctxModal = document.getElementById('modalRadarChart');
                    if (ctxModal && !modalRadarChartInstance) {
                        modalRadarChartInstance = new Chart(ctxModal.getContext('2d'), {
                            type: 'radar',
                            data: {
                                labels: modalLabels,
                                datasets: [{
                                    label: 'Pencapaian Akumulasi (%)',
                                    data: accumulatedScores,
                                    backgroundColor: 'rgba(2, 132, 199, 0.28)',
                                    borderColor: '#0284c7',
                                    pointBackgroundColor: '#ef4444',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#ef4444',
                                    pointRadius: 6,
                                    pointHoverRadius: 9,
                                    borderWidth: 3
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    r: {
                                        beginAtZero: true,
                                        max: 100,
                                        pointLabels: {
                                            font: { size: 14, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                                            color: '#0f172a'
                                        },
                                        ticks: {
                                            stepSize: 10,
                                            backdropColor: 'rgba(255, 255, 255, 0.9)',
                                            font: { size: 11, weight: 'bold' },
                                            callback: function(value) { return value + '%'; }
                                        }
                                    }
                                },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) { return 'Pencapaian Akumulasi: ' + context.raw + '%'; }
                                        }
                                    }
                                }
                            }
                        });
                    } else if (modalRadarChartInstance) {
                        modalRadarChartInstance.resize();
                    }
                });
            }

            // 4. Horizontal Bar Chart Accumulated Findings
            const hBarTextPlugin = {
                id: 'hBarTextPlugin',
                afterDatasetsDraw(chart, args, pluginOptions) {
                    const { ctx, data } = chart;
                    chart.getDatasetMeta(0).data.forEach((bar, index) => {
                        const value = data.datasets[0].data[index];
                        ctx.save();
                        ctx.font = 'bold 12px sans-serif';
                        ctx.textBaseline = 'middle';
                        
                        if (value > 0) {
                            let xPos = bar.x - 10;
                            ctx.fillStyle = '#ffffff'; 
                            ctx.textAlign = 'right';
                            
                            if (bar.width < 35) {
                                xPos = bar.x + 10;
                                ctx.fillStyle = '#1e293b';
                                ctx.textAlign = 'left';
                            }
                            ctx.fillText(value + '%', xPos, bar.y);
                        } else {
                            ctx.fillStyle = '#1e293b';
                            ctx.textAlign = 'left';
                            ctx.fillText('0%', bar.x + 10, bar.y);
                        }
                        ctx.restore();
                    });
                }
            };

            const ctxFindings = document.getElementById('accumulatedFindingsBarChart');
            if (ctxFindings) {
                const rawFindingCounts = accumulatedData.accumulatedFindingCounts || [];
                const rawFindingTotals = accumulatedData.accumulatedFindingTotalsPerElemen || [];

                new Chart(ctxFindings.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: accumulatedData.findingLabels || [],
                        datasets: [{
                            label: 'Persentase Temuan Akumulasi (%)',
                            data: accumulatedData.accumulatedFindingPercentages || [],
                            backgroundColor: 'rgba(239, 68, 68, 0.75)',
                            borderColor: '#ef4444',
                            borderWidth: 2,
                            borderRadius: 6
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
                                ticks: { stepSize: 10, callback: function(val) { return val + '%'; } }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const count = rawFindingCounts[context.dataIndex] || 0;
                                        const total = rawFindingTotals[context.dataIndex] || 0;
                                        return `Temuan Akumulasi: ${context.parsed.x}% (${count}/${total} Kriteria)`;
                                    }
                                }
                            }
                        }
                    },
                    plugins: [hBarTextPlugin]
                });
            }
        }

        // 5. Multi-Year Grouped Bar Chart per Elemen (Batch Grafik per Tahun Periode)
        const ctxMultiYear = document.getElementById('multiYearGroupedBarChart');
        if (ctxMultiYear) {
            const trendYears = <?php echo json_encode($elementTrendData['years'] ?? []); ?>;
            const trendElements = <?php echo json_encode($elementTrendData['elements'] ?? []); ?>;

            const elementPalette = [
                '#4f46e5', // Elemen I - Indigo/Blue
                '#ef4444', // Elemen II - Red/Orange
                '#10b981', // Elemen III - Emerald Green
                '#8b5cf6', // Elemen IV - Purple
                '#f59e0b', // Elemen V - Amber
                '#06b6d4', // Elemen VI - Cyan
                '#ec4899'  // Elemen VII - Pink
            ];

            const yearLabels = trendYears.map(y => 'Tahun ' + y);

            function getOverallAveragePerYear(companyType = 'all') {
                const yearsCount = trendYears.length;
                const totals = new Array(yearsCount).fill(0);
                const counts = new Array(yearsCount).fill(0);

                Object.values(trendElements).forEach(el => {
                    let scoreArr = el.scores || [];
                    if (companyType === 'msm') scoreArr = el.msmScores || [];
                    else if (companyType === 'ttn') scoreArr = el.ttnScores || [];

                    scoreArr.forEach((sc, i) => {
                        if (sc !== null && sc !== undefined) {
                            totals[i] += Number(sc);
                            counts[i] += 1;
                        }
                    });
                });

                return totals.map((tot, i) => counts[i] > 0 ? Math.round((tot / counts[i]) * 100) / 100 : 0);
            }

            function buildGroupedDatasets(companyType = 'all', elementFilter = 'all') {
                const datasets = [];
                let idx = 0;

                Object.values(trendElements).forEach(el => {
                    const matchesFilter = elementFilter === 'all' 
                        || String(el.id) === String(elementFilter) 
                        || String(el.kode) === String(elementFilter);

                    if (!matchesFilter) {
                        idx++;
                        return;
                    }

                    let scoreArr = el.scores || [];
                    if (companyType === 'msm') {
                        scoreArr = el.msmScores || [];
                    } else if (companyType === 'ttn') {
                        scoreArr = el.ttnScores || [];
                    }

                    const color = elementPalette[idx % elementPalette.length];

                    // 1. Bar Dataset
                    datasets.push({
                        type: 'bar',
                        label: 'Elemen ' + el.kode + ': ' + el.nama,
                        shortLabel: 'Elemen ' + el.kode,
                        data: scoreArr,
                        backgroundColor: color,
                        borderColor: color,
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: elementFilter === 'all' ? 0.85 : 0.45,
                        categoryPercentage: 0.75,
                        order: 2
                    });

                    // 2. Element-Specific Line Curve (when filtered to a specific element)
                    if (elementFilter !== 'all') {
                        datasets.push({
                            type: 'line',
                            label: 'Tren Naik-Turun Elemen ' + el.kode,
                            data: scoreArr,
                            borderColor: color,
                            backgroundColor: color,
                            borderWidth: 3.5,
                            fill: false,
                            tension: 0.35,
                            pointRadius: 7,
                            pointHoverRadius: 10,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: color,
                            pointBorderWidth: 3,
                            order: 1
                        });
                    }

                    idx++;
                });

                return datasets;
            }

            const groupedMultiYearBarTextPlugin = {
                id: 'groupedMultiYearBarTextPlugin',
                afterDatasetsDraw(chart) {
                    const { ctx } = chart;
                    chart.data.datasets.forEach((dataset, datasetIndex) => {
                        // Skip line curve datasets so numbers are only drawn once per bar
                        if (dataset.type === 'line') return;

                        const meta = chart.getDatasetMeta(datasetIndex);
                        if (!meta || meta.hidden) return;

                        meta.data.forEach((bar, index) => {
                            const value = dataset.data[index];
                            if (value !== undefined && value !== null && value > 0) {
                                ctx.save();
                                const isWide = bar.width >= 24;
                                
                                if (isWide) {
                                    ctx.font = 'bold 10px "Plus Jakarta Sans", sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    ctx.fillStyle = '#0f172a';
                                    ctx.fillText(value + '%', bar.x, bar.y - 2);
                                } else {
                                    ctx.translate(bar.x, bar.y - 4);
                                    ctx.rotate(-Math.PI / 2);
                                    ctx.textAlign = 'left';
                                    ctx.textBaseline = 'middle';
                                    ctx.font = 'bold 10px "Plus Jakarta Sans", sans-serif';
                                    ctx.fillStyle = '#0f172a';
                                    ctx.fillText(value + '%', 0, 0);
                                }
                                ctx.restore();
                            }
                        });
                    });
                }
            };

            const multiYearChartInstance = new Chart(ctxMultiYear.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: yearLabels,
                    datasets: buildGroupedDatasets('all', 'all')
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 13, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                                color: '#1e293b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 110,
                            ticks: {
                                stepSize: 10,
                                callback: function(val) { return val <= 100 ? val + '%' : ''; },
                                font: { size: 11, weight: '600' }
                            },
                            title: {
                                display: true,
                                text: 'Nilai Audit (%)',
                                font: { size: 12, weight: 'bold' },
                                color: '#64748b'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'start',
                            labels: {
                                boxWidth: 12,
                                boxHeight: 12,
                                padding: 14,
                                usePointStyle: true,
                                font: { size: 11, weight: '600', family: "'Plus Jakarta Sans', sans-serif" },
                                filter: function(legendItem) {
                                    return !legendItem.text.startsWith('Tren Naik-Turun');
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: function(items) {
                                    return items[0].label;
                                },
                                label: function(context) {
                                    const dataset = context.dataset;
                                    return ` ${dataset.label}: ${context.raw}%`;
                                }
                            }
                        }
                    }
                },
                plugins: [groupedMultiYearBarTextPlugin]
            });

            const elementFilterEl = document.getElementById('multiYearElementFilter');
            const companyFilterEl = document.getElementById('multiYearCompanyFilter');

            function updateMultiYearChart() {
                const compVal = companyFilterEl ? companyFilterEl.value : 'all';
                const elemVal = elementFilterEl ? elementFilterEl.value : 'all';
                multiYearChartInstance.data.datasets = buildGroupedDatasets(compVal, elemVal);
                multiYearChartInstance.update();
            }

            if (elementFilterEl) {
                elementFilterEl.addEventListener('change', updateMultiYearChart);
            }
            if (companyFilterEl) {
                companyFilterEl.addEventListener('change', updateMultiYearChart);
            }
        }
    });
</script>
<?php
$scripts = ob_get_clean();

echo view('layouts.app', get_defined_vars())->render();
