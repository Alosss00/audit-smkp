@extends('layouts.app')

@section('title', 'Master Sub-Elemen — SMKP Minerba')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-diagram-3-fill text-info me-2"></i>Kelola Master Sub-Elemen
        </h2>
        <p class="text-muted mb-0">Kelola turunan Sub-Elemen berdasarkan masing-masing Elemen SMKP.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button class="btn btn-info text-dark rounded-3 px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createSubModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Sub-Elemen Baru
        </button>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light align-middle text-uppercase fw-bold text-secondary small">
                <tr>
                    <th rowspan="2" class="align-middle text-center" style="width: 140px;">Induk Elemen</th>
                    <th colspan="2" class="text-center">Kode Sub</th>
                    <th colspan="2" class="text-center">Nama Sub</th>
                    <th rowspan="2" class="align-middle text-center" style="width: 130px;">Jumlah Kriteria</th>
                    <th rowspan="2" class="align-middle text-center" style="width: 110px;">Aksi</th>
                </tr>
                <tr>
                    <th class="text-center" style="width: 110px;">Sub Elemen</th>
                    <th class="text-center" style="width: 140px;">Sub-Sub Elemen</th>
                    <th>Sub Elemen</th>
                    <th>Sub-Sub Elemen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($elemens as $elemen)
                    @php
                        $subList = $elemen->subElemens;
                        $totalRowsForElemen = 0;
                        foreach ($subList as $s) {
                            $totalRowsForElemen += 1 + $s->kriterias->count();
                        }
                        $elemenRendered = false;
                    @endphp

                    @forelse($subList as $sub)
                        <!-- Row Sub-Elemen -->
                        <tr class="table-light">
                            @if(!$elemenRendered)
                                <td rowspan="{{ max(1, $totalRowsForElemen) }}" class="align-middle text-center bg-white border-end fw-bold p-3" style="width: 140px;">
                                    <span class="badge bg-primary fs-6 px-2 py-2 text-uppercase d-block mb-1">ELEMEN {{ $elemen->kode_elemen }}</span>
                                    <span class="small text-muted fw-normal d-block" style="font-size: 0.75rem;">{{ $elemen->nama_elemen }}</span>
                                </td>
                                @php $elemenRendered = true; @endphp
                            @endif

                            <td class="text-center fw-bold align-middle" style="width: 110px;">
                                <span class="badge bg-info text-dark font-monospace fs-6 py-1 px-2">Sub {{ $sub->kode_sub }}</span>
                            </td>
                            <td class="text-center text-muted align-middle" style="width: 130px;">-</td>
                            <td class="fw-bold text-slate-800 align-middle">{{ $sub->nama_sub }}</td>
                            <td class="text-muted align-middle">-</td>
                            <td class="text-center align-middle" style="width: 130px;">
                                <span class="badge bg-secondary rounded-pill px-3 py-1 fs-6">{{ $sub->kriterias->count() }} Kriteria</span>
                            </td>
                            <td class="text-center align-middle" style="width: 110px;">
                                <div class="btn-group gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-2" data-bs-toggle="modal" data-bs-target="#editSubModal{{ $sub->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('admin.sub-elemens.destroy', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Nonaktifkan Sub-Elemen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" title="Hapus Sub-Elemen">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Rows Sub-sub Elemen (Kriteria) -->
                        @foreach($sub->kriterias as $kriteria)
                            <tr>
                                <td class="text-center text-muted align-middle">-</td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-dark font-monospace fs-6 py-1 px-2">{{ $kriteria->kode_kriteria }}</span>
                                </td>
                                <td class="text-center text-muted align-middle">-</td>
                                <td class="small text-slate-700 align-middle">
                                    <i class="bi bi-arrow-return-right text-primary me-1 ms-2"></i>{{ $kriteria->deskripsi }}
                                </td>
                                <td class="text-center text-muted align-middle">-</td>
                                <td class="text-center text-muted align-middle">-</td>
                            </tr>
                        @endforeach

                    @empty
                        <tr>
                            <td class="align-middle text-center bg-white border-end fw-bold p-3" style="width: 140px;">
                                <span class="badge bg-primary fs-6 px-2 py-2 text-uppercase d-block mb-1">ELEMEN {{ $elemen->kode_elemen }}</span>
                            </td>
                            <td colspan="6" class="text-center py-3 text-muted fst-italic">Belum ada data sub-elemen pada Elemen {{ $elemen->kode_elemen }}.</td>
                        </tr>
                    @endforelse
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data master elemen & sub-elemen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modals Placed Outside Table -->
@foreach($subElemens as $sub)
    <div class="modal fade" id="editSubModal{{ $sub->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom border-0">
                <form action="{{ route('admin.sub-elemens.update', $sub->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">Edit Sub-Elemen {{ $sub->kode_sub }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Induk Elemen</label>
                            <select name="elemen_id" class="form-select" required>
                                @foreach($elemens as $el)
                                    <option value="{{ $el->id }}" {{ $sub->elemen_id == $el->id ? 'selected' : '' }}>
                                        Elemen {{ $el->kode_elemen }} - {{ $el->nama_elemen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Kode Sub-Elemen (Contoh: I.1)</label>
                            <input type="text" name="kode_sub" class="form-control" value="{{ $sub->kode_sub }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Sub-Elemen</label>
                            <input type="text" name="nama_sub" class="form-control" value="{{ $sub->nama_sub }}" required>
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
<div class="modal fade" id="createSubModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-custom border-0">
            <form action="{{ route('admin.sub-elemens.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Tambah Sub-Elemen Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Induk Elemen</label>
                        <select name="elemen_id" class="form-select" required>
                            <option value="">-- Pilih Induk Elemen --</option>
                            @foreach($elemens as $el)
                                <option value="{{ $el->id }}">Elemen {{ $el->kode_elemen }} - {{ $el->nama_elemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kode Sub-Elemen (Contoh: I.1, I.2)</label>
                        <input type="text" name="kode_sub" class="form-control" placeholder="Contoh: I.1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Sub-Elemen</label>
                        <input type="text" name="nama_sub" class="form-control" placeholder="Contoh: Kebijakan Keselamatan Pertambangan" required>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3">Simpan Sub-Elemen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
