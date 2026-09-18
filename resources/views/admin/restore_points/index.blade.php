@extends('layouts.app')

@section('title', 'Pusat Pemulihan Data (Recycle Bin) — SMKP Minerba')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-arrow-counterclockwise text-primary me-2"></i>Pusat Pemulihan Data
        </h2>
        <p class="text-muted mb-0">Kelola dan pulihkan data aplikasi yang terhapus (*Elemen, Sub-Elemen, Kriteria, Perusahaan, Departemen, User, Sesi Audit*) untuk menjaga konsistensi pengkodean.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
            <i class="bi bi-clock-history me-1"></i> Log Aktivitas
        </a>
        @if($totalDeletedCount > 0)
            <button class="btn btn-outline-success rounded-3 px-3 shadow-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#restoreAllModal">
                <i class="bi bi-arrow-repeat"></i>
                <span>Pulihkan Semua</span>
            </button>
            <button class="btn btn-outline-danger rounded-3 px-3 shadow-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#emptyTrashModal">
                <i class="bi bi-trash3-fill"></i>
                <span>Kosongkan Sampah</span>
            </button>
        @endif
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-3 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon-box bg-danger bg-opacity-10 text-danger" style="width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bi bi-trash3-fill"></i>
            </div>
            <div>
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Item Terhapus</small>
                <div class="h4 fw-bold text-slate-800 mb-0">{{ $totalDeletedCount }} Data</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon-box bg-primary bg-opacity-10 text-primary" style="width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
            <div>
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Struktur Pengkodean Terhapus</small>
                <div class="h4 fw-bold text-slate-800 mb-0">{{ $deletedCountsByModule['elemen'] + $deletedCountsByModule['sub_elemen'] + $deletedCountsByModule['kriteria'] }} Kriteria/Elemen</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon-box bg-success bg-opacity-10 text-success" style="width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Status Proteksi</small>
                <div class="fw-bold text-success mb-0 small d-flex align-items-center gap-1">
                    <i class="bi bi-check-circle-fill"></i> Proteksi Soft-Delete Aktif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.restore-points.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="trash_search" class="form-control bg-light border-start-0" placeholder="Cari nama, kode, atau info data terhapus..." value="{{ request('trash_search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <select name="trash_module" class="form-select" onchange="this.form.submit()">
                <option value="all" {{ request('trash_module') == 'all' || !request('trash_module') ? 'selected' : '' }}>
                    Semua Modul ({{ $deletedCountsByModule['all'] }})
                </option>
                <option value="elemen" {{ request('trash_module') == 'elemen' ? 'selected' : '' }}>
                    Elemen SMKP ({{ $deletedCountsByModule['elemen'] }})
                </option>
                <option value="sub_elemen" {{ request('trash_module') == 'sub_elemen' ? 'selected' : '' }}>
                    Sub-Elemen ({{ $deletedCountsByModule['sub_elemen'] }})
                </option>
                <option value="kriteria" {{ request('trash_module') == 'kriteria' ? 'selected' : '' }}>
                    Kriteria ({{ $deletedCountsByModule['kriteria'] }})
                </option>
                <option value="perusahaan" {{ request('trash_module') == 'perusahaan' ? 'selected' : '' }}>
                    Perusahaan ({{ $deletedCountsByModule['perusahaan'] }})
                </option>
                <option value="departemen" {{ request('trash_module') == 'departemen' ? 'selected' : '' }}>
                    Departemen ({{ $deletedCountsByModule['departemen'] }})
                </option>
                <option value="audit_sesi" {{ request('trash_module') == 'audit_sesi' ? 'selected' : '' }}>
                    Sesi Audit ({{ $deletedCountsByModule['audit_sesi'] }})
                </option>
                <option value="user" {{ request('trash_module') == 'user' ? 'selected' : '' }}>
                    User Pengguna ({{ $deletedCountsByModule['user'] }})
                </option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-3 flex-fill">
                <i class="bi bi-filter me-1"></i> Filter
            </button>
            @if(request('trash_search') || (request('trash_module') && request('trash_module') !== 'all'))
                <a href="{{ route('admin.restore-points.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset Filter">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Main Table of Deleted Application Data -->
<div class="card card-custom p-0 overflow-hidden shadow-sm border-0 mb-4">
    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="fw-bold text-slate-800 d-flex align-items-center gap-2">
            <i class="bi bi-recycle text-danger fs-5"></i>
            <span>Daftar Data Terhapus (Dapat Dipulihkan)</span>
        </div>
        <span class="badge bg-secondary rounded-pill px-3 py-1.5">Menampilkan {{ $deletedItems->count() }} Data</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 140px;">Modul Data</th>
                    <th>Nama / Kode Entitas Data</th>
                    <th>Detail & Hubungan Induk</th>
                    <th style="width: 160px;">Waktu Terhapus</th>
                    <th style="width: 160px;" class="text-end">Aksi Pemulihan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deletedItems as $item)
                    <tr>
                        <td>
                            <span class="badge {{ $item['badge_class'] }} badge-role py-1.5 px-2.5 d-inline-flex align-items-center gap-1">
                                <i class="bi {{ $item['icon'] }}"></i>
                                {{ $item['module'] }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-slate-800">{{ $item['title'] }}</div>
                            <small class="text-muted">Tipe Model: <code>{{ $item['type'] }}</code> #{{ $item['id'] }}</small>
                        </td>
                        <td>
                            <span class="text-secondary small">{{ $item['info'] }}</span>
                        </td>
                        <td>
                            <div class="small fw-semibold text-slate-700">{{ $item['deleted_at'] ? $item['deleted_at']->format('d M Y') : '-' }}</div>
                            <small class="text-muted" style="font-size: 0.72rem;">{{ $item['deleted_at'] ? $item['deleted_at']->format('H:i') . ' WIB (' . $item['deleted_at']->diffForHumans() . ')' : '' }}</small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group gap-1">
                                <!-- Tombol Pulihkan -->
                                <button type="button" class="btn btn-sm btn-outline-success rounded-2 px-2.5" 
                                        data-bs-toggle="modal" data-bs-target="#restoreModal_{{ $item['type'] }}_{{ $item['id'] }}" 
                                        title="Pulihkan data ini kembali aktif">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan
                                </button>
                                <!-- Tombol Hapus Permanen -->
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-2" 
                                        data-bs-toggle="modal" data-bs-target="#forceDeleteModal_{{ $item['type'] }}_{{ $item['id'] }}" 
                                        title="Hapus permanen (tidak dapat dipulihkan)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 64px; height: 64px;">
                                    <i class="bi bi-check-all fs-2"></i>
                                </div>
                                <h6 class="fw-bold text-slate-800 mb-1">Kotak Sampah Bersih</h6>
                                <p class="text-muted small mb-0">Tidak ada data aplikasi yang sedang terhapus atau cocok dengan kriteria pencarian.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ==================== MODALS KONFIRMASI ==================== -->
@foreach($deletedItems as $item)
    <!-- Modal Pulihkan Single Item -->
    <div class="modal fade" id="restoreModal_{{ $item['type'] }}_{{ $item['id'] }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-custom border-0">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-success">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Konfirmasi Pemulihan Data
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <span class="badge {{ $item['badge_class'] }} mb-2">{{ $item['module'] }}</span>
                        <h6 class="fw-bold text-slate-800 mb-1">{{ $item['title'] }}</h6>
                        <small class="text-muted">{{ $item['info'] }}</small>
                    </div>
                    <p class="text-secondary mb-0">
                        Apakah Anda yakin ingin memulihkan data ini? Data akan <strong>aktif kembali</strong> dan dapat diakses di menu <strong>{{ $item['module'] }}</strong>.
                    </p>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('admin.restore-points.trash.restore', ['type' => $item['type'], 'id' => $item['id']]) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-3 px-4 fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i> Ya, Pulihkan Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Permanen Single Item -->
    <div class="modal fade" id="forceDeleteModal_{{ $item['type'] }}_{{ $item['id'] }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-custom border-0">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Permanen Data
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <span class="badge {{ $item['badge_class'] }} mb-2">{{ $item['module'] }}</span>
                        <h6 class="fw-bold text-slate-800 mb-1">{{ $item['title'] }}</h6>
                        <small class="text-muted">{{ $item['info'] }}</small>
                    </div>
                    <div class="alert alert-danger py-2 small mb-0">
                        <i class="bi bi-exclamation-circle-fill me-1"></i> <strong>Peringatan Kritis:</strong> Tindakan ini akan menghapus data secara permanen dari basis data dan <strong>TIDAK DAPAT DIPULIHKAN LAGI</strong>.
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('admin.restore-points.trash.force-delete', ['type' => $item['type'], 'id' => $item['id']]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-3 px-4 fw-semibold">
                            <i class="bi bi-trash me-1"></i> Ya, Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if($totalDeletedCount > 0)
    <!-- Modal Pulihkan Semua -->
    <div class="modal fade" id="restoreAllModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-custom border-0">
                <form action="{{ route('admin.restore-points.trash.restore-all') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold text-success">
                            <i class="bi bi-arrow-repeat me-2"></i>Pulihkan Semua Data Terhapus
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <p class="text-secondary mb-3">
                            Anda akan memulihkan <strong>{{ $totalDeletedCount }} data</strong> yang saat ini berada di tempat sampah.
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Pilih Cakupan Modul yang Ingin Dipulihkan:</label>
                            <select name="module" class="form-select">
                                <option value="all">Semua Modul Sekaligus ({{ $deletedCountsByModule['all'] }} data)</option>
                                @if($deletedCountsByModule['elemen'] > 0)
                                    <option value="elemen">Khusus Elemen SMKP ({{ $deletedCountsByModule['elemen'] }} data)</option>
                                @endif
                                @if($deletedCountsByModule['sub_elemen'] > 0)
                                    <option value="sub_elemen">Khusus Sub-Elemen ({{ $deletedCountsByModule['sub_elemen'] }} data)</option>
                                @endif
                                @if($deletedCountsByModule['kriteria'] > 0)
                                    <option value="kriteria">Khusus Kriteria ({{ $deletedCountsByModule['kriteria'] }} data)</option>
                                @endif
                                @if($deletedCountsByModule['perusahaan'] > 0)
                                    <option value="perusahaan">Khusus Perusahaan ({{ $deletedCountsByModule['perusahaan'] }} data)</option>
                                @endif
                                @if($deletedCountsByModule['departemen'] > 0)
                                    <option value="departemen">Khusus Departemen ({{ $deletedCountsByModule['departemen'] }} data)</option>
                                @endif
                                @if($deletedCountsByModule['audit_sesi'] > 0)
                                    <option value="audit_sesi">Khusus Sesi Audit ({{ $deletedCountsByModule['audit_sesi'] }} data)</option>
                                @endif
                                @if($deletedCountsByModule['user'] > 0)
                                    <option value="user">Khusus User Pengguna ({{ $deletedCountsByModule['user'] }} data)</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-3 px-4 fw-semibold">
                            <i class="bi bi-arrow-repeat me-1"></i> Pulihkan Data Terpilih
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Kosongkan Kotak Sampah -->
    <div class="modal fade" id="emptyTrashModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-custom border-0">
                <form action="{{ route('admin.restore-points.trash.empty') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="bi bi-trash3-fill me-2"></i>Kosongkan Kotak Sampah
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="alert alert-danger py-2 small mb-3">
                            <i class="bi bi-exclamation-octagon-fill me-1"></i> <strong>Peringatan Kritis:</strong> Seluruh <strong>{{ $totalDeletedCount }} data</strong> di kotak sampah akan dihapus <strong>PERMANEN</strong> dari basis data.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Konfirmasi Kata Sandi Administrator:</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Masukkan password Anda untuk konfirmasi" required>
                            <small class="text-muted" style="font-size: 0.75rem;">Diperlukan verifikasi kredensial untuk mencegah penghapusan massal tidak sengaja.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 px-4 fw-semibold">
                            <i class="bi bi-trash3-fill me-1"></i> Ya, Kosongkan Permanen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection
