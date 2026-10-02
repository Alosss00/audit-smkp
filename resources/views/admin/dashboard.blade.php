@extends('layouts.app')

@section('title', 'Dashboard Auditor Internal SMKP — SMKP Minerba')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-speedometer2 text-danger me-2"></i>Dashboard Auditor Internal SMKP
        </h2>
        <p class="text-muted mb-0">Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Pengawasan audit internal, pembuatan sesi, penilaian matriks, dan otoritas PICA SMKP Minerba.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
        <a href="{{ route('admin.audit-sesi.create') }}" class="btn btn-danger rounded-3 shadow-sm px-3 py-2 fw-semibold">
            <i class="bi bi-plus-circle me-1"></i> Buat Sesi Audit
        </a>
        <a href="{{ route('admin.pica.index') }}" class="btn btn-outline-danger rounded-3 px-3 py-2 fw-semibold">
            <i class="bi bi-tools me-1"></i> Monitoring PICA
        </a>
    </div>
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
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['total_audits'] }}</h3>
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
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['open_pica'] }}</h3>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ number_format($stats['open_pica_kriteria']) }} Kriteria Open</small>
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
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['in_progress_pica'] }}</h3>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ number_format($stats['in_progress_pica_kriteria']) }} Kriteria In Progress</small>
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
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['closed_pica'] }}</h3>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ number_format($stats['closed_pica_kriteria']) }} Kriteria Selesai</small>
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
            <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Pencapaian Nilai Audit per Elemen</h5>
            <p class="text-muted small mb-3">Grafik rata-rata persentase pencapaian nilai per elemen SMKP dari seluruh sesi audit yang dijalankan.</p>
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

<!-- Visual Chart Analytics Section 1.5: Radar Charts -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100 border-start border-4 border-success">
            <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-heptagon-fill me-2 text-success"></i>PT Meares Soputan Mining (MSM)</h5>
            <p class="text-muted small mb-3">Grafik visualisasi persentase rata-rata pencapaian tiap elemen SMKP.</p>
            <div style="height: 600px; display: flex; justify-content: center;">
                <canvas id="msmRadarChart" style="max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100 border-start border-4 border-info">
            <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-heptagon-fill me-2 text-info"></i>PT Tambang Tondano Nusajaya (TTN)</h5>
            <p class="text-muted small mb-3">Grafik visualisasi persentase rata-rata pencapaian tiap elemen SMKP.</p>
            <div style="height: 600px; display: flex; justify-content: center;">
                <canvas id="ttnRadarChart" style="max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Visual Chart Analytics Section 2: Audit Findings Frequency per Elemen & Top 5 Findings List -->
<div class="row g-4 mb-5">
    <!-- Horizontal Bar Chart Findings -->
    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-exclamation-octagon-fill me-2 text-danger"></i>Frekuensi Temuan Audit per Elemen</h5>
                    <p class="text-muted small mb-0">Grafik persentase akumulasi temuan ketidaksesuaian/catatan audit per elemen SMKP.</p>
                </div>
                @if(isset($totalAllFindings) && $totalAllFindings > 0)
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                        Total: {{ number_format($totalAllFindings) }} Temuan
                    </span>
                @endif
            </div>
            <div style="height: 260px;">
                <canvas id="findingsBarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 7 Most Frequent Findings List -->
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top 7 Elemen Paling Sering Ditemui Temuan</h5>
            <p class="text-muted small mb-3">Peringkat elemen SMKP yang memerlukan perhatian dan perbaikan khusus.</p>

            <div class="list-group list-group-flush border-0">
                @forelse($topFindings as $index => $top)
                    <div class="list-group-item d-flex align-items-center justify-content-between px-0 py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ $index == 0 ? 'bg-danger' : ($index == 1 ? 'bg-warning text-dark' : 'bg-secondary') }} rounded-circle p-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <strong class="text-slate-800 d-block small">Elemen {{ $top['kode_elemen'] }}: {{ $top['nama_elemen'] }}</strong>
                            </div>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 font-monospace">
                            {{ $top['percentage'] }}% <small class="text-muted">({{ $top['total_findings'] }}/{{ $top['total_assessed'] ?? 0 }})</small>
                        </span>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Belum ada data temuan audit.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->hasMasterDataAccess())
<!-- Master Data Quick Navigation -->
<h5 class="fw-bold mb-3"><i class="bi bi-grid-fill me-2 text-primary"></i>Kelola Master Data & User</h5>
<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Master Elemen</h5>
                <span class="badge bg-primary rounded-pill">{{ $stats['total_elemens'] }}</span>
            </div>
            <p class="text-muted small">Kelola data elemen SMKP, kode elemen, nama elemen, dan bobot persen.</p>
            <a href="{{ route('admin.elemens.index') }}" class="btn btn-outline-primary btn-sm rounded-3 mt-auto">
                <i class="bi bi-gear me-1"></i> Kelola Elemen
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Sub-Elemen</h5>
                <span class="badge bg-info text-dark rounded-pill">{{ $stats['total_sub_elemens'] }}</span>
            </div>
            <p class="text-muted small">Kelola turunan sub-elemen berdasarkan masing-masing elemen regulasi.</p>
            <a href="{{ route('admin.sub-elemens.index') }}" class="btn btn-outline-info text-dark btn-sm rounded-3 mt-auto">
                <i class="bi bi-gear me-1"></i> Kelola Sub-Elemen
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Master Kriteria</h5>
                <span class="badge bg-success rounded-pill">{{ $stats['total_kriterias'] }}</span>
            </div>
            <p class="text-muted small">Kelola kriteria pertanyaan audit beserta deskripsi dan nilai maksimal.</p>
            <a href="{{ route('admin.kriterias.index') }}" class="btn btn-outline-success btn-sm rounded-3 mt-auto">
                <i class="bi bi-gear me-1"></i> Kelola Kriteria
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Kelola Users</h5>
                <span class="badge bg-warning text-dark rounded-pill">{{ $stats['total_users'] }}</span>
            </div>
            <p class="text-muted small">Kelola data pengguna, role Administrator, Auditor SMKP, dan Auditor Perusahaan.</p>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-warning text-dark btn-sm rounded-3 mt-auto">
                <i class="bi bi-gear me-1"></i> Kelola User
            </a>
        </div>
    </div>
</div>
@else
<!-- Auditor SMKP Quick Action Links -->
<h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge-fill me-2 text-primary"></i>Akses Cepat Penilaian & Monitoring</h5>
<div class="row g-4">
    <div class="col-md-6 col-lg-4">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Kelola Sesi Audit</h5>
                <span class="badge bg-primary rounded-pill">{{ $stats['total_audits'] }} Sesi</span>
            </div>
            <p class="text-muted small">Kelola seluruh sesi evaluasi, input matriks penilaian kriteria, dan rekapitulasi nilai.</p>
            <a href="{{ route('admin.audit-sesi.index') }}" class="btn btn-outline-primary btn-sm rounded-3 mt-auto">
                <i class="bi bi-journal-check me-1"></i> Buka Sesi Audit
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Monitoring Audit</h5>
                <span class="badge bg-success rounded-pill">{{ $stats['audits_selesai'] }} Selesai</span>
            </div>
            <p class="text-muted small">Pantau status pelaksanaan audit internal lintas perusahaan dan unduh laporan.</p>
            <a href="{{ route('admin.rekap-audit.index') }}" class="btn btn-outline-success btn-sm rounded-3 mt-auto">
                <i class="bi bi-shield-check me-1"></i> Buka Monitoring
            </a>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">Tindak Lanjut Temuan (PICA)</h5>
                <span class="badge bg-danger rounded-pill">{{ $stats['open_pica'] + $stats['in_progress_pica'] }} Aktif</span>
            </div>
            <p class="text-muted small">Verifikasi rencana koreksi/pencegahan ketidaksesuaian dan kelola penutupan temuan PICA.</p>
            <a href="{{ route('admin.pica.index') }}" class="btn btn-outline-danger btn-sm rounded-3 mt-auto">
                <i class="bi bi-tools me-1"></i> Buka Tindak Lanjut PICA
            </a>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Bar Chart Average Compliance per Elemen
        const ctxBar = document.getElementById('elementBarChart').getContext('2d');
        const elementFullNames = {!! json_encode($elementFullNames) !!};
        const barLabels = elementFullNames.map(name => {
            const parts = name.split(': ');
            return [parts[0], parts[1] || ''];
        });

        const barTextPlugin = {
            id: 'barTextPlugin',
            afterDatasetsDraw(chart, args, pluginOptions) {
                const { ctx, data } = chart;
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

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{
                    label: 'Rata-Rata Pencapaian (%)',
                    data: {!! json_encode($elementScores) !!},
                    backgroundColor: {!! json_encode($elementColors) !!},
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
                            callback: function(value) { return value + '%'; }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
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

        // 1.5 Radar Charts
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

        const rawElementNames = {!! json_encode($elementFullNames) !!};
        const msmScores = {!! json_encode($msmScores) !!};
        const ttnScores = {!! json_encode($ttnScores) !!};

        const msmLabels = rawElementNames.map((name, i) => {
            let lines = wrapText(name, 22);
            lines.push('(' + msmScores[i] + '%)');
            return lines;
        });

        const ttnLabels = rawElementNames.map((name, i) => {
            let lines = wrapText(name, 22);
            lines.push('(' + ttnScores[i] + '%)');
            return lines;
        });
        const radarOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    pointLabels: {
                        font: { size: 13, weight: 'bold' }
                    },
                    ticks: {
                        stepSize: 10,
                        font: { size: 12 },
                        callback: function(value) { return value + '%'; }
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) { return 'Pencapaian: ' + context.raw + '%'; }
                    }
                }
            }
        };

        const ctxMsmRadar = document.getElementById('msmRadarChart').getContext('2d');
        new Chart(ctxMsmRadar, {
            type: 'radar',
            data: {
                labels: msmLabels,
                datasets: [{
                    label: 'Pencapaian (%)',
                    data: msmScores,
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 2,
                }]
            },
            options: radarOptions
        });

        const ctxTtnRadar = document.getElementById('ttnRadarChart').getContext('2d');
        new Chart(ctxTtnRadar, {
            type: 'radar',
            data: {
                labels: ttnLabels,
                datasets: [{
                    label: 'Pencapaian (%)',
                    data: ttnScores,
                    backgroundColor: 'rgba(14, 165, 233, 0.2)',
                    borderColor: 'rgba(14, 165, 233, 1)',
                    pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 2,
                }]
            },
            options: radarOptions
        });

        // 2. Doughnut Chart Status
        const ctxDoughnut = document.getElementById('statusDoughnutChart').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Berjalan', 'Selesai', 'Draft'],
                datasets: [{
                    data: [
                        {{ $stats['audits_berjalan'] }},
                        {{ $stats['audits_selesai'] }},
                        {{ $stats['total_audits'] - $stats['audits_berjalan'] - $stats['audits_selesai'] }}
                    ],
                    backgroundColor: ['#f59e0b', '#10b981', '#64748b'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 3. Horizontal Bar Chart Audit Findings Frequency (Skala 100% per Elemen)
        const ctxFindings = document.getElementById('findingsBarChart').getContext('2d');
        const rawFindingCounts = {!! json_encode($findingCounts) !!};
        const rawFindingTotals = {!! json_encode($findingTotalsPerElemen ?? []) !!};

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
                        
                        if (bar.width < 30) {
                            xPos = bar.x + 10;
                            ctx.fillStyle = '#1e293b'; // slate-800
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

        new Chart(ctxFindings, {
            type: 'bar',
            data: {
                labels: {!! json_encode($findingLabels) !!},
                datasets: [{
                    label: 'Persentase Temuan per Elemen (%)',
                    data: {!! json_encode($findingPercentages) !!},
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
            },
            plugins: [hBarTextPlugin]
        });
    });
</script>
@endpush
