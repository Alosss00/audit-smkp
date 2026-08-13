@extends('layouts.app')

@section('title', 'Rekapitulasi Audit System — Administrator')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-shield-check text-primary me-2"></i>Monitoring Rekap Audit System
        </h2>
        <p class="text-muted mb-0">Pantau dan evaluasi seluruh pelaksanaan sesi audit internal dari seluruh auditor.</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.rekap-audit.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Cari area audit atau nama auditor..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Status Sesi --</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="berjalan" {{ request('status') == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100 rounded-3">Cari</button>
        </div>
        @if(request()->filled('search') || request()->filled('status'))
            <div class="col-md-2">
                <a href="{{ route('admin.rekap-audit.index') }}" class="btn btn-outline-secondary w-100 rounded-3">Reset</a>
            </div>
        @endif
    </form>
</div>

<!-- Audit Sessions Table -->
<div class="card card-custom p-4">
    @if($auditSesis->isEmpty())
        <div class="text-center py-4 text-muted">Belum ada sesi audit yang ditemukan.</div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Tanggal Audit</th>
                        <th>Area / Lokasi Audit</th>
                        <th>Auditor Pelaksana</th>
                        <th>Status</th>
                        <th>Skor Akhir</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($auditSesis as $index => $sesi)
                        <tr>
                            <td>{{ $auditSesis->firstItem() + $index }}</td>
                            <td>{{ $sesi->tanggal_mulai->format('d M Y') }} - {{ $sesi->tanggal_selesai->format('d M Y') }}</td>
                            <td class="fw-bold text-slate-800">{{ $sesi->area_audit }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-circle text-secondary"></i>
                                    {{ $sesi->user->name ?? '-' }}
                                </div>
                            </td>
                            <td>
                                @if($sesi->status === 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($sesi->status === 'berjalan')
                                    <span class="badge bg-warning text-dark">Berjalan</span>
                                @else
                                    <span class="badge bg-success">Selesai</span>
                                @endif
                            </td>
                            <td>
                                @if($sesi->skor_akhir !== null)
                                    <span class="fw-bold text-primary fs-6">{{ number_format($sesi->skor_akhir, 2) }}%</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group gap-1">
                                    <a href="{{ route('admin.rekap-audit.show', $sesi->id) }}" class="btn btn-sm btn-outline-primary rounded-2">
                                        <i class="bi bi-eye me-1"></i> Detail Rekap
                                    </a>
                                    <a href="{{ route('admin.rekap-audit.cetak', $sesi->id) }}" target="_blank" class="btn btn-sm btn-dark rounded-2">
                                        <i class="bi bi-printer me-1"></i> Cetak
                                    </a>
                                </div>
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
