@extends('layouts.app')

@section('title', 'Master Kriteria — SMKP Minerba')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-list-check text-success me-2"></i>Kelola Master Kriteria SMKP
        </h2>
        <p class="text-muted mb-0">Kelola kriteria pertanyaan audit, persyaratan dokumen, dan rubrik pedoman nilai (0-4) Kepdirjen 185.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button class="btn btn-success rounded-3 px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createKriteriaModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kriteria Baru
        </button>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 100px;">Kode</th>
                    <th>Deskripsi Kriteria & Persyaratan Dokumen</th>
                    <th>Sub-Elemen</th>
                    <th class="text-center">Nilai Maksimal</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kriterias as $kriteria)
                    <tr>
                        <td>
                            <span class="badge bg-secondary font-monospace fs-6 px-2 py-1">{{ $kriteria->kode_kriteria }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-slate-800 fs-6 mb-1">{{ $kriteria->deskripsi }}</div>
                            @if($kriteria->persyaratan_dokumen)
                                <div class="small text-muted bg-light p-2 rounded border mb-1">
                                    <i class="bi bi-file-earmark-text text-primary me-1"></i><strong>Dokumen Wajib:</strong> {{ Str::limit($kriteria->persyaratan_dokumen, 90) }}
                                </div>
                            @endif
                            @if($kriteria->dependency)
                                <div class="small text-dark bg-warning bg-opacity-10 p-2 rounded border border-warning border-opacity-50">
                                    <i class="bi bi-link-45deg text-warning me-1 fw-bold"></i><strong>Prasyarat:</strong> {{ $kriteria->dependency->kode_kriteria }} - {{ Str::limit($kriteria->dependency->deskripsi, 60) }}
                                    @if($kriteria->dependency_note)
                                        <div class="fst-italic text-muted small mt-1"><i class="bi bi-info-circle me-1"></i>{{ $kriteria->dependency_note }}</div>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted d-block">Sub {{ $kriteria->subElemen ? $kriteria->subElemen->kode_sub : '-' }}</small>
                            <span class="fw-bold text-dark small">{{ $kriteria->subElemen ? $kriteria->subElemen->nama_sub : '-' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-success border font-monospace fs-6 px-3 py-2">
                                {{ (int) $kriteria->nilai_maksimal }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-2" data-bs-toggle="modal" data-bs-target="#editKriteriaModal{{ $kriteria->id }}">
                                    <i class="bi bi-pencil-square me-1"></i> Edit Rubrik
                                </button>
                                <form action="{{ route('admin.kriterias.destroy', $kriteria->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Nonaktifkan Kriteria ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" title="Hapus Kriteria">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data kriteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modals Placed Outside Table -->
@foreach($kriterias as $kriteria)
    <div class="modal fade" id="editKriteriaModal{{ $kriteria->id }}" tabindex="-1" aria-hidden="true">
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
                                <span class="badge bg-secondary font-monospace">Sub {{ $kriteria->subElemen ? $kriteria->subElemen->kode_sub : '-' }}</span>
                                <span class="badge bg-success font-monospace">Nilai Max: {{ (int) $kriteria->nilai_maksimal }}</span>
                            </div>
                            <div class="fw-bold text-slate-800 small">{{ $kriteria->subElemen ? $kriteria->subElemen->nama_sub : '' }}</div>
                            <div class="text-muted small mt-1"><i class="bi bi-card-text me-1"></i>{{ $kriteria->deskripsi }}</div>
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

<!-- Create Modal -->
<div class="modal fade" id="createKriteriaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-custom border-0">
            <form action="{{ route('admin.kriterias.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Tambah Kriteria Baru & Pedoman Nilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Pilih Induk Sub-Elemen <span class="text-danger">*</span></label>
                            <select name="sub_elemen_id" id="createSubElemenSelect" class="form-select select-searchable select-induk-kriteria" required>
                                <option value="">-- Pilih Induk Sub-Elemen --</option>
                                @foreach($subElemens as $sub)
                                    <option value="{{ $sub->id }}" data-max-score="{{ (int) ($sub->nilai_maksimal ?? 4) }}" data-kode="{{ $sub->kode_sub }}" data-nama="{{ $sub->nama_sub }}">
                                        Sub {{ $sub->kode_sub }} - {{ $sub->nama_sub }} (Nilai Max: {{ (int) ($sub->nilai_maksimal ?? 4) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">
                                Setelah memilih induk sub-elemen, isi rubrik pedoman nilai di bawah (jumlah rubrik disesuaikan secara otomatis dengan Nilai Max).
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-bold text-slate-800 mb-2"><i class="bi bi-bookmark-star-fill text-warning me-2"></i>Rubrik Pedoman Penilaian (Acuan Pemberian Nilai)</h6>
                            <div id="dynamicRubrikContainer" class="row g-3">
                                <div class="col-12 text-muted small italic p-3 bg-light rounded text-center">
                                    Silakan pilih Induk Sub-Elemen di atas terlebih dahulu untuk menampilkan rubrik pedoman nilai.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3">Simpan Kriteria</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createModal = document.getElementById('createKriteriaModal');
        if (createModal) {
            const selectEl = createModal.querySelector('#createSubElemenSelect');
            const rubrikContainer = createModal.querySelector('#dynamicRubrikContainer');

            function renderRubrikFields() {
                if (!selectEl || !rubrikContainer) return;

                const selectedOpt = selectEl.options[selectEl.selectedIndex];
                if (!selectedOpt || !selectedOpt.value) {
                    rubrikContainer.innerHTML = `<div class="col-12 text-muted small italic p-3 bg-light rounded text-center">
                        Silakan pilih Induk Sub-Elemen di atas terlebih dahulu untuk menampilkan rubrik pedoman nilai.
                    </div>`;
                    return;
                }

                const maxScoreFloat = parseFloat(selectedOpt.dataset.maxScore) || 4;
                const maxScoreInt = Math.ceil(maxScoreFloat);

                let html = '';
                for (let i = 0; i <= maxScoreInt; i++) {
                    const pct = maxScoreInt > 0 ? Math.round((i / maxScoreInt) * 100) : 0;
                    let colorClass = 'text-danger';
                    if (pct >= 100) colorClass = 'text-success';
                    else if (pct >= 75) colorClass = 'text-primary';
                    else if (pct >= 50) colorClass = 'text-info';
                    else if (pct >= 25) colorClass = 'text-warning';

                    const colSize = (maxScoreInt > 4) ? 'col-md-6' : 'col-12';

                    html += `<div class="${colSize}">
                        <label class="form-label fw-semibold small ${colorClass}">
                            Pedoman Nilai ${i} (${pct}% dari Max ${maxScoreFloat})
                        </label>
                        <textarea name="pedoman_nilai[${i}]" class="form-control" rows="2" placeholder="Acuan pemberian Nilai ${i}..."></textarea>
                    </div>`;
                }

                rubrikContainer.innerHTML = html;
            }

            selectEl.addEventListener('change', renderRubrikFields);
        }
    });
</script>
@endpush
@endsection
