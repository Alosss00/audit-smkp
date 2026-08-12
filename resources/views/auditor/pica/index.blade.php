@extends('layouts.app')

@section('title', 'Tindak Lanjut PICA — SMKP Minerba')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-tools text-primary me-2"></i> Tindak Lanjut PICA (Corrective Action)
        </h3>
        <p class="text-muted small mb-0">Rekam jejak dan verifikasi tindakan perbaikan temuan audit SMKP Minerba</p>
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
                    placeholder="Cari temuan, area, atau PIC..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select bg-light">
                <option value="">-- Semua Status PICA --</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open (Belum Ditindaklanjuti)</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress (Dalam Perbaikan)</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed (Selesai Verifikasi)</option>
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

<!-- PICA Table Card -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Area & Tanggal Audit</th>
                    <th>Kriteria & Deskripsi Temuan</th>
                    <th>Akar Masalah</th>
                    <th>PIC & Tenggat</th>
                    <th>Status</th>
                    <th class="pe-4 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($picas as $index => $pica)
                    @php
                        $detail = $pica->auditDetail;
                        $sesi = $detail ? $detail->auditSesi : null;
                        $kriteria = $detail ? $detail->kriteria : null;
                    @endphp
                    <tr>
                        <td class="ps-4 fw-semibold text-muted">{{ $picas->firstItem() + $index }}</td>
                        <td>
                            <strong class="d-block text-slate-800">{{ $sesi ? $sesi->area_audit : '-' }}</strong>
                            <small class="text-muted"><i class="bi bi-calendar me-1"></i> {{ $sesi ? $sesi->tanggal_audit->format('d M Y') : '-' }}</small>
                        </td>
                        <td style="max-width: 280px;">
                            <span class="badge bg-secondary mb-1">{{ $kriteria ? $kriteria->kode_kriteria : '-' }}</span>
                            <div class="small text-dark fw-semibold text-truncate" title="{{ $pica->deskripsi_temuan }}">
                                {{ $pica->deskripsi_temuan }}
                            </div>
                        </td>
                        <td style="max-width: 200px;">
                            @if($pica->akar_masalah)
                                <div class="small text-muted text-truncate" title="{{ $pica->akar_masalah }}">
                                    {{ $pica->akar_masalah }}
                                </div>
                            @else
                                <span class="badge bg-light text-muted border">Belum diisi</span>
                            @endif
                        </td>
                        <td>
                            @if($pica->pic_perbaikan)
                                <strong class="d-block small text-slate-800"><i class="bi bi-person me-1"></i> {{ $pica->pic_perbaikan }}</strong>
                            @else
                                <span class="text-muted small">-</span>
                            @endif

                            @if($pica->tenggat_waktu)
                                <small class="text-danger d-block"><i class="bi bi-clock me-1"></i> {{ $pica->tenggat_waktu->format('d M Y') }}</small>
                            @endif
                        </td>
                        <td>
                            @if($pica->status === 'open')
                                <span class="badge bg-danger rounded-pill px-3 py-2">Open</span>
                            @elseif($pica->status === 'in_progress')
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">In Progress</span>
                            @else
                                <span class="badge bg-success rounded-pill px-3 py-2">Closed</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" 
                                data-bs-toggle="modal" data-bs-target="#picaModal{{ $pica->id }}">
                                <i class="bi bi-pencil-square me-1"></i> Tindak Lanjut
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard-check display-4 d-block mb-3 text-secondary opacity-50"></i>
                            <h6 class="fw-bold">Belum Ada Data PICA</h6>
                            <p class="small mb-0">Temuan audit yang bernilai di bawah maksimal akan otomatis muncul di sini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($picas->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $picas->links() }}
        </div>
    @endif
</div>

<!-- Modal Forms for PICA Follow-Up & Verification -->
@foreach($picas as $pica)
    @php
        $detail = $pica->auditDetail;
        $sesi = $detail ? $detail->auditSesi : null;
        $kriteria = $detail ? $detail->kriteria : null;
    @endphp
    <div class="modal fade" id="picaModal{{ $pica->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-slate-900 text-white rounded-top-4" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-tools text-info me-2"></i> Form Tindak Lanjut & Verifikasi PICA
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('auditor.pica.update', $pica->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        
                        <!-- Callout Info Temuan -->
                        <div class="p-3 bg-light rounded-3 border mb-4">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Area & Tanggal Audit:</small>
                                    <strong class="text-slate-800">{{ $sesi ? $sesi->area_audit : '-' }} ({{ $sesi ? $sesi->tanggal_audit->format('d M Y') : '-' }})</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Kode Kriteria Audit:</small>
                                    <strong class="text-slate-800">{{ $kriteria ? $kriteria->kode_kriteria : '-' }}</strong>
                                </div>
                                <div class="col-12 mt-2">
                                    <small class="text-muted d-block">Deskripsi Temuan Audit:</small>
                                    <div class="fw-semibold text-danger">{{ $pica->deskripsi_temuan }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Input Tindak Lanjut -->
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

                            <hr class="my-3">

                            <!-- Status & Verifikasi Auditor -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Status PICA <span class="text-danger">*</span></label>
                                <select name="status" class="form-select bg-light fw-bold">
                                    <option value="open" {{ old('status', $pica->status) == 'open' ? 'selected' : '' }}>🔴 Open (Belum Ditindaklanjuti)</option>
                                    <option value="in_progress" {{ old('status', $pica->status) == 'in_progress' ? 'selected' : '' }}>🟡 In Progress (Dalam Perbaikan)</option>
                                    <option value="closed" {{ old('status', $pica->status) == 'closed' ? 'selected' : '' }}>🟢 Closed (Selesai Verifikasi)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">Catatan Verifikasi Auditor</label>
                                <textarea name="catatan_verifikasi_auditor" rows="2" class="form-control bg-light" 
                                    placeholder="Wajib diisi saat status diubah ke Closed...">{{ old('catatan_verifikasi_auditor', $pica->catatan_verifikasi_auditor) }}</textarea>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">* Wajib diisi jika status diubah ke Closed.</small>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan PICA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
