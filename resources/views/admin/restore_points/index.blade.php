@extends('layouts.app')

@section('title', 'Pusat Pemulihan & Restore Point Data — SMKP Minerba')

@section('content')
@php
    $activeTab = request('tab', ($totalDeletedCount > 0 ? 'trash' : 'snapshots'));
@endphp

<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-arrow-counterclockwise text-primary me-2"></i>Pusat Pemulihan & Restore Point
        </h2>
        <p class="text-muted mb-0">Kelola pemulihan data terhapus (*Recycle Bin*) dan snapshot pencadangan basis data sistem (*disaster recovery*).</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
            <i class="bi bi-clock-history me-1"></i> Log Aktivitas
        </a>
        <button class="btn btn-primary rounded-3 px-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createRestorePointModal">
            <i class="bi bi-camera-fill"></i>
            <span>Buat Snapshot Baru</span>
        </button>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-3 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon-box bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-trash3-fill"></i>
            </div>
            <div>
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Data di Kotak Sampah</small>
                <div class="h4 fw-bold text-slate-800 mb-0">{{ $totalDeletedCount }} Item Terhapus</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-database-check"></i>
            </div>
            <div>
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Snapshot Cadangan</small>
                <div class="h4 fw-bold text-slate-800 mb-0">{{ $totalCount }} Snapshot</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Status Proteksi Sistem</small>
                <div class="fw-bold text-success mb-0 small d-flex align-items-center gap-1">
                    <i class="bi bi-check-circle-fill"></i> Soft Delete & Auto-Backup Aktif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Tabs -->
<ul class="nav nav-pills mb-4 bg-white p-2 rounded-4 shadow-sm border" id="restorePointTabs" role="tablist">
    <li class="nav-item flex-fill text-center" role="presentation">
        <button class="nav-link w-100 py-2.5 fw-bold rounded-3 {{ $activeTab === 'trash' ? 'active' : '' }}" id="trash-tab" data-bs-toggle="pill" data-bs-target="#trash-pane" type="button" role="tab">
            <i class="bi bi-recycle me-2"></i>
            Kotak Sampah & Data Terhapus
            <span class="badge {{ $activeTab === 'trash' ? 'bg-white text-primary' : 'bg-danger text-white' }} rounded-pill ms-2 px-2 py-0.5">
                {{ $totalDeletedCount }}
            </span>
        </button>
    </li>
    <li class="nav-item flex-fill text-center" role="presentation">
        <button class="nav-link w-100 py-2.5 fw-bold rounded-3 {{ $activeTab === 'snapshots' ? 'active' : '' }}" id="snapshots-tab" data-bs-toggle="pill" data-bs-target="#snapshots-pane" type="button" role="tab">
            <i class="bi bi-hdd-network me-2"></i>
            Snapshot Basis Data & Rollback
            <span class="badge {{ $activeTab === 'snapshots' ? 'bg-white text-primary' : 'bg-primary text-white' }} rounded-pill ms-2 px-2 py-0.5">
                {{ $totalCount }}
            </span>
        </button>
    </li>
</ul>

<div class="tab-content" id="restorePointTabContent">

    <!-- ==================== TAB 1: KOTAK SAMPAH (ALL DELETED DATA) ==================== -->
    <div class="tab-pane fade {{ $activeTab === 'trash' ? 'show active' : '' }}" id="trash-pane" role="tabpanel">
        
        <!-- Filter Bar Trash -->
        <div class="card card-custom p-3 mb-4">
            <form method="GET" action="{{ route('admin.restore-points.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="trash">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="trash_search" class="form-control" placeholder="Cari nama data, kode, atau modul..." value="{{ request('trash_search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="trash_module" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Modul Data ({{ $deletedCountsByModule['all'] }}) --</option>
                        <option value="audit_sesi" {{ request('trash_module') === 'audit_sesi' ? 'selected' : '' }}>Sesi Audit ({{ $deletedCountsByModule['audit_sesi'] }})</option>
                        <option value="user" {{ request('trash_module') === 'user' ? 'selected' : '' }}>User Pengguna ({{ $deletedCountsByModule['user'] }})</option>
                        <option value="perusahaan" {{ request('trash_module') === 'perusahaan' ? 'selected' : '' }}>Perusahaan ({{ $deletedCountsByModule['perusahaan'] }})</option>
                        <option value="departemen" {{ request('trash_module') === 'departemen' ? 'selected' : '' }}>Departemen ({{ $deletedCountsByModule['departemen'] }})</option>
                        <option value="elemen" {{ request('trash_module') === 'elemen' ? 'selected' : '' }}>Elemen SMKP ({{ $deletedCountsByModule['elemen'] }})</option>
                        <option value="sub_elemen" {{ request('trash_module') === 'sub_elemen' ? 'selected' : '' }}>Sub-Elemen ({{ $deletedCountsByModule['sub_elemen'] }})</option>
                        <option value="kriteria" {{ request('trash_module') === 'kriteria' ? 'selected' : '' }}>Kriteria ({{ $deletedCountsByModule['kriteria'] }})</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    @if(request()->filled('trash_search') || request()->filled('trash_module'))
                        <a href="{{ route('admin.restore-points.index', ['tab' => 'trash']) }}" class="btn btn-outline-secondary rounded-3">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Bulk Action Toolbar -->
        @if($totalDeletedCount > 0)
            <div class="d-flex justify-content-between align-items-center mb-3 px-1 flex-wrap gap-2">
                <div class="small text-muted">
                    Menampilkan <strong>{{ $deletedItems->count() }}</strong> data terhapus dalam kotak sampah.
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('admin.restore-points.trash.restore-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MEMULIHKAN SEMUA data terhapus kembali ke sistem?')">
                        @csrf
                        <input type="hidden" name="module" value="{{ request('trash_module', 'all') }}">
                        <button type="submit" class="btn btn-sm btn-success rounded-3 px-3 shadow-sm">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan Semua ({{ $deletedItems->count() }})
                        </button>
                    </form>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#emptyTrashModal">
                        <i class="bi bi-trash3 me-1"></i> Kosongkan Tempat Sampah
                    </button>
                </div>
            </div>
        @endif

        <!-- Deleted Data Table -->
        <div class="card card-custom p-4">
            @if($deletedItems->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check2-circle fs-1 text-success opacity-75 d-block mb-2"></i>
                    <h6 class="fw-bold text-slate-800">Kotak Sampah Bersih!</h6>
                    <p class="text-muted small mb-0">Tidak ada data yang sedang terhapus. Semua data aktif dan beroperasi normal.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 170px;">Modul Entitas</th>
                                <th>Nama & Rincian Data Terhapus</th>
                                <th style="width: 200px;">Waktu Dihapus</th>
                                <th class="text-end" style="width: 230px;">Aksi Pemulihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deletedItems as $item)
                                <tr>
                                    <td>
                                        <span class="badge {{ $item['badge_class'] }} rounded-pill px-2.5 py-1.5 small">
                                            <i class="bi {{ $item['icon'] }} me-1"></i>{{ $item['module'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-slate-800">{{ $item['title'] }}</div>
                                        <small class="text-muted d-block" style="font-size: 0.78rem;">{{ $item['info'] }}</small>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-slate-800">{{ $item['deleted_at'] ? $item['deleted_at']->format('d M Y') : '-' }}</div>
                                        <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                            <i class="bi bi-clock me-1"></i>{{ $item['deleted_at'] ? $item['deleted_at']->format('H:i:s') : '-' }} ({{ $item['deleted_at'] ? $item['deleted_at']->diffForHumans() : '' }})
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <!-- Tombol Restore -->
                                            <form action="{{ route('admin.restore-points.trash.restore', ['type' => $item['type'], 'id' => $item['id']]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary rounded-3 px-2.5 py-1 shadow-sm" title="Pulihkan Data Ini">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan
                                                </button>
                                            </form>

                                            <!-- Tombol Force Delete -->
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-2.5 py-1" data-bs-toggle="modal" data-bs-target="#forceDeleteModal{{ $item['type'] }}_{{ $item['id'] }}" title="Hapus Permanen">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== TAB 2: SYSTEM SNAPSHOTS (RESTORE POINTS) ==================== -->
    <div class="tab-pane fade {{ $activeTab === 'snapshots' ? 'show active' : '' }}" id="snapshots-pane" role="tabpanel">
        
        <!-- Filter Bar Snapshots -->
        <div class="card card-custom p-3 mb-4">
            <form method="GET" action="{{ route('admin.restore-points.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="snapshots">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama snapshot, kode (RP-...), atau deskripsi..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="tipe" class="form-select">
                        <option value="">-- Semua Tipe Snapshot --</option>
                        <option value="manual" {{ request('tipe') === 'manual' ? 'selected' : '' }}>Manual (Dibuat Admin)</option>
                        <option value="auto_prerollback" {{ request('tipe') === 'auto_prerollback' ? 'selected' : '' }}>Auto (Pre-Rollback Safety)</option>
                        <option value="auto_finalisasi" {{ request('tipe') === 'auto_finalisasi' ? 'selected' : '' }}>Auto (Finalisasi Audit)</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    @if(request()->filled('search') || request()->filled('tipe'))
                        <a href="{{ route('admin.restore-points.index', ['tab' => 'snapshots']) }}" class="btn btn-outline-secondary rounded-3">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Restore Points Table -->
        <div class="card card-custom p-4">
            @if($restorePoints->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-archive fs-1 opacity-50 d-block mb-2 text-primary"></i>
                    <h6 class="fw-bold text-slate-800">Belum Ada Snapshot Cadangan Tersedia</h6>
                    <p class="text-muted small mb-3">Buat Restore Point pertama Anda untuk mengamankan seluruh basis data audit SMKP.</p>
                    <button class="btn btn-sm btn-primary rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#createRestorePointModal">
                        <i class="bi bi-camera-fill me-1"></i> Buat Snapshot Sekarang
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 210px;">Kode & Tipe</th>
                                <th>Nama & Deskripsi Snapshot</th>
                                <th style="width: 170px;">Ukuran & Data</th>
                                <th style="width: 180px;">Dibuat Oleh & Waktu</th>
                                <th class="text-end" style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($restorePoints as $rp)
                                <tr>
                                    <td>
                                        <div class="font-monospace fw-bold text-slate-800 small mb-1">
                                            <i class="bi bi-hdd-stack me-1 text-primary"></i>{{ $rp->kode }}
                                        </div>
                                        @if($rp->tipe === 'manual')
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-person-fill me-1"></i>Manual Admin
                                            </span>
                                        @elseif($rp->tipe === 'auto_prerollback')
                                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-50 rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-shield-shaded me-1 text-warning"></i>Pre-Rollback Safety
                                            </span>
                                        @else
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-gear-fill me-1"></i>{{ $rp->tipe }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-slate-800">{{ $rp->nama }}</div>
                                        @if($rp->deskripsi)
                                            <small class="text-muted d-block text-truncate" style="max-width: 320px;">
                                                {{ $rp->deskripsi }}
                                            </small>
                                        @else
                                            <small class="text-muted fst-italic">— Tidak ada catatan deskripsi —</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-slate-800">
                                            <i class="bi bi-file-earmark-zip text-secondary me-1"></i>{{ $rp->file_size }}
                                        </div>
                                        @if(!empty($rp->table_counts))
                                            <button class="btn btn-link p-0 text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#summaryModal{{ $rp->id }}" style="font-size: 0.75rem;">
                                                <i class="bi bi-list-columns me-1"></i>Lihat Rekap Data ({{ array_sum($rp->table_counts) }} baris)
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-slate-800">{{ $rp->created_at->format('d M Y, H:i') }}</div>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                            <i class="bi bi-person me-1"></i>{{ $rp->user->name ?? 'Sistem Otomatis' }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-primary rounded-3 px-2.5 py-1.5 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-gear-fill me-1"></i> Opsi
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <li>
                                                    <button class="dropdown-item text-warning fw-semibold d-flex align-items-center gap-2 py-2" data-bs-toggle="modal" data-bs-target="#restoreModal{{ $rp->id }}">
                                                        <i class="bi bi-arrow-counterclockwise text-warning fs-6"></i>
                                                        <span>Pulihkan / Rollback</span>
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-primary fw-semibold d-flex align-items-center gap-2 py-2" href="{{ route('admin.restore-points.download', $rp->id) }}">
                                                        <i class="bi bi-download text-primary fs-6"></i>
                                                        <span>Unduh File (.json)</span>
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item text-danger d-flex align-items-center gap-2 py-2" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $rp->id }}">
                                                        <i class="bi bi-trash fs-6"></i>
                                                        <span>Hapus Snapshot</span>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Menampilkan {{ $restorePoints->firstItem() ?? 0 }} - {{ $restorePoints->lastItem() ?? 0 }} dari total {{ $restorePoints->total() }} snapshot
                    </div>
                    <div>
                        {{ $restorePoints->appends(['tab' => 'snapshots'])->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ==================== MODALS ==================== -->

<!-- Modal Create Restore Point -->
<div class="modal fade" id="createRestorePointModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 card-custom overflow-hidden">
            <div class="modal-header border-bottom bg-slate-900 text-white p-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary rounded-3 p-2 text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-hdd-network-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Buat Snapshot Baru</h5>
                        <small class="text-slate-300">Ambil snapshot basis data SMKP saat ini</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.restore-points.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-info border-info border-opacity-25 rounded-3 small mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Snapshot akan mencakup seluruh data: <strong>Perusahaan, Departemen, Elemen, Sub-Elemen, Kriteria, User, Sesi Audit, Nilai Matriks, dan PICA</strong>.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-slate-800 small">Nama / Label Restore Point <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: Snapshot Sebelum Audit Semester 2 / Sebelum Import" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-slate-800 small">Deskripsi / Catatan Tambahan</label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="3" placeholder="Catatan opsional mengenai tujuan atau kondisi pembuatan snapshot ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-white">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                        <i class="bi bi-camera-fill me-1"></i> Simpan Snapshot Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Empty Trash Confirmation -->
<div class="modal fade" id="emptyTrashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 card-custom overflow-hidden">
            <div class="modal-header border-bottom bg-danger text-white p-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Kosongkan Tempat Sampah</h5>
                        <small class="text-white-50">Hapus Permanen Seluruh Data Terhapus</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.restore-points.trash.empty') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-warning border-warning border-opacity-25 rounded-3 mb-3 small">
                        <strong>PERINGATAN:</strong> Tindakan ini akan <strong>menghapus permanen</strong> sebanyak <strong>{{ $totalDeletedCount }} data</strong> dari seluruh modul. Data yang dihapus permanen tidak dapat dipulihkan kembali kecuali melalui Snapshot Basis Data.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-slate-800 small">Konfirmasi Kata Sandi Admin <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3" placeholder="Masukkan kata sandi Anda..." required autocomplete="current-password">
                    </div>
                </div>
                <div class="modal-footer border-top bg-white">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4">
                        <i class="bi bi-trash3-fill me-1"></i> Kosongkan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modals for Force Delete Trash Items -->
@foreach($deletedItems as $item)
    <div class="modal fade" id="forceDeleteModal{{ $item['type'] }}_{{ $item['id'] }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 card-custom overflow-hidden">
                <div class="modal-header border-bottom bg-danger text-white p-3 px-4">
                    <h5 class="modal-title fw-bold text-white mb-0">Hapus Permanen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.restore-points.trash.force-delete', ['type' => $item['type'], 'id' => $item['id']]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body p-4 bg-light">
                        <p class="mb-1">Apakah Anda yakin ingin menghapus <strong>permanen</strong> data ini?</p>
                        <div class="p-3 bg-white rounded-3 border my-2 small">
                            <span class="badge {{ $item['badge_class'] }} mb-1">{{ $item['module'] }}</span>
                            <div class="fw-bold text-slate-800">{{ $item['title'] }}</div>
                            <small class="text-muted">{{ $item['info'] }}</small>
                        </div>
                        <small class="text-danger"><i class="bi bi-exclamation-circle me-1"></i> Data tidak dapat dipulihkan kembali setelah dihapus permanen.</small>
                    </div>
                    <div class="modal-footer border-top bg-white">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 px-3">
                            <i class="bi bi-trash-fill me-1"></i> Ya, Hapus Permanen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modals for Snapshot Summary, Rollback & Delete -->
@foreach($restorePoints as $rp)
    <!-- Modal Summary Baris Data -->
    <div class="modal fade" id="summaryModal{{ $rp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 card-custom overflow-hidden">
                <div class="modal-header border-bottom bg-slate-900 text-white p-3 px-4">
                    <h5 class="modal-title fw-bold text-white mb-0">
                        <i class="bi bi-list-check text-primary me-2"></i>Rekap Data: {{ $rp->kode }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <p class="small text-muted mb-3">Rincian jumlah baris data yang tersimpan pada titik snapshot <strong>{{ $rp->nama }}</strong>:</p>
                    <div class="list-group rounded-3 shadow-sm">
                        @if(!empty($rp->table_counts))
                            @foreach($rp->table_counts as $tbl => $count)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <span class="font-monospace small fw-semibold text-slate-800">{{ $tbl }}</span>
                                    <span class="badge bg-primary rounded-pill px-2.5 py-1">{{ number_format($count) }} baris</span>
                                </div>
                            @endforeach
                        @else
                            <div class="list-group-item text-center text-muted small py-3">Tidak ada data rincian baris.</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-top bg-white">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rollback / Restore Confirmation -->
    <div class="modal fade" id="restoreModal{{ $rp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 card-custom overflow-hidden">
                <div class="modal-header border-bottom bg-danger text-white p-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0">Konfirmasi Pemulihan Sistem</h5>
                            <small class="text-white-50">Rollback Data ke Titik Snapshot</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.restore-points.restore', $rp->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="alert alert-warning border-warning border-opacity-25 rounded-3 mb-3">
                            <strong class="d-block mb-1"><i class="bi bi-shield-exclamation me-1"></i> PERHATIAN:</strong>
                            Memulihkan data ke <strong>"{{ $rp->nama }}" ({{ $rp->kode }})</strong> akan menggantikan seluruh data sistem saat ini dengan kondisi snapshot tanggal <strong>{{ $rp->created_at->format('d M Y H:i:s') }}</strong>.
                        </div>

                        <div class="p-3 bg-white rounded-3 border mb-3 small">
                            <div class="row g-2">
                                <div class="col-5 text-muted">Kode Snapshot:</div>
                                <div class="col-7 font-monospace fw-bold">{{ $rp->kode }}</div>
                                <div class="col-5 text-muted">Tanggal Snapshot:</div>
                                <div class="col-7 fw-semibold">{{ $rp->created_at->format('d M Y, H:i') }}</div>
                                <div class="col-5 text-muted">Proteksi:</div>
                                <div class="col-7 text-success fw-bold"><i class="bi bi-check-circle me-1"></i>Auto Safety Backup dibuat otomatis</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-slate-800 small">Masukkan Kata Sandi Admin untuk Konfirmasi <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3" placeholder="Kata sandi akun Anda..." required autocomplete="current-password">
                            <small class="text-muted" style="font-size: 0.75rem;">Diperlukan untuk memvalidasi otorisasi eksekusi pemulihan.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-white">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 px-4">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Ya, Pulihkan Data Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Delete Restore Point Snapshot -->
    <div class="modal fade" id="deleteModal{{ $rp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 card-custom overflow-hidden">
                <div class="modal-header border-bottom bg-slate-900 text-white p-3 px-4">
                    <h5 class="modal-title fw-bold text-white mb-0">Hapus Snapshot Cadangan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.restore-points.destroy', $rp->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body p-4 bg-light">
                        <p class="mb-2">Apakah Anda yakin ingin menghapus snapshot <strong>{{ $rp->nama }}</strong> (<code>{{ $rp->kode }}</code>)?</p>
                        <small class="text-muted">File snapshot cadangan fisik di server juga akan dihapus secara permanen.</small>
                    </div>
                    <div class="modal-footer border-top bg-white">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 px-3">
                            <i class="bi bi-trash me-1"></i> Hapus Permanen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection
