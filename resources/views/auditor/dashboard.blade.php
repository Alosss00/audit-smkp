@extends('layouts.app')

@section('title', 'Dashboard Auditor — SMKP Minerba')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-clipboard-check-fill text-info me-2"></i>Dashboard Auditor
        </h2>
        <p class="text-muted mb-0">Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Pengelolaan dan pengisian matriks audit SMKP Minerba.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="{{ route('auditor.audit-sesi.create') }}" class="btn btn-primary rounded-3 shadow-sm px-3 py-2 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> Buat Sesi Audit Baru
        </a>
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
                    <div class="text-muted small fw-semibold">Total Sesi Audit</div>
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['total_sesi'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-secondary">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-secondary bg-opacity-10 text-secondary">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Status Draft</div>
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['draft'] }}</h3>
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
                    <div class="text-muted small fw-semibold">Berjalan</div>
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['berjalan'] }}</h3>
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
                    <div class="text-muted small fw-semibold">Selesai</div>
                    <h3 class="fw-bold text-slate-800 mb-0">{{ $stats['selesai'] }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visual Charts Section -->
<div class="row g-4 mb-4">
    <!-- Bar Chart Compliance Percentage for Latest Audit -->
    @if($latestAudit)
        <div class="col-lg-6">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold text-slate-800 mb-0"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Pencapaian Audit Terbaru</h5>
                        <small class="text-muted">Area: <strong>{{ $latestAudit->area_audit }}</strong></small>
                    </div>
                    <span class="fs-5 fw-bold text-primary">{{ number_format($latestAudit->skor_akhir ?? 0, 2) }}%</span>
                </div>
                <div style="height: 220px;">
                    <canvas id="auditorBarChart"></canvas>
                </div>
            </div>
        </div>
    @endif

    <!-- Horizontal Bar Chart Audit Findings Frequency -->
    <div class="col-lg-{{ $latestAudit ? '6' : '12' }}">
        <div class="card card-custom p-4 h-100 border-start border-4 border-danger">
            <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Frekuensi Temuan Audit per Elemen</h5>
            <p class="text-muted small mb-3">Akumulasi jumlah temuan ketidaksesuaian pada seluruh audit Anda.</p>
            <div style="height: 220px;">
                <canvas id="auditorFindingsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Audit Sessions Table -->
<div class="card card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Sesi Audit Terbaru</h5>
        <a href="{{ route('auditor.audit-sesi.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">Lihat Semua</a>
    </div>

    @if($recentAudits->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox display-4 d-block mb-3 opacity-50"></i>
            <p class="mb-0">Belum ada sesi audit yang dibuat.</p>
            <small>Klik tombol "Buat Sesi Audit Baru" untuk memulai penilaian matriks SMKP.</small>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Area Audit</th>
                        <th>Status</th>
                        <th>Skor Akhir</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAudits as $audit)
                        <tr>
                            <td>{{ $audit->tanggal_audit->format('d M Y') }}</td>
                            <td class="fw-semibold">{{ $audit->area_audit }}</td>
                            <td>
                                @if($audit->status === 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($audit->status === 'berjalan')
                                    <span class="badge bg-warning text-dark">Berjalan</span>
                                @else
                                    <span class="badge bg-success">Selesai</span>
                                @endif
                            </td>
                            <td>
                                @if($audit->skor_akhir !== null)
                                    <strong class="text-primary">{{ number_format($audit->skor_akhir, 2) }}%</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group gap-1">
                                    @if($audit->status !== 'selesai')
                                        <a href="{{ route('auditor.audit-sesi.matrix', $audit->id) }}" class="btn btn-sm btn-primary rounded-2">
                                            <i class="bi bi-pencil-square me-1"></i> Isi Matriks
                                        </a>
                                    @endif
                                    <a href="{{ route('auditor.audit-sesi.rekap', $audit->id) }}" class="btn btn-sm btn-outline-info text-dark rounded-2">
                                        <i class="bi bi-bar-chart-line me-1"></i> Rekap
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($latestAudit)
            // Bar Chart Compliance
            const ctxBar = document.getElementById('auditorBarChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Persentase Pencapaian (%)',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: 'rgba(2, 132, 199, 0.75)',
                        borderColor: '#0284c7',
                        borderWidth: 2,
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
                    }
                }
            });
        @endif

        // Horizontal Bar Chart Findings
        const ctxFindings = document.getElementById('auditorFindingsChart').getContext('2d');
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
