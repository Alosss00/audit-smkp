@extends('layouts.app')

@section('title', 'Log Aktivitas User & Perubahan File — SMKP Minerba')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-clock-history text-primary me-2"></i>Log Aktivitas User & Audit Trail File
        </h2>
        <p class="text-muted mb-0">Pantau seluruh jejak aktivitas pengguna, riwayat autentikasi, serta perubahan file lampiran & bukti PICA.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <span class="badge bg-slate-900 text-white px-3 py-2 rounded-pill">
            <i class="bi bi-shield-check me-1"></i> Audit Trail Active
        </span>
    </div>
</div>

<!-- Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Cari aktivitas atau nama user..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="modul" class="form-select">
                <option value="">-- Semua Modul --</option>
                @foreach($modules as $m)
                    <option value="{{ $m }}" {{ request('modul') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="user_id" class="form-select">
                <option value="">-- Semua User --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->username }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 rounded-3">
                <i class="bi bi-filter me-1"></i> Filter
            </button>
            @if(request()->filled('search') || request()->filled('modul') || request()->filled('user_id') || request()->filled('file_only'))
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary rounded-3">Reset</a>
            @endif
        </div>
        <div class="col-12 mt-2">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="file_only" id="fileOnlySwitch" value="1" {{ request('file_only') == 1 ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label small fw-semibold text-slate-700" for="fileOnlySwitch">
                    <i class="bi bi-paperclip me-1 text-danger"></i> Tampilkan Khusus Perubahan File & Lampiran (Upload/Bukti/Lampiran)
                </label>
            </div>
        </div>
    </form>
</div>

<!-- Logs Table Card -->
<div class="card card-custom p-4">
    @if($logs->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-journal-x fs-1 opacity-50 d-block mb-2"></i>
            Belum ada catatan log aktivitas yang sesuai kriteria pencarian.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 170px;">Waktu & Tanggal</th>
                        <th style="width: 180px;">Pengguna (User)</th>
                        <th style="width: 130px;">Modul</th>
                        <th>Aktivitas / Tindakan</th>
                        <th class="text-end" style="width: 120px;">Detail Change</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>
                                <div class="small fw-bold text-slate-800">{{ $log->waktu_perubahan ? $log->waktu_perubahan->format('d M Y') : '-' }}</div>
                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $log->waktu_perubahan ? $log->waktu_perubahan->format('H:i:s') : '-' }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light rounded-circle border p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="bi bi-person-fill text-secondary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-slate-800 small">{{ $log->user->name ?? 'Sistem' }}</div>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $log->user->role ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($log->modul === 'Autentikasi')
                                    <span class="badge bg-secondary rounded-pill px-2.5 py-1.5"><i class="bi bi-key me-1"></i>Autentikasi</span>
                                @elseif($log->modul === 'PICA')
                                    <span class="badge bg-danger rounded-pill px-2.5 py-1.5"><i class="bi bi-tools me-1"></i>PICA</span>
                                @elseif($log->modul === 'Sesi Audit')
                                    <span class="badge bg-primary rounded-pill px-2.5 py-1.5"><i class="bi bi-journal-check me-1"></i>Sesi Audit</span>
                                @elseif($log->modul === 'Manajemen User')
                                    <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1.5"><i class="bi bi-people me-1"></i>User</span>
                                @else
                                    <span class="badge bg-info text-dark rounded-pill px-2.5 py-1.5">{{ $log->modul }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold text-slate-800 small d-block">{{ $log->tindakan }}</span>
                                @if(str_contains(strtolower($log->tindakan), 'upload') || str_contains(strtolower($log->tindakan), 'bukti') || str_contains(strtolower($log->tindakan), 'lampiran'))
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.68rem;">
                                        <i class="bi bi-paperclip me-1"></i> File Activity
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(!empty($log->data_lama) || !empty($log->data_baru))
                                    <button class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                        <i class="bi bi-code-slash me-1"></i> Data JSON
                                    </button>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-muted">
                Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari total {{ $logs->total() }} log aktivitas
            </div>
            <div>
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

<!-- Modals for Data Changes JSON View -->
@foreach($logs as $log)
    @if(!empty($log->data_lama) || !empty($log->data_baru))
        <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 card-custom">
                    <div class="modal-header border-bottom bg-slate-900 text-white rounded-top-4">
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0">
                                <i class="bi bi-file-earmark-code me-2 text-warning"></i> Detail Perubahan Log #{{ $log->id }}
                            </h5>
                            <small class="text-slate-300">{{ $log->tindakan }} | {{ $log->waktu_perubahan ? $log->waktu_perubahan->format('d M Y H:i:s') : '' }}</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-light">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-danger mb-2"><i class="bi bi-arrow-left-circle me-1"></i> Snapshot Data Lama (Sebelum):</h6>
                                <pre class="p-3 bg-white rounded border text-danger small font-monospace" style="max-height: 250px; overflow-y: auto;">{{ !empty($log->data_lama) ? json_encode($log->data_lama, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : 'Tidak ada data lama' }}</pre>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-success mb-2"><i class="bi bi-arrow-right-circle me-1"></i> Snapshot Data Baru (Sesudah):</h6>
                                <pre class="p-3 bg-white rounded border text-success small font-monospace" style="max-height: 250px; overflow-y: auto;">{{ !empty($log->data_baru) ? json_encode($log->data_baru, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : 'Tidak ada data baru' }}</pre>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-white">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection
