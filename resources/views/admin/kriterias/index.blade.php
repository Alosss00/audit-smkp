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
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-2" data-bs-toggle="modal" data-bs-target="#editKriteriaModal{{ $kriteria->id }}" title="Edit Kriteria, Nilai Maksimal, dan Rubrik">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
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
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Kriteria / Sub-sub Elemen — <span class="badge bg-dark font-monospace">{{ $kriteria->kode_kriteria }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Summary Card Info -->
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-secondary font-monospace">Sub {{ $kriteria->subElemen ? $kriteria->subElemen->kode_sub : '-' }}</span>
                                <span class="badge bg-success font-monospace" id="kriMaxBadge_{{ $kriteria->id }}">Nilai Max: {{ (int) $kriteria->nilai_maksimal }}</span>
                            </div>
                            <div class="fw-bold text-slate-800 small">{{ $kriteria->subElemen ? $kriteria->subElemen->nama_sub : '' }}</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-dark">Kode Kriteria <span class="text-danger">*</span></label>
                                <input type="text" name="kode_kriteria" class="form-control font-monospace" value="{{ $kriteria->kode_kriteria }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-dark">Nilai Maksimal <span class="text-danger">*</span></label>
                                <input type="number" step="1" min="0" max="1000" name="nilai_maksimal" 
                                    class="form-control font-monospace edit-kriteria-nilai-max fw-bold text-primary" 
                                    value="{{ (int) $kriteria->nilai_maksimal }}" 
                                    data-kriteria-id="{{ $kriteria->id }}" 
                                    data-target-container="editKriRubrikContainer_{{ $kriteria->id }}" 
                                    required>
                                <div class="form-text text-muted" style="font-size: 0.75rem;">Mengubah nilai ini akan otomatis menyesuaikan rubrik & total nilai Sub-Elemen.</div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch pb-2">
                                    <input class="form-check-input" type="checkbox" name="is_na" id="edit_is_na_kri_{{ $kriteria->id }}" value="1" {{ $kriteria->is_na ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold small text-dark" for="edit_is_na_kri_{{ $kriteria->id }}">Set N/A Default</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-dark">Deskripsi / Pertanyaan Kriteria <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" class="form-control" rows="2" required>{{ $kriteria->deskripsi }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-secondary">
                                    <i class="bi bi-file-earmark-text text-primary me-1"></i>Dokumen Wajib / Acuan Persyaratan (Opsional)
                                </label>
                                <input type="text" name="persyaratan_dokumen" class="form-control" value="{{ $kriteria->persyaratan_dokumen }}" placeholder="Contoh: SOP, SK KTT, Buku Catatan...">
                            </div>
                        </div>

                        <div class="mb-2">
                            <h6 class="fw-bold text-slate-800 small mb-2"><i class="bi bi-bookmark-star-fill text-warning me-1"></i>Rubrik Pedoman Penilaian</h6>
                        </div>

                        @php
                            $maxValEdit = (int) ceil($kriteria->nilai_maksimal);
                            $pedomanArr = $kriteria->pedoman_array ?? [];
                        @endphp
                        <div class="row g-3" id="editKriRubrikContainer_{{ $kriteria->id }}">
                            @for($i = 0; $i <= $maxValEdit; $i++)
                                @php
                                    $pctEdit = $maxValEdit > 0 ? round(($i / $maxValEdit) * 100) : 0;
                                    $colSizeEdit = ($maxValEdit > 4) ? 'col-md-6' : 'col-12';
                                @endphp
                                <div class="{{ $colSizeEdit }}">
                                    <label class="form-label fw-semibold small text-dark">Pedoman Nilai {{ $i }} ({{ $pctEdit }}% dari Max {{ (int) $kriteria->nilai_maksimal }})</label>
                                    <textarea name="pedoman_nilai[{{ $i }}]" class="form-control" rows="2" placeholder="Acuan pemberian Nilai {{ $i }}...">{{ $pedomanArr[(string)$i] ?? ($kriteria->{"pedoman_nilai_$i"} ?? '') }}</textarea>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3"><i class="bi bi-check-lg me-1"></i>Simpan Perubahan Kriteria</button>
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
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Pilih Induk Sub-Elemen <span class="text-danger">*</span></label>
                            <select name="sub_elemen_id" id="createSubElemenSelect" class="form-select select-searchable select-induk-kriteria" required>
                                <option value="">-- Pilih Induk Sub-Elemen --</option>
                                @foreach($subElemens as $sub)
                                    <option value="{{ $sub->id }}" data-max-score="{{ (int) ($sub->nilai_maksimal ?? 4) }}" data-kode="{{ $sub->kode_sub }}" data-nama="{{ $sub->nama_sub }}">
                                        Sub {{ $sub->kode_sub }} - {{ $sub->nama_sub }} (Nilai Max: {{ (int) ($sub->nilai_maksimal ?? 4) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Nilai Maksimal Kriteria <span class="text-danger">*</span></label>
                            <input type="number" step="1" min="0" max="1000" name="nilai_maksimal" id="createNilaiMaksimalInput" class="form-control font-monospace fw-bold text-primary" value="4" required>
                        </div>
                        <div class="col-12">
                            <div class="form-text text-muted">
                                Setelah memilih induk sub-elemen dan menentukan Nilai Maksimal, isi rubrik pedoman nilai di bawah.
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
<script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        const createModal = document.getElementById('createKriteriaModal');
        if (createModal) {
            const selectEl = createModal.querySelector('#createSubElemenSelect');
            const maxInput = createModal.querySelector('#createNilaiMaksimalInput');
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

                const maxScoreInt = Math.max(0, parseInt(maxInput ? maxInput.value : selectedOpt.dataset.maxScore, 10) || 4);

                // Read current values
                const currentValues = {};
                rubrikContainer.querySelectorAll('textarea[name^="pedoman_nilai["]').forEach(textarea => {
                    const match = textarea.getAttribute('name').match(/pedoman_nilai\[(\d+)\]/);
                    if (match) {
                        currentValues[match[1]] = textarea.value;
                    }
                });

                let html = '';
                for (let i = 0; i <= maxScoreInt; i++) {
                    const pct = maxScoreInt > 0 ? Math.round((i / maxScoreInt) * 100) : 0;
                    let colorClass = 'text-danger';
                    if (pct >= 100) colorClass = 'text-success';
                    else if (pct >= 75) colorClass = 'text-primary';
                    else if (pct >= 50) colorClass = 'text-info';
                    else if (pct >= 25) colorClass = 'text-warning';

                    const colSize = (maxScoreInt > 4) ? 'col-md-6' : 'col-12';
                    const val = currentValues[i] || '';

                    html += `<div class="${colSize}">
                        <label class="form-label fw-semibold small ${colorClass}">
                            Pedoman Nilai ${i} (${pct}% dari Max ${maxScoreInt})
                        </label>
                        <textarea name="pedoman_nilai[${i}]" class="form-control" rows="2" placeholder="Acuan pemberian Nilai ${i}...">${val}</textarea>
                    </div>`;
                }

                rubrikContainer.innerHTML = html;
            }

            selectEl.addEventListener('change', function() {
                const selectedOpt = selectEl.options[selectEl.selectedIndex];
                if (selectedOpt && selectedOpt.value && maxInput) {
                    maxInput.value = parseInt(selectedOpt.dataset.maxScore || 4, 10);
                }
                renderRubrikFields();
            });

            if (maxInput) {
                maxInput.addEventListener('input', renderRubrikFields);
            }
        }

        // Edit Kriteria Modals Dynamic Rubric Updater
        document.querySelectorAll('.edit-kriteria-nilai-max').forEach(input => {
            input.addEventListener('input', function() {
                const targetContainerId = this.dataset.targetContainer;
                const container = document.getElementById(targetContainerId);
                if (!container) return;

                const maxScoreInt = Math.max(0, parseInt(this.value, 10) || 0);

                // Read current values
                const currentValues = {};
                container.querySelectorAll('textarea[name^="pedoman_nilai["]').forEach(textarea => {
                    const match = textarea.getAttribute('name').match(/pedoman_nilai\[(\d+)\]/);
                    if (match) {
                        currentValues[match[1]] = textarea.value;
                    }
                });

                let html = '';
                for (let i = 0; i <= maxScoreInt; i++) {
                    const pct = maxScoreInt > 0 ? Math.round((i / maxScoreInt) * 100) : 0;
                    let colorClass = 'text-danger';
                    if (pct >= 100) colorClass = 'text-success';
                    else if (pct >= 75) colorClass = 'text-primary';
                    else if (pct >= 50) colorClass = 'text-info';
                    else if (pct >= 25) colorClass = 'text-warning';

                    const colSize = (maxScoreInt > 4) ? 'col-md-6' : 'col-12';
                    const val = currentValues[i] || '';

                    html += `
                        <div class="${colSize}">
                            <label class="form-label fw-semibold small text-dark ${colorClass}">
                                Pedoman Nilai ${i} (${pct}% dari Max ${maxScoreInt})
                            </label>
                            <textarea name="pedoman_nilai[${i}]" class="form-control" rows="2" placeholder="Acuan pemberian Nilai ${i}...">${val}</textarea>
                        </div>
                    `;
                }

                container.innerHTML = html;
                const kriteriaId = this.dataset.kriteriaId;
                const maxBadge = document.getElementById('kriMaxBadge_' + kriteriaId);
                if (maxBadge) {
                    maxBadge.textContent = 'Nilai Max: ' + maxScoreInt;
                }
            });
        });
    });
</script>
@endpush
@endsection
