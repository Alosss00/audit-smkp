@extends('layouts.app')

@section('title', 'Kelola Master Elemen — SMKP Minerba')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-folder-fill text-primary me-2"></i>Kelola Master Elemen SMKP
        </h2>
        <p class="text-muted mb-0">Kelola daftar Elemen Kepdirjen 185, alokasi bobot persentase, dan struktur turunan.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button class="btn btn-primary rounded-3 px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createElemenModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Elemen Baru
        </button>
    </div>
</div>

<!-- Total Bobot Accumulation Card -->
<div class="card card-custom p-4 mb-4 border-start border-4 {{ $totalBobot == 100 ? 'border-success' : 'border-warning' }}">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-pie-chart-fill fs-4 {{ $totalBobot == 100 ? 'text-success' : 'text-warning' }}"></i>
            <div>
                <h6 class="fw-bold mb-0">Total Alokasi Bobot Elemen Aktif</h6>
                <small class="text-muted">Standar evaluasi SMKP Minerba Kepdirjen 185 mensyaratkan total bobot 100.00%</small>
            </div>
        </div>
        <div class="fs-4 fw-bold {{ $totalBobot == 100 ? 'text-success' : 'text-warning' }}">
            {{ number_format($totalBobot, 2) }}% / 100.00%
        </div>
    </div>
    <div class="progress" style="height: 10px; border-radius: 8px;">
        <div class="progress-bar {{ $totalBobot == 100 ? 'bg-success' : 'bg-warning' }}" role="progressbar" 
            style="width: {{ min($totalBobot, 100) }}%"></div>
    </div>
    @if($totalBobot != 100)
        <div class="mt-2 text-warning small fw-semibold">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> Catatan: Total bobot saat ini {{ number_format($totalBobot, 2) }}%. Pastikan total bobot seluruh elemen aktif genap 100.00%.
        </div>
    @endif
</div>

<!-- Navigation Tabs for Active vs Trashed -->
<ul class="nav nav-tabs border-bottom mb-4" id="elemenTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold py-2 px-3" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-pane" type="button" role="tab">
            <i class="bi bi-check-circle-fill text-success me-1"></i> Elemen Aktif ({{ $elemens->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold py-2 px-3 text-secondary" id="trashed-tab" data-bs-toggle="tab" data-bs-target="#trashed-pane" type="button" role="tab">
            <i class="bi bi-trash-fill me-1"></i> Nonaktif / Terhapus ({{ $trashedElemens->count() }})
        </button>
    </li>
</ul>

<div class="tab-content" id="elemenTabsContent">
    <!-- Active Elements Tab -->
    <div class="tab-pane fade show active" id="active-pane" role="tabpanel">
        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;">Kode</th>
                            <th>Nama Elemen</th>
                            <th class="text-center">Bobot (%)</th>
                            <th class="text-center">Jumlah Sub-Elemen</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($elemens as $elemen)
                            <tr>
                                <td>
                                    <span class="badge bg-primary fs-6 px-3 py-2">Elemen {{ $elemen->kode_elemen }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-slate-800 fs-6">{{ $elemen->nama_elemen }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-primary border font-monospace fs-6 py-2 px-3">
                                        {{ number_format($elemen->bobot, 2) }}%
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill px-3">{{ $elemen->sub_elemens_count }} Sub-Elemen</span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group gap-1">
                                        <a href="{{ route('admin.elemens.show', $elemen->id) }}" class="btn btn-sm btn-outline-info text-dark rounded-2" title="Lihat Struktur Pohon">
                                            <i class="bi bi-diagram-3 me-1"></i> Pohon Kriteria
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-2" data-bs-toggle="modal" data-bs-target="#editElemenModal{{ $elemen->id }}">
                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                        </button>

                                        <form action="{{ route('admin.elemens.destroy', $elemen->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Nonaktifkan elemen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" title="Soft Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data elemen aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Trashed Elements Tab -->
    <div class="tab-pane fade" id="trashed-pane" role="tabpanel">
        <div class="card card-custom p-4">
            @if($trashedElemens->isEmpty())
                <div class="text-center py-4 text-muted">Tidak ada elemen yang dinonaktifkan.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 100px;">Kode</th>
                                <th>Nama Elemen</th>
                                <th class="text-center">Bobot (%)</th>
                                <th class="text-end">Aksi Pemulihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trashedElemens as $tElemen)
                                <tr>
                                    <td><span class="badge bg-secondary">Elemen {{ $tElemen->kode_elemen }}</span></td>
                                    <td class="fw-semibold text-muted">{{ $tElemen->nama_elemen }}</td>
                                    <td class="text-center text-muted">{{ number_format($tElemen->bobot, 2) }}%</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.elemens.restore', $tElemen->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-2 me-1">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Aktifkan Kembali
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.elemens.force-delete', $tElemen->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen elemen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-2">
                                                <i class="bi bi-x-circle me-1"></i> Hapus Permanen
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Modals Placed Outside Table -->
@foreach($elemens as $elemen)
    <div class="modal fade" id="editElemenModal{{ $elemen->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom border-0">
                <form action="{{ route('admin.elemens.update', $elemen->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">Edit Elemen {{ $elemen->kode_elemen }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Kode Elemen</label>
                            <input type="text" name="kode_elemen" class="form-control" value="{{ $elemen->kode_elemen }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Elemen</label>
                            <input type="text" name="nama_elemen" class="form-control" value="{{ $elemen->nama_elemen }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Bobot Persentase (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="bobot" class="form-control" value="{{ $elemen->bobot }}" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Create Modal -->
<div class="modal fade" id="createElemenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-custom border-0">
            <form action="{{ route('admin.elemens.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Tambah Elemen Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kode Elemen (Contoh: I, II, III)</label>
                        <input type="text" name="kode_elemen" class="form-control" placeholder="Contoh: I" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Elemen</label>
                        <input type="text" name="nama_elemen" class="form-control" placeholder="Contoh: Keselamatan Pertambangan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Bobot Persentase (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="bobot" class="form-control" placeholder="Contoh: 15.00" required>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3">Simpan Elemen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
