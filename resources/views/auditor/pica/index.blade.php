@extends('layouts.app')

@section('title', 'Tindak Lanjut PICA — SMKP Minerba')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-tools text-primary me-2"></i> Tindak Lanjut PICA per Area Audit
        </h3>
        <p class="text-muted small mb-0">Daftar sesi audit yang memiliki temuan PICA dan verifikasi tindakan perbaikan per area audit</p>
    </div>
</div>

<!-- Stat Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block">Total Temuan PICA</span>
                    <h3 class="fw-bold text-slate-800 mb-0 mt-1">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-card-checklist"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block">Status Open</span>
                    <h3 class="fw-bold text-danger mb-0 mt-1">{{ number_format($stats['open']) }}</h3>
                </div>
                <div class="stat-icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-octagon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block">In Progress</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($stats['in_progress']) }}</h3>
                </div>
                <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block">Closed / Verifikasi</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($stats['closed']) }}</h3>
                </div>
                <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('auditor.pica.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control bg-light border-start-0" 
                    placeholder="Cari area audit, temuan, atau PIC..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select bg-light">
                <option value="">-- Semua Status PICA --</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Ada Temuan Open</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Ada Temuan In Progress</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Seluruh Temuan Closed</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                <i class="bi bi-filter me-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('auditor.pica.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card (Grouped by Area Audit) -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Area & Tanggal Audit</th>
                    <th>Total Temuan</th>
                    <th>Breakdown Status</th>
                    <th>Progress Penyelesaian</th>
                    <th class="pe-4 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditSesis as $index => $sesi)
                    @php
                        $picas = $sesi->auditDetails->map(fn($d) => $d->pica)->filter();
                        $totalPica = $picas->count();
                        $openCount = $picas->where('status', 'open')->count();
                        $progressCount = $picas->where('status', 'in_progress')->count();
                        $closedCount = $picas->where('status', 'closed')->count();
                        $overdueCount = $picas->where('status', '!=', 'closed')->filter(fn($p) => $p->tenggat_waktu && $p->tenggat_waktu->isPast())->count();
                        $pctClosed = $totalPica > 0 ? round(($closedCount / $totalPica) * 100) : 0;
                    @endphp
                    <tr>
                        <td class="ps-4 fw-semibold text-muted">{{ $auditSesis->firstItem() + $index }}</td>
                        <td>
                            <strong class="d-block text-slate-800 fs-6">{{ $sesi->area_audit }}</strong>
                            <small class="text-muted"><i class="bi bi-calendar me-1"></i> {{ $sesi->tanggal_mulai->format('d M Y') }} - {{ $sesi->tanggal_selesai->format('d M Y') }}</small>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                {{ $totalPica }} Temuan
                            </span>
                            @if($overdueCount > 0)
                                <span class="badge bg-danger rounded-pill px-2 py-1 ms-1" style="font-size: 0.65rem;" title="{{ $overdueCount }} temuan overdue">
                                    <i class="bi bi-exclamation-triangle-fill"></i> {{ $overdueCount }} Overdue
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <span class="badge bg-danger rounded-pill px-2 py-1" title="Open">{{ $openCount }} Open</span>
                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1" title="In Progress">{{ $progressCount }} In Progress</span>
                                <span class="badge bg-success rounded-pill px-2 py-1" title="Closed">{{ $closedCount }} Closed</span>
                            </div>
                        </td>
                        <td style="min-width: 180px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pctClosed }}%"></div>
                                </div>
                                <span class="small fw-bold text-slate-700">{{ $pctClosed }}%</span>
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold" 
                                data-bs-toggle="modal" data-bs-target="#areaPicaModal{{ $sesi->id }}">
                                <i class="bi bi-list-check me-1"></i> Detail & Respon ({{ $totalPica }})
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard-check display-4 d-block mb-3 text-secondary opacity-50"></i>
                            <h6 class="fw-bold">Belum Ada Sesi Audit dengan PICA</h6>
                            <p class="small mb-0">Temuan audit yang membutuhkan tindakan perbaikan akan otomatis dikelompokkan berdasarkan Area Audit di sini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($auditSesis->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $auditSesis->links() }}
        </div>
    @endif
</div>

<!-- Modal Detail & Tindak Lanjut PICA per Area Audit -->
@foreach($auditSesis as $sesi)
    @php
        $detailsWithPica = $sesi->auditDetails->filter(fn($d) => $d->pica != null);
    @endphp
    <div class="modal fade" id="areaPicaModal{{ $sesi->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            <i class="bi bi-tools text-info me-2"></i> Daftar Temuan PICA — Area: {{ $sesi->area_audit }}
                        </h5>
                        <small class="text-slate-300" style="font-size: 0.8rem; color: #cbd5e1;">
                            <i class="bi bi-calendar me-1"></i> {{ $sesi->tanggal_mulai->format('d M Y') }} - {{ $sesi->tanggal_selesai->format('d M Y') }} | Total {{ $detailsWithPica->count() }} Temuan PICA
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    
                    <div class="accordion" id="accordionPicaSesi{{ $sesi->id }}">
                        @foreach($detailsWithPica as $detailIndex => $detail)
                            @php
                                $pica = $detail->pica;
                                $kriteria = $detail->kriteria;
                                $isOverdue = $pica->status !== 'closed' && $pica->tenggat_waktu && $pica->tenggat_waktu->isPast();
                            @endphp
                            <div class="accordion-item border rounded-3 mb-3 shadow-sm overflow-hidden">
                                <h2 class="accordion-header" id="headingPica{{ $pica->id }}">
                                    <button class="accordion-button {{ $detailIndex > 0 ? 'collapsed' : '' }} bg-white py-3" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#collapsePica{{ $pica->id }}" 
                                        aria-expanded="{{ $detailIndex == 0 ? 'true' : 'false' }}">
                                        <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-secondary font-monospace">{{ $kriteria ? $kriteria->kode_kriteria : '-' }}</span>
                                                <strong class="text-slate-800 text-truncate" style="max-width: 450px;" title="{{ $pica->deskripsi_temuan }}">
                                                    {{ $pica->deskripsi_temuan }}
                                                </strong>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($isOverdue)
                                                    <span class="badge bg-danger">Overdue</span>
                                                @endif
                                                @if($pica->status === 'open')
                                                    <span class="badge bg-danger rounded-pill px-3 py-1">Open</span>
                                                @elseif($pica->status === 'in_progress')
                                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1">In Progress</span>
                                                @else
                                                    <span class="badge bg-success rounded-pill px-3 py-1">Closed</span>
                                                @endif
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapsePica{{ $pica->id }}" class="accordion-collapse collapse {{ $detailIndex == 0 ? 'show' : '' }}" 
                                    data-bs-parent="#accordionPicaSesi{{ $sesi->id }}">
                                    <div class="accordion-body bg-white p-4 border-top">
                                        
                                        <!-- Form Input Per Temuan PICA -->
                                        <form action="{{ route('auditor.pica.update', $pica->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <!-- Detail Info Kriteria & Deskripsi -->
                                            <div class="p-3 bg-light rounded-3 border mb-4">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <small class="text-muted d-block">Kode Kriteria Audit:</small>
                                                        <strong class="text-slate-800">{{ $kriteria ? $kriteria->kode_kriteria : '-' }}</strong>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <small class="text-muted d-block">Persyaratan Dokumen / Deskripsi Kriteria:</small>
                                                        <div class="small text-slate-700 fw-semibold">{{ $kriteria ? $kriteria->deskripsi : '-' }}</div>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <small class="text-muted d-block">Deskripsi Temuan Audit:</small>
                                                        <div class="fw-bold text-danger p-2 bg-white rounded border border-danger border-opacity-25">
                                                            {{ $pica->deskripsi_temuan }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-3">
                                                 <div class="col-md-6">
                                                     <small class="text-muted d-block fw-semibold">Akar Masalah (Root Cause):</small>
                                                     <div class="p-2 bg-light rounded border text-slate-800 small">{{ $pica->akar_masalah ?? 'Belum diisi oleh responden' }}</div>
                                                 </div>
                                                 <div class="col-md-6">
                                                     <small class="text-muted d-block fw-semibold">Bukti Perbaikan:</small>
                                                     @if($pica->bukti_perbaikan)
                                                         <a href="{{ $pica->bukti_perbaikan_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                                             <i class="bi bi-paperclip me-1"></i> Lihat Bukti Terunggah
                                                         </a>
                                                     @else
                                                         <div class="p-2 bg-light rounded border text-muted small">Belum ada file bukti perbaikan</div>
                                                     @endif
                                                 </div>
                                                 <div class="col-md-4">
                                                     <small class="text-muted d-block fw-semibold">Tenggat Waktu (Lead Auditor):</small>
                                                     <strong class="text-slate-800">{{ $pica->tenggat_waktu ? $pica->tenggat_waktu->format('d M Y') : 'Belum ditentukan' }}</strong>
                                                 </div>
                                                 <div class="col-md-4">
                                                     <small class="text-muted d-block fw-semibold">Status Saat Ini:</small>
                                                     <span class="badge bg-{{ $pica->status === 'closed' ? 'success' : ($pica->status === 'in_progress' ? 'warning text-dark' : 'danger') }} text-uppercase">{{ $pica->status }}</span>
                                                 </div>
                                                 <div class="col-md-4">
                                                     <small class="text-muted d-block fw-semibold">Catatan Verifikasi Auditor:</small>
                                                     <span class="small text-slate-700">{{ $pica->catatan_verifikasi_auditor ?? '-' }}</span>
                                                 </div>
                                             </div>

                                             <div class="mt-3 text-end">
                                                 <a href="{{ route('auditor.pica.edit', $pica->id) }}" class="btn btn-primary px-4 rounded-3 fw-semibold">
                                                     <i class="bi bi-pencil-square me-1"></i> Isi Respon & Upload Bukti PICA
                                                 </a>
                                             </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
