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
    <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('admin.restore-points.index') }}" class="btn btn-outline-primary rounded-3 px-3 shadow-sm">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Pusat Restore Point
        </a>
        <span class="badge bg-slate-900 text-white px-3 py-2 rounded-pill d-none d-lg-inline-flex align-items-center">
            <i class="bi bi-shield-check me-1"></i> Audit Trail
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
                        <th style="width: 190px;">Pengguna (User)</th>
                        <th style="width: 130px;">Modul</th>
                        <th>Aktivitas / Tindakan</th>
                        <th class="text-end" style="width: 160px;">Detail Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        @php
                            $oldData = is_array($log->data_lama) ? $log->data_lama : (json_decode($log->data_lama, true) ?? []);
                            $newData = is_array($log->data_baru) ? $log->data_baru : (json_decode($log->data_baru, true) ?? []);
                            $hasChanges = !empty($oldData) || !empty($newData);
                            $allKeys = array_unique(array_merge(array_keys($oldData), array_keys($newData)));
                            $changedCount = 0;
                            foreach ($allKeys as $k) {
                                $oldVal = $oldData[$k] ?? null;
                                $newVal = $newData[$k] ?? null;
                                if ($oldVal !== $newVal) {
                                    $changedCount++;
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                <div class="small fw-bold text-slate-800">{{ $log->waktu_perubahan ? $log->waktu_perubahan->format('d M Y') : '-' }}</div>
                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $log->waktu_perubahan ? $log->waktu_perubahan->format('H:i:s') : '-' }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light rounded-circle border p-1 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
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
                                @if($hasChanges)
                                    <button class="btn btn-sm btn-outline-primary rounded-3 px-2.5 py-1.5 shadow-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                        <i class="bi bi-eye"></i>
                                        <span>Detail Change</span>
                                        @if($changedCount > 0)
                                            <span class="badge bg-primary text-white rounded-pill ms-1" style="font-size: 0.65rem;">{{ $changedCount }}</span>
                                        @endif
                                    </button>
                                @else
                                    <span class="text-muted small fst-italic">— Tidak Ada Data —</span>
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

<!-- Helper Functions for formatting blade values -->
@php
if (!function_exists('formatAuditValue')) {
    function formatAuditValue($val) {
        if (is_null($val)) {
            return '<span class="text-muted fst-italic small">— Kosong / Null —</span>';
        }
        if (is_bool($val)) {
            return $val 
                ? '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 small"><i class="bi bi-check-circle me-1"></i>True (Aktif)</span>' 
                : '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1 small"><i class="bi bi-x-circle me-1"></i>False (Nonaktif)</span>';
        }
        if (is_array($val)) {
            return '<pre class="p-2 mb-0 bg-light border rounded small font-monospace" style="max-height: 120px; overflow-y: auto;">' . e(json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) . '</pre>';
        }
        $str = (string) $val;
        if (strlen($str) > 100) {
            return '<div class="text-break small">' . e($str) . '</div>';
        }
        return '<span class="font-monospace fw-semibold small">' . e($str) . '</span>';
    }
}
@endphp

<!-- Modals for Data Changes Comparison & JSON View -->
@foreach($logs as $log)
    @php
        $oldData = is_array($log->data_lama) ? $log->data_lama : (json_decode($log->data_lama, true) ?? []);
        $newData = is_array($log->data_baru) ? $log->data_baru : (json_decode($log->data_baru, true) ?? []);
        $hasChanges = !empty($oldData) || !empty($newData);
        $allKeys = array_unique(array_merge(array_keys($oldData), array_keys($newData)));
    @endphp

    @if($hasChanges)
        <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 card-custom overflow-hidden">
                    <!-- Modal Header -->
                    <div class="modal-header border-bottom bg-slate-900 text-white p-3 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 40px; height: 40px; background: linear-gradient(135deg, #0284c7, #0369a1) !important;">
                                <i class="bi bi-file-earmark-diff fs-5"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-white mb-0">
                                    Detail Perubahan Aktivitas #{{ $log->id }}
                                </h5>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-secondary rounded-pill small px-2 py-0.5">{{ $log->modul }}</span>
                                    <span class="text-slate-400 small">|</span>
                                    <small class="text-slate-300">
                                        <i class="bi bi-person me-1"></i>{{ $log->user->name ?? 'Sistem' }} &bull;
                                        <i class="bi bi-clock me-1 ms-1"></i>{{ $log->waktu_perubahan ? $log->waktu_perubahan->format('d M Y H:i:s') : '-' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Action Banner -->
                    <div class="bg-slate-100 border-bottom p-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background-color: #f8fafc;">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tindakan Aktivitas:</span>
                            <span class="fw-bold text-slate-800">{{ $log->tindakan }}</span>
                        </div>
                        <ul class="nav nav-pills nav-fill bg-white p-1 rounded-3 border" id="logTab{{ $log->id }}" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-1.5 px-3 small fw-bold rounded-2" id="diff-tab-{{ $log->id }}" data-bs-toggle="pill" data-bs-target="#diff-content-{{ $log->id }}" type="button" role="tab">
                                    <i class="bi bi-table me-1 text-primary"></i> Tabel Komparasi Field
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1.5 px-3 small fw-bold rounded-2" id="json-tab-{{ $log->id }}" data-bs-toggle="pill" data-bs-target="#json-content-{{ $log->id }}" type="button" role="tab">
                                    <i class="bi bi-code-square me-1 text-warning"></i> Raw JSON Code
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-4 bg-light">
                        <div class="tab-content" id="logTabContent{{ $log->id }}">
                            
                            <!-- TAB 1: Visual Comparison Table -->
                            <div class="tab-pane fade show active" id="diff-content-{{ $log->id }}" role="tabpanel">
                                @if(empty($allKeys))
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                                        Tidak ada rincian atribut data yang tersimpan.
                                    </div>
                                @else
                                    <div class="card border rounded-3 overflow-hidden shadow-sm">
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle mb-0">
                                                <thead class="bg-white text-slate-700">
                                                    <tr class="small text-uppercase">
                                                        <th style="width: 22%;">Nama Atribut / Kolom</th>
                                                        <th style="width: 34%;" class="text-danger bg-danger bg-opacity-10 border-danger border-opacity-25">
                                                            <i class="bi bi-arrow-left-circle me-1"></i> Nilai Sebelum (Lama)
                                                        </th>
                                                        <th style="width: 34%;" class="text-success bg-success bg-opacity-10 border-success border-opacity-25">
                                                            <i class="bi bi-arrow-right-circle me-1"></i> Nilai Sesudah (Baru)
                                                        </th>
                                                        <th style="width: 10%;" class="text-center">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($allKeys as $key)
                                                        @php
                                                            $hasOld = array_key_exists($key, $oldData);
                                                            $hasNew = array_key_exists($key, $newData);
                                                            $valOld = $hasOld ? $oldData[$key] : null;
                                                            $valNew = $hasNew ? $newData[$key] : null;
                                                            $isEqual = ($hasOld && $hasNew && $valOld === $valNew);
                                                            $isCreated = (!$hasOld && $hasNew);
                                                            $isDeleted = ($hasOld && !$hasNew);
                                                            $isModified = ($hasOld && $hasNew && !$isEqual);
                                                        @endphp
                                                        <tr class="{{ $isModified ? 'table-warning bg-opacity-25' : ($isCreated ? 'table-success bg-opacity-25' : ($isDeleted ? 'table-danger bg-opacity-25' : '')) }}">
                                                            <td class="fw-bold text-slate-800 font-monospace small">
                                                                <i class="bi bi-tag-fill me-1 text-muted" style="font-size: 0.75rem;"></i>{{ $key }}
                                                            </td>
                                                            <td class="{{ $isModified ? 'bg-danger bg-opacity-10' : '' }}">
                                                                @if(!$hasOld)
                                                                    <span class="text-muted fst-italic small">— Tidak Ada —</span>
                                                                @else
                                                                    {!! formatAuditValue($valOld) !!}
                                                                @endif
                                                            </td>
                                                            <td class="{{ $isModified ? 'bg-success bg-opacity-10' : '' }}">
                                                                @if(!$hasNew)
                                                                    <span class="text-muted fst-italic small">— Dihapus —</span>
                                                                @else
                                                                    {!! formatAuditValue($valNew) !!}
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($isModified)
                                                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small">
                                                                        <i class="bi bi-pencil-fill me-1"></i>Diubah
                                                                    </span>
                                                                @elseif($isCreated)
                                                                    <span class="badge bg-success rounded-pill px-2 py-1 small">
                                                                        <i class="bi bi-plus-circle me-1"></i>Baru
                                                                    </span>
                                                                @elseif($isDeleted)
                                                                    <span class="badge bg-danger rounded-pill px-2 py-1 small">
                                                                        <i class="bi bi-trash me-1"></i>Dihapus
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                                                                        Tetap
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- TAB 2: Side-by-Side Raw JSON View -->
                            <div class="tab-pane fade" id="json-content-{{ $log->id }}" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card border border-danger border-opacity-25 h-100 shadow-sm">
                                            <div class="card-header bg-danger bg-opacity-10 py-2 px-3 d-flex justify-content-between align-items-center">
                                                <span class="fw-bold text-danger small"><i class="bi bi-arrow-left-circle me-1"></i> Snapshot Data Lama (Sebelum)</span>
                                                @if(!empty($oldData))
                                                    <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2 rounded small btn-copy-json" data-copy="{{ addslashes(json_encode($oldData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}">
                                                        <i class="bi bi-clipboard me-1"></i>Copy
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="card-body p-0">
                                                <pre class="p-3 bg-white text-danger mb-0 font-monospace small" style="max-height: 380px; overflow-y: auto; border: 0;">{{ !empty($oldData) ? json_encode($oldData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '// Tidak ada data lama' }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border border-success border-opacity-25 h-100 shadow-sm">
                                            <div class="card-header bg-success bg-opacity-10 py-2 px-3 d-flex justify-content-between align-items-center">
                                                <span class="fw-bold text-success small"><i class="bi bi-arrow-right-circle me-1"></i> Snapshot Data Baru (Sesudah)</span>
                                                @if(!empty($newData))
                                                    <button type="button" class="btn btn-xs btn-outline-success py-0 px-2 rounded small btn-copy-json" data-copy="{{ addslashes(json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}">
                                                        <i class="bi bi-clipboard me-1"></i>Copy
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="card-body p-0">
                                                <pre class="p-3 bg-white text-success mb-0 font-monospace small" style="max-height: 380px; overflow-y: auto; border: 0;">{{ !empty($newData) ? json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '// Tidak ada data baru' }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer bg-light py-2 px-4">
                        <button type="button" class="btn btn-secondary rounded-3 px-3" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-copy-json');
            if (!btn) return;
            const text = btn.getAttribute('data-copy');
            if (!navigator.clipboard) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
            } else {
                navigator.clipboard.writeText(text);
            }
            
            const origHtml = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Tersalin!';
            setTimeout(function() {
                btn.innerHTML = origHtml;
            }, 1800);
        });
    });
</script>
@endpush

@endsection
