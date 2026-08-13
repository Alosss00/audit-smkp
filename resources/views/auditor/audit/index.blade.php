@extends('layouts.app')

@section('title', 'Daftar Sesi Audit — SMKP Minerba')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-journal-text text-info me-2"></i>Sesi Audit Area Kerja
        </h2>
        <p class="text-muted mb-0">Daftar dan rekapitulasi sesi penilaian matriks SMKP pada area kerja: <strong>{{ $userArea ?? 'Semua Area' }}</strong></p>
    </div>
</div>

<!-- Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('auditor.audit-sesi.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <select name="status" class="form-select rounded-3" onchange="this.form.submit()">
                <option value="">-- Semua Status Sesi --</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="berjalan" {{ request('status') == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        @if(request()->filled('status'))
            <div class="col-md-2">
                <a href="{{ route('auditor.audit-sesi.index') }}" class="btn btn-outline-secondary rounded-3 w-100">Reset</a>
            </div>
        @endif
    </form>
</div>

<!-- Sessions Table -->
<div class="card card-custom p-4">
    @if($auditSesis->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox display-4 d-block mb-3 opacity-50"></i>
            <p class="mb-2 fs-5 fw-semibold">Belum ada sesi audit pada area kerja Anda.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Periode Audit</th>
                        <th>Area Audit</th>
                        <th>Status</th>
                        <th>Skor Akhir</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($auditSesis as $index => $sesi)
                        <tr>
                            <td>{{ $auditSesis->firstItem() + $index }}</td>
                            <td>
                                <i class="bi bi-calendar-event me-1 text-muted"></i>
                                {{ $sesi->tanggal_mulai->format('d M Y') }} - {{ $sesi->tanggal_selesai->format('d M Y') }}
                            </td>
                            <td>
                                <span class="fw-bold text-slate-800">{{ $sesi->area_audit }}</span>
                            </td>
                            <td>
                                @if($sesi->status === 'draft')
                                    <span class="badge bg-secondary badge-role">Draft</span>
                                @elseif($sesi->status === 'berjalan')
                                    <span class="badge bg-warning text-dark badge-role">Berjalan</span>
                                @else
                                    <span class="badge bg-success badge-role">Selesai</span>
                                @endif
                            </td>
                            <td>
                                @if($sesi->skor_akhir !== null)
                                    <span class="fs-6 fw-bold text-primary">{{ number_format($sesi->skor_akhir, 2) }}%</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('auditor.audit-sesi.rekap', $sesi->id) }}" class="btn btn-sm btn-outline-info text-dark rounded-2">
                                    <i class="bi bi-bar-chart-line me-1"></i> Rekap
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $auditSesis->links() }}
        </div>
    @endif
</div>
@endsection
