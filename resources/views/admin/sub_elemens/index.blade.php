@extends('layouts.app')

@section('title', 'Master Sub-Elemen — SMKP Minerba')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-diagram-3-fill text-info me-2"></i>Kelola Master Sub-Elemen & Sub-sub Elemen
        </h2>
        <p class="text-muted mb-0">Kelola hirarki struktur Sub-Elemen dan Sub-sub Elemen (Kriteria Pertanyaan Penilaian SMKP).</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <button class="btn btn-info text-dark rounded-3 px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createSubModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Sub-Elemen
        </button>
        <button class="btn btn-primary rounded-3 px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createSubSubModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Sub-sub Elemen
        </button>
    </div>
</div>

@forelse($elemens as $elemen)
    <div class="card card-custom mb-4 overflow-hidden border-0 shadow-sm">
        <!-- Card Header per Elemen -->
        <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary fs-6 px-3 py-2 text-uppercase">Elemen {{ $elemen->kode_elemen }}</span>
                <div>
                    <h5 class="fw-bold text-slate-800 mb-0">{{ $elemen->nama_elemen }}</h5>
                    <div class="small text-muted mt-1">
                        Bobot Elemen: <span class="fw-semibold text-dark">{{ number_format($elemen->bobot, 2) }}%</span>
                        @if($elemen->total_nilai_sub_elemen > 0)
                            @php
                                $terpakai = $elemen->total_nilai_sub_terpakai;
                                $maxSub = $elemen->total_nilai_sub_elemen;
                                $isOver = $terpakai > $maxSub;
                            @endphp
                            <span class="ms-3 border-start ps-3">
                                Total Sub-Elemen: 
                                <span class="badge {{ $isOver ? 'bg-danger' : 'bg-light text-dark border' }} font-monospace ms-1">
                                    {{ number_format($terpakai, 2) }} / {{ number_format($maxSub, 2) }}
                                </span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-info text-dark rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#createSubForElemenModal{{ $elemen->id }}">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Sub-Elemen
                </button>
            </div>
        </div>

        <!-- Table for this Elemen -->
        <div class="card-body p-0">
            @if($elemen->subElemens->isEmpty())
                <div class="p-4 text-center text-muted fst-italic">
                    Belum ada data Sub-Elemen untuk Elemen {{ $elemen->kode_elemen }}. 
                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none ms-1" data-bs-toggle="modal" data-bs-target="#createSubForElemenModal{{ $elemen->id }}">
                        Klik di sini untuk membuat Sub-Elemen baru.
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fw-bold text-secondary small border-bottom">
                            <tr>
                                <th style="width: 140px;" class="text-center">Kode Sub</th>
                                <th style="width: 140px;" class="text-center">Kode Sub-Sub</th>
                                <th>Nama / Deskripsi Pertanyaan</th>
                                <th style="width: 140px;" class="text-center">Nilai Maksimal</th>
                                <th style="width: 140px;" class="text-center">Jumlah Kriteria</th>
                                <th style="width: 160px;" class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($elemen->subElemens as $sub)
                                <!-- Row Sub-Elemen -->
                                <tr class="table-light border-bottom {{ $sub->is_na ? 'bg-warning bg-opacity-10' : '' }}">
                                    <td class="text-center fw-bold align-middle">
                                        <span class="badge bg-info text-dark font-monospace fs-6 py-1 px-2">Sub {{ $sub->kode_sub }}</span>
                                    </td>
                                    <td class="text-center text-muted align-middle">-</td>
                                    <td class="fw-bold text-slate-800 align-middle">
                                        {{ $sub->nama_sub }}
                                        @if($sub->is_na)
                                            <span class="badge bg-warning text-dark ms-2"><i class="bi bi-slash-circle me-1"></i>N/A (Not Applicable)</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle font-monospace fw-bold {{ $sub->is_na ? 'text-muted text-decoration-line-through' : 'text-primary' }}">
                                        {{ number_format($sub->nilai_maksimal ?? 0, 2) }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($sub->kriterias->count() === 1 && $sub->kriterias->first()->kode_kriteria === $sub->kode_sub)
                                            <span class="badge bg-info text-dark rounded-pill px-3 py-1 fs-6" title="Sub-Elemen ini dinilai langsung tanpa sub-sub elemen">Penilaian Langsung</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill px-3 py-1 fs-6">{{ $sub->kriterias->count() }} Kriteria</span>
                                        @endif
                                    </td>
                                    <td class="text-end align-middle pe-3">
                                        <div class="btn-group gap-1">
                                            <form action="{{ route('admin.sub-elemens.toggle-na', $sub->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $sub->is_na ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning' }} rounded-2" title="{{ $sub->is_na ? 'Aktifkan Kembali Sub-Elemen ini' : 'Set Sub-Elemen ini menjadi N/A' }}">
                                                    <i class="bi bi-slash-circle me-1"></i> {{ $sub->is_na ? 'N/A Active' : 'Set N/A' }}
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-sm btn-outline-success rounded-2" data-bs-toggle="modal" data-bs-target="#createSubSubForSubModal{{ $sub->id }}" title="Tambah Sub-sub Elemen di bawah {{ $sub->kode_sub }}">
                                                <i class="bi bi-plus-lg"></i> Sub-sub
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-2" data-bs-toggle="modal" data-bs-target="#editSubModal{{ $sub->id }}" title="Edit Sub-Elemen">
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
                                    @if($kriteria->kode_kriteria !== $sub->kode_sub)
                                        <tr class="border-bottom {{ $kriteria->is_na || $sub->is_na ? 'table-warning bg-opacity-25' : '' }}">
                                            <td class="text-center text-muted align-middle">-</td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-dark font-monospace fs-6 py-1 px-2">{{ $kriteria->kode_kriteria }}</span>
                                            </td>
                                            <td class="small text-slate-700 align-middle">
                                                <div class="fw-semibold">
                                                    <i class="bi bi-arrow-return-right text-primary me-1 ms-2"></i>
                                                    <span class="{{ $kriteria->is_na || $sub->is_na ? 'text-muted text-decoration-line-through' : '' }}">{{ $kriteria->deskripsi }}</span>
                                                    @if($kriteria->is_na || $sub->is_na)
                                                        <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;"><i class="bi bi-slash-circle me-1"></i>N/A</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center align-middle font-monospace text-muted small {{ $kriteria->is_na || $sub->is_na ? 'text-decoration-line-through' : '' }}">
                                                {{ number_format($kriteria->nilai_maksimal, 2) }}
                                            </td>
                                            <td class="text-center text-muted align-middle">-</td>
                                            <td class="text-end align-middle pe-3">
                                                <div class="btn-group gap-1">
                                                    <form action="{{ route('admin.kriterias.toggle-na', $kriteria->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm {{ $kriteria->is_na ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning' }} rounded-2 py-0 px-2" style="font-size: 0.75rem;" title="{{ $kriteria->is_na ? 'Aktifkan Kriteria' : 'Set Kriteria menjadi N/A' }}">
                                                            {{ $kriteria->is_na ? 'N/A' : 'Set N/A' }}
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-2 py-0 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#editSubSubModal{{ $kriteria->id }}">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </button>
                                                    <form action="{{ route('admin.kriterias.destroy', $kriteria->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus/nonaktifkan Sub-sub Elemen {{ $kriteria->kode_kriteria }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 py-0 px-1" style="font-size: 0.75rem;" title="Hapus Sub-sub Elemen">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@empty
    <div class="card card-custom p-4 text-center text-muted">
        Belum ada data master elemen.
    </div>
@endforelse

<!-- Modal General: Tambah Sub-sub Elemen Baru (Multi-Baris Dinamis) -->
<div class="modal fade" id="createSubSubModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content card-custom border-0">
            <form action="{{ route('admin.kriterias.store') }}" method="POST" id="formGeneralBatchSubSub">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Sub-sub Elemen (Bisa Tambah Lebih dari 1 Sekaligus)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- 1. Pilih Induk Sub-Elemen -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Induk Sub-Elemen <span class="text-danger">*</span></label>
                        <select name="sub_elemen_id" id="generalSubElemenSelect" class="form-select select-searchable" required>
                            <option value="">-- Pilih Induk Sub-Elemen --</option>
                            @foreach($subElemens as $s)
                                <option value="{{ $s->id }}" 
                                    data-kode="{{ $s->kode_sub }}" 
                                    data-nama="{{ $s->nama_sub }}" 
                                    data-max="{{ (float) ($s->nilai_maksimal ?? 4.00) }}"
                                    data-count="{{ $s->kriterias->where('kode_kriteria', '!=', $s->kode_sub)->count() }}">
                                    Sub {{ $s->kode_sub }} - {{ $s->nama_sub }} (Nilai Max: {{ number_format($s->nilai_maksimal ?? 4, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">Setelah memilih Sub-Elemen induk, Anda dapat menambahkan 1 atau beberapa Sub-sub Elemen sekaligus di bawah.</div>
                    </div>

                    <!-- 2. Banner Informasi Induk Sub-Elemen -->
                    <div id="generalSubElemenInfo" class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 rounded-3 d-none mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="badge bg-info text-dark font-monospace fs-6 me-2" id="infoKodeSub">Sub -</span>
                                <strong class="text-slate-800 fs-6" id="infoNamaSub">Nama Sub Elemen</strong>
                            </div>
                            <div>
                                <span class="badge bg-white text-dark border font-monospace px-3 py-2">
                                    Nilai Max Induk: <span class="fw-bold text-primary" id="infoMaxScore">4.00</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Dynamic Sub-sub Items Container -->
                    <div id="generalSubSubRowsContainer" class="d-none">
                        <div class="d-flex align-items-center justify-content-between mb-3 bg-light p-3 rounded-3 border">
                            <div>
                                <h6 class="fw-bold text-slate-800 mb-0">
                                    <i class="bi bi-list-task text-primary me-2"></i>Daftar Sub-sub Elemen yang akan Ditambahkan
                                </h6>
                                <small class="text-muted">Klik tombol <strong>+ Tambah Baris</strong> untuk menambah lebih dari 1 sub-sub elemen.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary fw-semibold rounded-pill px-3 shadow-sm btn-add-general-row">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris Sub-sub
                            </button>
                        </div>

                        <!-- Rows Wrapper -->
                        <div id="generalSubSubItemsWrapper" class="d-flex flex-column gap-3 mb-3">
                            <!-- Injected dynamically by JS -->
                        </div>

                        <!-- Bottom Add Row Button -->
                        <div class="text-center p-3 border border-dashed rounded-3 bg-light bg-opacity-50">
                            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-4 btn-add-general-row">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Baris Sub-sub Elemen (+ Baris)
                            </button>
                        </div>
                    </div>

                    <!-- Placeholder when no sub-elemen is selected -->
                    <div id="generalSelectPlaceholder" class="p-4 text-center text-muted bg-light rounded-3 border">
                        <i class="bi bi-diagram-3 fs-2 d-block text-secondary mb-2"></i>
                        Silakan pilih <strong>Induk Sub-Elemen</strong> di atas terlebih dahulu untuk memunculkan formulir input Sub-sub Elemen.
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold" id="btnSubmitGeneralBatch" disabled>
                        <i class="bi bi-check-lg me-1"></i> Simpan Semua Sub-sub Elemen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Per Sub-Elemen: Tambah Sub-sub Elemen di bawah Sub Tersebut (Multi-Baris Dinamis) -->
@foreach($subElemens as $sub)
    @php
        $maxInt = ceil($sub->nilai_maksimal ?? 4);
        $existingCount = $sub->kriterias->where('kode_kriteria', '!=', $sub->kode_sub)->count();
    @endphp
    <div class="modal fade" id="createSubSubForSubModal{{ $sub->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content card-custom border-0">
                <form action="{{ route('admin.kriterias.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="sub_elemen_id" value="{{ $sub->id }}">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-plus-lg text-success me-2"></i>Tambah Sub-sub Elemen di bawah <span class="badge bg-info text-dark font-monospace">Sub {{ $sub->kode_sub }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-light border small mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <strong>Induk Sub-Elemen:</strong> Sub {{ $sub->kode_sub }} - {{ $sub->nama_sub }}
                            </div>
                            <div>
                                <span class="badge bg-light text-dark border font-monospace">Nilai Max Induk: {{ number_format($sub->nilai_maksimal ?? 4, 2) }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-3 bg-light p-3 rounded-3 border">
                            <div>
                                <h6 class="fw-bold text-slate-800 mb-0">
                                    <i class="bi bi-list-task text-primary me-2"></i>Daftar Sub-sub Elemen yang akan Ditambahkan
                                </h6>
                                <small class="text-muted">Tambahkan satu atau lebih Sub-sub Elemen sekaligus di bawah ini.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-success text-white fw-semibold rounded-pill px-3 shadow-sm btn-add-sub-modal-row" data-target="subSubItemsWrapper{{ $sub->id }}" data-kode-sub="{{ $sub->kode_sub }}" data-max-score="{{ (float)($sub->nilai_maksimal ?? 4) }}" data-existing-count="{{ $existingCount }}">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris Sub-sub
                            </button>
                        </div>

                        <!-- Rows Wrapper -->
                        <div id="subSubItemsWrapper{{ $sub->id }}" class="sub-sub-items-container d-flex flex-column gap-3 mb-3" data-kode-sub="{{ $sub->kode_sub }}" data-max-score="{{ (float)($sub->nilai_maksimal ?? 4) }}" data-existing-count="{{ $existingCount }}">
                            <!-- Injected dynamically by JS on modal open -->
                        </div>

                        <div class="text-center p-3 border border-dashed rounded-3 bg-light bg-opacity-50">
                            <button type="button" class="btn btn-sm btn-outline-success fw-semibold rounded-pill px-4 btn-add-sub-modal-row" data-target="subSubItemsWrapper{{ $sub->id }}" data-kode-sub="{{ $sub->kode_sub }}" data-max-score="{{ (float)($sub->nilai_maksimal ?? 4) }}" data-existing-count="{{ $existingCount }}">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Baris Sub-sub Elemen (+ Baris)
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success text-white rounded-3 px-4 fw-semibold"><i class="bi bi-check-lg me-1"></i>Simpan Semua Sub-sub Elemen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Edit Rubrik Pedoman Penilaian (Kriteria) -->
@foreach($subElemens as $sub)
    @foreach($sub->kriterias as $kriteria)
        <div class="modal fade" id="editSubSubModal{{ $kriteria->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content card-custom border-0">
                    <form action="{{ route('admin.kriterias.update', $kriteria->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="from_edit_modal" value="1">
                        <input type="hidden" name="sub_elemen_id" value="{{ $kriteria->sub_elemen_id }}">
                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Rubrik Pedoman Penilaian — <span class="badge bg-dark font-monospace">{{ $kriteria->kode_kriteria }}</span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Summary Card Info -->
                            <div class="p-3 bg-light rounded-3 border mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-secondary font-monospace">Sub {{ $sub->kode_sub }}</span>
                                    <span class="badge bg-success font-monospace">Nilai Max: {{ (int) $kriteria->nilai_maksimal }}</span>
                                </div>
                                <div class="fw-bold text-slate-800 small">{{ $sub->nama_sub }}</div>
                                <div class="text-muted small mt-1"><i class="bi bi-card-text me-1"></i>{{ $kriteria->deskripsi }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_na" id="edit_is_na_kriteria_{{ $kriteria->id }}" value="1" {{ $kriteria->is_na ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold small text-dark" for="edit_is_na_kriteria_{{ $kriteria->id }}">Set N/A (Not Applicable) secara default</label>
                                </div>
                            </div>

                            <div class="mb-2">
                                <h6 class="fw-bold text-slate-800 small mb-2"><i class="bi bi-bookmark-star-fill text-warning me-1"></i>Isi Rubrik Pedoman Penilaian (Jumlah Nilai Max: {{ (int) $kriteria->nilai_maksimal }})</h6>
                            </div>

                            @php
                                $maxValEdit = (int) ceil($kriteria->nilai_maksimal);
                                $pedomanArr = $kriteria->pedoman_array ?? [];
                            @endphp
                            <div class="row g-3">
                                @for($i = 0; $i <= $maxValEdit; $i++)
                                    @php
                                        $pctEdit = $maxValEdit > 0 ? round(($i / $maxValEdit) * 100) : 0;
                                        $colSizeEdit = ($maxValEdit > 4) ? 'col-md-6' : 'col-12';
                                    @endphp
                                    <div class="{{ $colSizeEdit }}">
                                        <label class="form-label fw-semibold small text-dark">Pedoman Nilai {{ $i }} ({{ $pctEdit }}% dari Max {{ $maxValEdit }})</label>
                                        <textarea name="pedoman_nilai[{{ $i }}]" class="form-control" rows="2" placeholder="Acuan pemberian Nilai {{ $i }}...">{{ $pedomanArr[(string)$i] ?? ($kriteria->{"pedoman_nilai_$i"} ?? '') }}</textarea>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-3"><i class="bi bi-check-lg me-1"></i>Simpan Perubahan Rubrik</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endforeach

<!-- Edit Sub-Elemen Modals -->
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
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nilai Maksimal <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="1000" name="nilai_maksimal" class="form-control" value="{{ $sub->nilai_maksimal ?? 4.00 }}" required>
                            <div class="form-text text-muted">Akumulasi total nilai maksimal sub-elemen tidak boleh melebihi Total Nilai Sub-Elemen pada Elemen induk.</div>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_na" id="edit_is_na_sub_{{ $sub->id }}" value="1" {{ $sub->is_na ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold small text-dark" for="edit_is_na_sub_{{ $sub->id }}">Set N/A (Not Applicable) secara default</label>
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

<!-- Create Sub-Elemen Modal -->
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
                        <select name="elemen_id" class="form-select select-searchable" required>
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
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nilai Maksimal <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" max="1000" name="nilai_maksimal" class="form-control" value="4.00" required>
                        <div class="form-text text-muted">Akumulasi total nilai maksimal sub-elemen tidak boleh melebihi Total Nilai Sub-Elemen pada Elemen induk.</div>
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

<!-- Modal Per-Elemen: Tambah Sub-Elemen -->
@foreach($elemens as $elemen)
    <div class="modal fade" id="createSubForElemenModal{{ $elemen->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom border-0">
                <form action="{{ route('admin.sub-elemens.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="elemen_id" value="{{ $elemen->id }}">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">Tambah Sub-Elemen pada Elemen {{ $elemen->kode_elemen }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-light border small mb-3">
                            <strong>Induk Elemen:</strong> Elemen {{ $elemen->kode_elemen }} - {{ $elemen->nama_elemen }}
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Kode Sub-Elemen (Contoh: {{ $elemen->kode_elemen }}.1)</label>
                            <input type="text" name="kode_sub" class="form-control" placeholder="Contoh: {{ $elemen->kode_elemen }}.1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Sub-Elemen</label>
                            <input type="text" name="nama_sub" class="form-control" placeholder="Contoh: Nama Sub-Elemen Baru" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nilai Maksimal <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="1000" name="nilai_maksimal" class="form-control" value="4.00" required>
                            <div class="form-text text-muted">Akumulasi total nilai maksimal sub-elemen tidak boleh melebihi Total Nilai Sub-Elemen pada Elemen induk.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-dark rounded-3 fw-semibold">Simpan Sub-Elemen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentGeneralData = null;

    // Helper to generate Rubrik Pedoman Nilai inputs (0..N)
    function generateRubrikInputs(index, maxScore) {
        const maxScoreInt = Math.max(1, Math.ceil(parseFloat(maxScore) || 4));
        let html = '';

        for (let i = 0; i <= maxScoreInt; i++) {
            const pct = Math.round((i / maxScoreInt) * 100);
            let colorClass = 'text-danger';
            if (pct >= 100) colorClass = 'text-success';
            else if (pct >= 75) colorClass = 'text-primary';
            else if (pct >= 50) colorClass = 'text-info';
            else if (pct >= 25) colorClass = 'text-warning';

            html += `
                <div class="col-md-6">
                    <label class="form-label small fw-semibold ${colorClass} mb-1">
                        Pedoman Nilai ${i} (${pct}% dari Max ${maxScore})
                    </label>
                    <textarea name="sub_subs[${index}][pedoman_nilai][${i}]" class="form-control form-control-sm" rows="1" placeholder="Acuan penilaian untuk skor ${i}..."></textarea>
                </div>
            `;
        }
        return html;
    }

    // Helper to generate a single Sub-sub Elemen Card
    function createSubSubCardHtml(index, kodeSub, maxScore, existingCount) {
        const suggestedKode = (kodeSub && kodeSub !== '-') 
            ? `${kodeSub}.${parseInt(existingCount || 0) + index + 1}` 
            : '';
        const uniqueId = 'rubrik_' + Math.random().toString(36).substring(2, 9) + '_' + index;
        const formattedMax = (parseFloat(maxScore) || 4.00).toFixed(2);

        return `
            <div class="card card-custom border p-3 shadow-none bg-white sub-sub-item-card position-relative" data-index="${index}">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-pill px-3 py-1 fs-6 item-index-badge">#${index + 1}</span>
                        <span class="fw-bold text-slate-800">Sub-sub Elemen</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-remove-sub-row rounded-2 px-2 py-1" title="Hapus Baris Ini">
                        <i class="bi bi-trash3-fill me-1"></i> <span class="small">Hapus Baris</span>
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-slate-700 mb-1">Kode Sub-sub <span class="text-danger">*</span></label>
                        <input type="text" name="sub_subs[${index}][kode_kriteria]" class="form-control font-monospace input-kode-kriteria" value="${suggestedKode}" placeholder="Contoh: ${kodeSub || 'I.1'}.1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-700 mb-1">Deskripsi / Pertanyaan Sub-sub Elemen <span class="text-danger">*</span></label>
                        <input type="text" name="sub_subs[${index}][deskripsi]" class="form-control input-deskripsi" placeholder="Tulis deskripsi / pertanyaan audit..." required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-slate-700 mb-1">Nilai Maksimal <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" max="1000" name="sub_subs[${index}][nilai_maksimal]" class="form-control font-monospace input-nilai-max" value="${formattedMax}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-secondary mb-1">
                            <i class="bi bi-file-earmark-text text-primary me-1"></i>Dokumen Wajib / Acuan Persyaratan (Opsional)
                        </label>
                        <input type="text" name="sub_subs[${index}][persyaratan_dokumen]" class="form-control form-control-sm" placeholder="Contoh: SK KTT, SOP Inspeksi Terkait, Matriks Kompetensi...">
                    </div>

                    <!-- Accordion Pedoman Nilai -->
                    <div class="col-12">
                        <div class="accordion border rounded-3 overflow-hidden" id="acc_${uniqueId}">
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed py-2 px-3 small bg-light text-slate-700" type="button" data-bs-toggle="collapse" data-bs-target="#col_${uniqueId}">
                                        <i class="bi bi-bookmark-star text-warning me-2 fs-6"></i>
                                        <strong>Rubrik Pedoman Penilaian (Opsional - Klik untuk mengisi pedoman nilai 0 s/d ${Math.ceil(parseFloat(maxScore) || 4)})</strong>
                                    </button>
                                </h2>
                                <div id="col_${uniqueId}" class="accordion-collapse collapse" data-bs-parent="#acc_${uniqueId}">
                                    <div class="accordion-body p-3 bg-white">
                                        <div class="row g-2 rubrik-inputs-box">
                                            ${generateRubrikInputs(index, maxScore)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Helper to re-index cards inside a container
    function reindexContainer(container) {
        if (!container) return;
        const cards = container.querySelectorAll('.sub-sub-item-card');
        cards.forEach((card, newIdx) => {
            card.dataset.index = newIdx;
            const badge = card.querySelector('.item-index-badge');
            if (badge) badge.textContent = `#${newIdx + 1}`;

            // Update inputs name prefixes
            card.querySelectorAll('[name^="sub_subs["]').forEach(input => {
                const name = input.getAttribute('name');
                const updatedName = name.replace(/^sub_subs\[\d+\]/, `sub_subs[${newIdx}]`);
                input.setAttribute('name', updatedName);
            });
        });
    }

    // Helper to append a new card to a wrapper
    function appendCardToWrapper(wrapper, kodeSub, maxScore, existingCount) {
        if (!wrapper) return;
        const currentCount = wrapper.querySelectorAll('.sub-sub-item-card').length;
        const cardHtml = createSubSubCardHtml(currentCount, kodeSub, maxScore, existingCount);
        wrapper.insertAdjacentHTML('beforeend', cardHtml);
    }

    // 1. Logic for General Modal (#createSubSubModal)
    const generalSelect = document.getElementById('generalSubElemenSelect');
    const generalInfo = document.getElementById('generalSubElemenInfo');
    const generalRowsContainer = document.getElementById('generalSubSubRowsContainer');
    const generalItemsWrapper = document.getElementById('generalSubSubItemsWrapper');
    const generalPlaceholder = document.getElementById('generalSelectPlaceholder');
    const generalSubmitBtn = document.getElementById('btnSubmitGeneralBatch');

    function handleGeneralSelectChange() {
        if (!generalSelect) return;
        const selectedVal = generalSelect.value;

        if (!selectedVal) {
            currentGeneralData = null;
            if (generalInfo) generalInfo.classList.add('d-none');
            if (generalRowsContainer) generalRowsContainer.classList.add('d-none');
            if (generalPlaceholder) generalPlaceholder.classList.remove('d-none');
            if (generalSubmitBtn) generalSubmitBtn.disabled = true;
            if (generalItemsWrapper) generalItemsWrapper.innerHTML = '';
            return;
        }

        const selectedOpt = generalSelect.querySelector(`option[value="${selectedVal}"]`) || generalSelect.options[generalSelect.selectedIndex];
        if (!selectedOpt) return;

        const kode = selectedOpt.dataset.kode || '';
        const nama = selectedOpt.dataset.nama || '';
        const maxScore = parseFloat(selectedOpt.dataset.max) || 4.0;
        const count = parseInt(selectedOpt.dataset.count) || 0;

        currentGeneralData = { kode, nama, maxScore, count };

        // Update Info Banner
        const infoKode = document.getElementById('infoKodeSub');
        const infoNama = document.getElementById('infoNamaSub');
        const infoMax = document.getElementById('infoMaxScore');

        if (infoKode) infoKode.textContent = `Sub ${kode}`;
        if (infoNama) infoNama.textContent = nama;
        if (infoMax) infoMax.textContent = maxScore.toFixed(2);

        if (generalInfo) generalInfo.classList.remove('d-none');
        if (generalPlaceholder) generalPlaceholder.classList.add('d-none');
        if (generalRowsContainer) generalRowsContainer.classList.remove('d-none');
        if (generalSubmitBtn) generalSubmitBtn.disabled = false;

        // Reset and add 1 initial row
        if (generalItemsWrapper) {
            generalItemsWrapper.innerHTML = '';
            appendCardToWrapper(generalItemsWrapper, kode, maxScore, count);
        }
    }

    if (generalSelect) {
        generalSelect.addEventListener('change', handleGeneralSelectChange);

        // If TomSelect is initialized on generalSelect
        if (generalSelect.tomselect) {
            generalSelect.tomselect.on('change', handleGeneralSelectChange);
        }
    }

    // Click on "+ Tambah Baris" in General Modal
    document.querySelectorAll('.btn-add-general-row').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!currentGeneralData || !generalItemsWrapper) return;
            appendCardToWrapper(generalItemsWrapper, currentGeneralData.kode, currentGeneralData.maxScore, currentGeneralData.count);
        });
    });

    // 2. Logic for Per-Sub Modals
    document.querySelectorAll('.btn-add-sub-modal-row').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const targetWrapper = document.getElementById(targetId);
            if (!targetWrapper) return;

            const kodeSub = this.dataset.kodeSub || '';
            const maxScore = parseFloat(this.dataset.maxScore) || 4.0;
            const existingCount = parseInt(this.dataset.existingCount) || 0;

            appendCardToWrapper(targetWrapper, kodeSub, maxScore, existingCount);
        });
    });

    // Auto initialize per-sub modal when opened if empty
    document.querySelectorAll('[id^="createSubSubForSubModal"]').forEach(modalEl => {
        modalEl.addEventListener('shown.bs.modal', function() {
            const wrapper = this.querySelector('.sub-sub-items-container');
            if (wrapper && wrapper.querySelectorAll('.sub-sub-item-card').length === 0) {
                const kodeSub = wrapper.dataset.kodeSub || '';
                const maxScore = parseFloat(wrapper.dataset.maxScore) || 4.0;
                const existingCount = parseInt(wrapper.dataset.existingCount) || 0;
                appendCardToWrapper(wrapper, kodeSub, maxScore, existingCount);
            }
        });
    });

    // 3. Global Event Delegation for "Hapus Baris"
    document.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.btn-remove-sub-row');
        if (!removeBtn) return;

        const card = removeBtn.closest('.sub-sub-item-card');
        if (!card) return;

        const container = card.closest('.d-flex.flex-column');
        if (!container) return;

        const allCards = container.querySelectorAll('.sub-sub-item-card');
        if (allCards.length <= 1) {
            alert('Minimal harus ada 1 baris Sub-sub Elemen.');
            return;
        }

        card.remove();
        reindexContainer(container);
    });
});
</script>
@endpush
@endsection
