@extends('layouts.app')

@section('title', 'Dashboard Administrator — SMKP Minerba')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-speedometer2 text-danger me-2"></i>Dashboard Administrator
        </h2>
        <p class="text-muted mb-0">Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Monitoring & Evaluasi Penerapan Regulasi SMKP Kepdirjen 185.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">
            <i class="bi bi-shield-lock-fill me-1"></i> Mode Administrator
        </span>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total Elemen</div>
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['total_elemens'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-info bg-opacity-10 text-info">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Sub-Elemen</div>
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['total_sub_elemens'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-list-check"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Kriteria Penilaian</div>
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['total_kriterias'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total Sesi Audit</div>
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['total_audits'] }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visual Chart Analytics Section 1: Average Compliance & Session Status -->
<div class="row g-4 mb-4">
    <!-- Bar Chart Percentage -->
    <div class="col-lg-8">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Rata-Rata Pencapaian (%) per Elemen SMKP</h5>
            <p class="text-muted small mb-3">Grafik evaluasi tingkat pemenuhan elemen pada seluruh sesi audit yang berjalan/selesai.</p>
            <div style="height: 260px;">
                <canvas id="elementBarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart Status -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-pie-chart-fill me-2 text-info"></i>Status Sesi Audit</h5>
            <p class="text-muted small mb-3">Distribusi status sesi audit internal yang terdaftar.</p>
            <div style="height: 230px; position: relative;">
                <canvas id="statusDoughnutChart"></canvas>
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
                    <p class="text-muted small mb-0">Grafik jumlah akumulasi temuan ketidaksesuaian/catatan audit per elemen SMKP.</p>
                </div>
            </div>
            <div style="height: 260px;">
                <canvas id="findingsBarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 5 Most Frequent Findings List -->
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top 5 Elemen Paling Sering Ditemui Temuan</h5>
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
                            {{ $top['total_findings'] }} Temuan
                        </span>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Belum ada data temuan audit.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Master Data Quick Navigation -->
<h5 class="fw-bold mb-3"><i class="bi bi-grid-fill me-2 text-primary"></i>Kelola Master Data & Monitoring</h5>
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
            <p class="text-muted small">Kelola data pengguna, role hak akses Admin dan Auditor pelaksana.</p>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-warning text-dark btn-sm rounded-3 mt-auto">
                <i class="bi bi-gear me-1"></i> Kelola User
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Bar Chart Percentage
        const ctxBar = document.getElementById('elementBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Rata-Rata Pencapaian (%)',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(2, 132, 199, 0.75)',
                    borderColor: '#0284c7',
                    borderWidth: 2,
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
                }
            }
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

        // 3. Horizontal Bar Chart Audit Findings Frequency
        const ctxFindings = document.getElementById('findingsBarChart').getContext('2d');
        new Chart(ctxFindings, {
            type: 'bar',
            data: {
                labels: {!! json_encode($findingLabels) !!},
                datasets: [{
                    label: 'Jumlah Temuan Ketidaksesuaian',
                    data: {!! json_encode($findingCounts) !!},
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
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endpush
