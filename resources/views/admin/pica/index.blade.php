@extends('layouts.app')

@section('title', 'Monitoring PICA — SMKP Minerba Administrator')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-tools text-danger me-2"></i> Monitoring PICA per Area Audit
        </h3>
        <p class="text-muted small mb-0">Pemantauan dan pengawasan terpusat seluruh temuan PICA yang dikelompokkan berdasarkan Area Audit</p>
    </div>
    @if($stats['overdue'] > 0)
        <div>
            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $stats['overdue'] }} Temuan Melewati Tenggat (Overdue)
            </span>
        </div>
    @endif
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
    <form action="{{ route('admin.pica.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control bg-light border-start-0" 
                    placeholder="Cari area audit, temuan, PIC, atau auditor..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="user_id" class="form-select bg-light">
                <option value="">-- Semua Auditor --</option>
                @foreach($auditors as $auditor)
                    <option value="{{ $auditor->id }}" {{ request('user_id') == $auditor->id ? 'selected' : '' }}>
                        {{ $auditor->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select bg-light">
                <option value="">-- Semua Status PICA --</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Ada Temuan Open</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Ada Temuan In Progress</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Seluruh Temuan Closed</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-danger w-100 fw-semibold">
                <i class="bi bi-filter me-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'user_id']))
                <a href="{{ route('admin.pica.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
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
                    <th>Auditor</th>
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
                        $auditor = $sesi->user;
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
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-info bg-opacity-10 text-dark rounded-circle p-2 d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                    {{ $auditor ? strtoupper(substr($auditor->name, 0, 2)) : '??' }}
                                </div>
                                <div>
                                    <strong class="d-block small text-slate-800">{{ $auditor ? $auditor->name : '-' }}</strong>
                                    <small class="text-muted" style="font-size: 0.75rem;">Auditor</small>
                                </div>
                            </div>
                        </td>
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
                            <button class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold" 
                                data-bs-toggle="modal" data-bs-target="#adminAreaPicaModal{{ $sesi->id }}">
                                <i class="bi bi-pencil-square me-1"></i> Detail & Edit PICA ({{ $totalPica }})
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard-check display-4 d-block mb-3 text-secondary opacity-50"></i>
                            <h6 class="fw-bold">Belum Ada Sesi Audit dengan PICA</h6>
                            <p class="small mb-0">Seluruh temuan PICA akan dikelompokkan berdasarkan Area Audit di sini.</p>
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

<!-- Modal Detail & Admin Edit PICA per Area Audit -->
@foreach($auditSesis as $sesi)
    @php
        $auditor = $sesi->user;
        $detailsWithPica = $sesi->auditDetails->filter(fn($d) => $d->pica != null);
    @endphp
    <div class="modal fade" id="adminAreaPicaModal{{ $sesi->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            <i class="bi bi-tools text-danger me-2"></i> Pengawasan PICA — Area: {{ $sesi->area_audit }}
                        </h5>
                        <small class="text-slate-300" style="font-size: 0.8rem; color: #cbd5e1;">
                            <i class="bi bi-person me-1"></i> Auditor: {{ $auditor ? $auditor->name : '-' }} | <i class="bi bi-calendar me-1"></i> {{ $sesi->tanggal_mulai->format('d M Y') }} - {{ $sesi->tanggal_selesai->format('d M Y') }} | Total {{ $detailsWithPica->count() }} Temuan PICA
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    
                    <div class="accordion" id="adminAccordionPicaSesi{{ $sesi->id }}">
                        @foreach($detailsWithPica as $detailIndex => $detail)
                            @php
                                $pica = $detail->pica;
                                $kriteria = $detail->kriteria;
                                $isOverdue = $pica->status !== 'closed' && $pica->tenggat_waktu && $pica->tenggat_waktu->isPast();
                            @endphp
                            <div class="accordion-item border rounded-3 mb-3 shadow-sm overflow-hidden">
                                <h2 class="accordion-header" id="adminHeadingPica{{ $pica->id }}">
                                    <button class="accordion-button {{ $detailIndex > 0 ? 'collapsed' : '' }} bg-white py-3" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#adminCollapsePica{{ $pica->id }}" 
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
                                <div id="adminCollapsePica{{ $pica->id }}" class="accordion-collapse collapse {{ $detailIndex == 0 ? 'show' : '' }}" 
                                    data-bs-parent="#adminAccordionPicaSesi{{ $sesi->id }}">
                                    <div class="accordion-body bg-white p-4 border-top">
                                        
                                        <!-- Form Input Per Temuan PICA (Admin) -->
                                        <form action="{{ route('admin.pica.update', $pica->id) }}" method="POST">
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
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold small text-secondary">Akar Masalah (Root Cause) <span class="text-danger">*</span></label>
                                                    <textarea name="akar_masalah" rows="2" class="form-control bg-light" 
                                                        placeholder="Jelaskan analisis penyebab utama terjadinya temuan ini...">{{ old('akar_masalah', $pica->akar_masalah) }}</textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small text-secondary">Tindakan Koreksi (Corrective Action)</label>
                                                    <textarea name="tindakan_koreksi" rows="2" class="form-control bg-light" 
                                                        placeholder="Tindakan langsung perbaikan temuan...">{{ old('tindakan_koreksi', $pica->tindakan_koreksi) }}</textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small text-secondary">Tindakan Pencegahan (Preventive Action)</label>
                                                    <textarea name="tindakan_pencegahan" rows="2" class="form-control bg-light" 
                                                        placeholder="Tindakan agar temuan tidak terulang...">{{ old('tindakan_pencegahan', $pica->tindakan_pencegahan) }}</textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small text-secondary">PIC Penanggung Jawab Perbaikan</label>
                                                    <input type="text" name="pic_perbaikan" class="form-control bg-light" 
                                                        placeholder="Nama PIC perbaikan..." value="{{ old('pic_perbaikan', $pica->pic_perbaikan) }}">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small text-secondary">Tenggat Waktu Selesai (Target Date)</label>
                                                    <input type="date" name="tenggat_waktu" class="form-control bg-light" 
                                                        value="{{ old('tenggat_waktu', $pica->tenggat_waktu ? $pica->tenggat_waktu->format('Y-m-d') : '') }}">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small text-secondary">Status PICA <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-select bg-light fw-bold">
                                                        <option value="open" {{ old('status', $pica->status) == 'open' ? 'selected' : '' }}>🔴 Open (Belum Ditindaklanjuti)</option>
                                                        <option value="in_progress" {{ old('status', $pica->status) == 'in_progress' ? 'selected' : '' }}>🟡 In Progress (Dalam Perbaikan)</option>
                                                        <option value="closed" {{ old('status', $pica->status) == 'closed' ? 'selected' : '' }}>🟢 Closed (Selesai Verifikasi)</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small text-secondary">Catatan Verifikasi / Pengawasan (Admin)</label>
                                                    <textarea name="catatan_verifikasi_auditor" rows="2" class="form-control bg-light" 
                                                        placeholder="Wajib diisi saat status diubah ke Closed...">{{ old('catatan_verifikasi_auditor', $pica->catatan_verifikasi_auditor) }}</textarea>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">* Wajib diisi jika status diubah ke Closed.</small>
                                                </div>
                                            </div>

                                            <div class="mt-3 text-end">
                                                <button type="submit" class="btn btn-danger px-4 rounded-3 fw-semibold">
                                                    <i class="bi bi-save me-1"></i> Simpan PICA {{ $kriteria ? $kriteria->kode_kriteria : '' }}
                                                </button>
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
