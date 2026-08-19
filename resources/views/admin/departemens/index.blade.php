@extends('layouts.app')

@section('title', 'Master Data Departemen — Audit SMKP')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-diagram-3-fill me-2 text-primary"></i>Master Data Departemen
        </h4>
        <p class="text-muted mb-0 small">Kelola 27 struktur departemen pertambangan terdaftar dalam sistem audit SMKP.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCreateDepartemen">
            <i class="bi bi-plus-circle me-1"></i> Tambah Departemen
        </button>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-diagram-3-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Total Departemen</span>
                    <h5 class="fw-bold mb-0 text-slate-800">{{ $departemens->count() }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Departemen Aktif</span>
                    <h5 class="fw-bold mb-0 text-slate-800">{{ $departemens->where('is_active', true)->count() }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-info bg-opacity-10 text-info">
                    <i class="bi bi-journal-check fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Terhubung Sesi Audit</span>
                    <h5 class="fw-bold mb-0 text-slate-800">{{ $departemens->filter(fn($d) => $d->auditSesis->count() > 0)->count() }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Search Card --}}
<div class="card card-custom p-4 mb-4">
    <form action="{{ route('admin.departemens.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-8 col-lg-9">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama departemen (contoh: Departemen Mining, Departemen HSE)..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4 col-lg-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-pill px-4 w-100 fw-semibold">Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.departemens.index') }}" class="btn btn-light rounded-pill px-3" title="Reset Filter"><i class="bi bi-x-circle"></i></a>
            @endif
        </div>
    </form>
</div>

{{-- Data Table Card --}}
<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-secondary small fw-bold text-uppercase">
                    <th style="width: 50px;">#</th>
                    <th>Nama Departemen</th>
                    <th>Kode</th>
                    <th>Status</th>
                    <th>Jumlah Audit Sesi</th>
                    <th style="width: 130px;" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departemens as $index => $departemen)
                    <tr>
                        <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="stat-icon-box bg-light text-primary" style="width: 38px; height: 38px; font-size: 1rem;">
                                    <i class="bi bi-diagram-3"></i>
                                </div>
                                <span class="fw-bold text-slate-800">{{ $departemen->nama_departemen }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-monospace px-2 py-1 border">{{ $departemen->kode_departemen ?? '-' }}</span>
                        </td>
                        <td>
                            @if($departemen->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Aktif</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-bold">
                                {{ $departemen->auditSesis->count() }} Sesi
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <button type="button" class="btn btn-sm btn-light rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEditDepartemen{{ $departemen->id }}" title="Edit">
                                    <i class="bi bi-pencil-fill text-warning"></i>
                                </button>
                                <form action="{{ route('admin.departemens.toggle-status', $departemen->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-light rounded-circle" title="{{ $departemen->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi {{ $departemen->is_active ? 'bi-toggle-on text-success' : 'bi-toggle-off text-muted' }} fs-5"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.departemens.destroy', $departemen->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus {{ $departemen->nama_departemen }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light rounded-circle" title="Hapus">
                                        <i class="bi bi-trash-fill text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Edit Departemen --}}
                    <div class="modal fade" id="modalEditDepartemen{{ $departemen->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold text-slate-800">
                                        <i class="bi bi-pencil-square text-warning me-2"></i>Edit Data Departemen
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.departemens.update', $departemen->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body py-3">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-slate-800">Nama Departemen <span class="text-danger">*</span></label>
                                            <input type="text" name="nama_departemen" class="form-control" value="{{ $departemen->nama_departemen }}" required>
                                            <small class="text-muted">Kata "Departemen" akan otomatis ditambahkan jika belum diawali.</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-slate-800">Kode Departemen (Opsional)</label>
                                            <input type="text" name="kode_departemen" class="form-control" value="{{ $departemen->kode_departemen }}" placeholder="Contoh: D-HSE">
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="editActiveDept{{ $departemen->id }}" value="1" {{ $departemen->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold text-slate-800" for="editActiveDept{{ $departemen->id }}">Status Aktif</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-diagram-3 fs-1 text-muted d-block mb-2"></i>
                            <span class="text-muted fw-semibold">Belum ada data departemen yang sesuai.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Create Departemen --}}
<div class="modal fade" id="modalCreateDepartemen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800">
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Departemen Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.departemens.store') }}" method="POST">
                @csrf
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-800">Nama Departemen <span class="text-danger">*</span></label>
                        <input type="text" name="nama_departemen" class="form-control" placeholder="Contoh: Departemen Mining Tech Service" required>
                        <small class="text-muted">Kata "Departemen" akan otomatis ditambahkan jika tidak ditulis.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-800">Kode Departemen (Opsional)</label>
                        <input type="text" name="kode_departemen" class="form-control" placeholder="Contoh: MTS">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Departemen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
