<div class="col-12 col-xl-6">
    <div class="card card-custom h-100 border-start border-4 {{ $borderClass }} p-3 bg-white shadow-sm">
        <!-- Top Status & Code Header -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1">
                    {{ ucfirst($pica->kategori_temuan ?? 'Minor') }}
                </span>
                <span class="badge bg-light text-dark border px-2 py-1 small fw-bold">
                    {{ $pica->auditDetail->kriteria->kode_kriteria ?? '-' }}
                </span>
            </div>
            <div>
                @php
                    $status = $pica->status ?? 'open';
                    $stBadge = $status === 'closed' ? 'bg-success' : ($status === 'in_progress' ? 'bg-warning text-dark' : 'bg-danger');
                    $stLabel = $status === 'closed' ? 'Closed / Selesai' : ($status === 'in_progress' ? 'In Progress' : 'Open / Belum Tindak Lanjut');
                @endphp
                <span class="badge {{ $stBadge }} rounded-pill px-3 py-1">
                    <i class="bi {{ $status === 'closed' ? 'bi-check-circle' : ($status === 'in_progress' ? 'bi-clock-history' : 'bi-exclamation-circle') }} me-1"></i>
                    {{ $stLabel }}
                </span>
            </div>
        </div>

        <!-- Hierarchy Context -->
        <div class="mb-2">
            <small class="text-muted d-block">
                Elemen {{ $pica->auditDetail->kriteria->subElemen->elemen->kode_elemen ?? '' }} &bull; {{ $pica->auditDetail->kriteria->subElemen->nama_sub_elemen ?? '-' }}
            </small>
            <h6 class="fw-bold text-slate-800 mb-1">
                {{ $pica->auditDetail->kriteria->nama_kriteria ?? '-' }}
            </h6>
        </div>

        <!-- Finding Description -->
        <div class="p-3 bg-light rounded-3 mb-3 border">
            <span class="text-muted small fw-semibold d-block mb-1"><i class="bi bi-search text-danger me-1"></i> Uraian Ketidaksesuaian (Temuan):</span>
            <p class="text-slate-800 small mb-0 fw-medium">
                {{ $pica->deskripsi_temuan ?? 'Tidak ada uraian temuan.' }}
            </p>
        </div>

        <!-- Corrective Action Details -->
        <div class="row g-2 mb-3 small">
            <div class="col-12 col-md-6">
                <div class="p-2 border rounded-3 h-100 bg-white">
                    <span class="text-muted d-block mb-1"><i class="bi bi-question-circle text-primary me-1"></i> Akar Masalah:</span>
                    <span class="text-slate-700">{{ $pica->akar_masalah ?? '-' }}</span>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-2 border rounded-3 h-100 bg-white">
                    <span class="text-muted d-block mb-1"><i class="bi bi-shield-check text-success me-1"></i> Tindakan Koreksi:</span>
                    <span class="text-slate-700">{{ $pica->tindakan_koreksi ?? '-' }}</span>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-2 border rounded-3 h-100 bg-white">
                    <span class="text-muted d-block mb-1"><i class="bi bi-arrow-repeat text-info me-1"></i> Tindakan Pencegahan:</span>
                    <span class="text-slate-700">{{ $pica->tindakan_pencegahan ?? '-' }}</span>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-2 border rounded-3 h-100 bg-white">
                    <span class="text-muted d-block mb-1"><i class="bi bi-person-check text-secondary me-1"></i> PIC / Target:</span>
                    <strong class="text-slate-800 d-block">{{ $pica->pj_nama ?? 'Belum Ditunjuk' }}</strong>
                    <span class="text-muted small">
                        Target: {{ $pica->target_selesai ? \Carbon\Carbon::parse($pica->target_selesai)->format('d M Y') : '-' }}
                    </span>
                </div>
            </div>
        </div>

        @if(!empty($pica->auditor_catatan))
            <div class="alert alert-info py-2 px-3 mb-3 small rounded-3">
                <strong class="d-block text-dark"><i class="bi bi-chat-quote-fill me-1"></i> Catatan Verifikasi Auditor:</strong>
                <span class="text-slate-700">{{ $pica->auditor_catatan }}</span>
            </div>
        @endif

        <!-- Card Footer Actions -->
        <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-auto">
            <small class="text-muted">
                Skor Saat Audit: <strong class="text-danger">{{ $pica->auditDetail->nilai ?? 0 }} / {{ $pica->auditDetail->kriteria->nilai_maksimal ?? 4 }}</strong>
            </small>
            <div>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.pica.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-tools me-1"></i> Monitoring PICA
                    </a>
                @elseif(auth()->user()->role === 'auditor')
                    <a href="{{ route('auditor.pica.edit', $pica->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-pencil-square me-1"></i> Tindak Lanjut
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
