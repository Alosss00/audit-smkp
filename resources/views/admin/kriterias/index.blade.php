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
                                {{ number_format($kriteria->nilai_maksimal, 2) }}
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
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">Edit Kriteria & Pedoman Nilai {{ $kriteria->kode_kriteria }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Induk Sub-Elemen</label>
                                <select name="sub_elemen_id" class="form-select" required>
                                    @foreach($subElemens as $sub)
                                        <option value="{{ $sub->id }}" {{ $kriteria->sub_elemen_id == $sub->id ? 'selected' : '' }}>
                                            Sub {{ $sub->kode_sub }} - {{ $sub->nama_sub }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Kode Kriteria</label>
                                <input type="text" name="kode_kriteria" class="form-control" value="{{ $kriteria->kode_kriteria }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Nilai Maksimal</label>
                                <input type="number" step="0.01" min="0.01" max="100" name="nilai_maksimal" class="form-control" value="{{ $kriteria->nilai_maksimal }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Deskripsi Pertanyaan Kriteria</label>
                                <textarea name="deskripsi" class="form-control" rows="2" required>{{ $kriteria->deskripsi }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-primary">Persyaratan Dokumen & Bukti Fisik Kepdirjen 185</label>
                                <textarea name="persyaratan_dokumen" class="form-control" rows="2" placeholder="Dokumen, SOP, SK, atau rekaman pelaksanaan yang dipersyaratkan...">{{ $kriteria->persyaratan_dokumen }}</textarea>
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                                <h6 class="fw-bold text-slate-800 small mb-2"><i class="bi bi-link-45deg text-warning me-1"></i>Hubungan Prasyarat Antar-Kriteria (Opsional)</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Kriteria Prasyarat (Opsional)</label>
                                <select name="dependency_id" class="form-select">
                                    <option value="">-- Tanpa Kriteria Prasyarat --</option>
                                    @foreach($kriterias as $other)
                                        @if($other->id !== $kriteria->id)
                                            <option value="{{ $other->id }}" {{ $kriteria->dependency_id == $other->id ? 'selected' : '' }}>
                                                {{ $other->kode_kriteria }} - {{ Str::limit($other->deskripsi, 50) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Catatan Hubungan Prasyarat</label>
                                <input type="text" name="dependency_note" class="form-control" value="{{ $kriteria->dependency_note }}" placeholder="Penjelasan relasi (mis. Nilai fisik unit bergantung pada dokumen...)">
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                                <h6 class="fw-bold text-slate-800 small mb-2"><i class="bi bi-bookmark-star-fill text-warning me-1"></i>Rubrik Pedoman Penilaian (Nilai 0 s/d 4)</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-danger">Pedoman Nilai 0 (0%)</label>
                                <textarea name="pedoman_nilai_0" class="form-control" rows="2">{{ $kriteria->pedoman_nilai_0 }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-warning">Pedoman Nilai 1 (25%)</label>
                                <textarea name="pedoman_nilai_1" class="form-control" rows="2">{{ $kriteria->pedoman_nilai_1 }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-info">Pedoman Nilai 2 (50%)</label>
                                <textarea name="pedoman_nilai_2" class="form-control" rows="2">{{ $kriteria->pedoman_nilai_2 }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-primary">Pedoman Nilai 3 (75%)</label>
                                <textarea name="pedoman_nilai_3" class="form-control" rows="2">{{ $kriteria->pedoman_nilai_3 }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-success">Pedoman Nilai 4 (100% Sempurna)</label>
                                <textarea name="pedoman_nilai_4" class="form-control" rows="2">{{ $kriteria->pedoman_nilai_4 }}</textarea>
                            </div>
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
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Induk Sub / Sub-sub Elemen <span class="text-danger">*</span></label>
                            <select name="sub_elemen_id" class="form-select select-searchable select-induk-kriteria" required>
                                <option value="">-- Pilih Induk Sub atau Sub-sub Elemen --</option>
                                <optgroup label="DAFTAR SUB-ELEMEN (Sub Kriteria)">
                                    @foreach($subElemens as $sub)
                                        <option value="{{ $sub->id }}" data-max-score="{{ $sub->nilai_maksimal ?? 4.00 }}">
                                            Sub {{ $sub->kode_sub }} - {{ $sub->nama_sub }} (Max: {{ number_format($sub->nilai_maksimal ?? 4, 2) }})
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="DAFTAR SUB-SUB ELEMEN (Kriteria)">
                                    @foreach($kriterias as $otherK)
                                        <option value="{{ $otherK->sub_elemen_id }}" data-max-score="{{ $otherK->nilai_maksimal ?? 4.00 }}">
                                            Kriteria {{ $otherK->kode_kriteria }} - {{ Str::limit($otherK->deskripsi, 45) }} (Max: {{ number_format($otherK->nilai_maksimal, 2) }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Kode Kriteria <span class="text-danger">*</span></label>
                            <input type="text" name="kode_kriteria" class="form-control font-monospace" placeholder="Contoh: I.1.1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Nilai Maksimal <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" max="100" name="nilai_maksimal" class="form-control input-nilai-max" value="4.00" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Deskripsi Pertanyaan Kriteria <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Tuliskan kriteria penilaian..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-primary">Persyaratan Dokumen & Bukti Fisik</label>
                            <textarea name="persyaratan_dokumen" class="form-control" rows="2" placeholder="Sebutkan dokumen wajib yang harus diverifikasi..."></textarea>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-bold text-slate-800 small mb-2"><i class="bi bi-link-45deg text-warning me-1"></i>Hubungan Prasyarat Antar-Kriteria (Opsional)</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Kriteria Prasyarat (Opsional)</label>
                            <select name="dependency_id" class="form-select select-searchable">
                                <option value="">-- Tanpa Kriteria Prasyarat --</option>
                                @foreach($kriterias as $other)
                                    <option value="{{ $other->id }}">
                                        {{ $other->kode_kriteria }} - {{ Str::limit($other->deskripsi, 50) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Catatan Hubungan Prasyarat</label>
                            <input type="text" name="dependency_note" class="form-control" placeholder="Penjelasan relasi (mis. Nilai fisik unit bergantung pada dokumen...)">
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-bold text-slate-800 small mb-2"><i class="bi bi-bookmark-star-fill text-warning me-1"></i>Rubrik Pedoman Penilaian (Nilai 0 s/d 4)</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-danger label-rubrik-0">Pedoman Nilai 0 (0%)</label>
                            <textarea name="pedoman_nilai_0" class="form-control" rows="2" placeholder="Acuan pemberian Nilai 0..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-warning label-rubrik-1">Pedoman Nilai 1 (25%)</label>
                            <textarea name="pedoman_nilai_1" class="form-control" rows="2" placeholder="Acuan pemberian Nilai 1..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-info label-rubrik-2">Pedoman Nilai 2 (50%)</label>
                            <textarea name="pedoman_nilai_2" class="form-control" rows="2" placeholder="Acuan pemberian Nilai 2..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-primary label-rubrik-3">Pedoman Nilai 3 (75%)</label>
                            <textarea name="pedoman_nilai_3" class="form-control" rows="2" placeholder="Acuan pemberian Nilai 3..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-success label-rubrik-4">Pedoman Nilai 4 (100%)</label>
                            <textarea name="pedoman_nilai_4" class="form-control" rows="2" placeholder="Acuan pemberian Nilai 4..."></textarea>
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
        document.querySelectorAll('.modal').forEach(function(modal) {
            const indukSelect = modal.querySelector('.select-induk-kriteria');
            const nilaiMaxInput = modal.querySelector('.input-nilai-max') || modal.querySelector('input[name="nilai_maksimal"]');
            const rubrikLabels = {
                0: modal.querySelector('.label-rubrik-0'),
                1: modal.querySelector('.label-rubrik-1'),
                2: modal.querySelector('.label-rubrik-2'),
                3: modal.querySelector('.label-rubrik-3'),
                4: modal.querySelector('.label-rubrik-4')
            };

            function updateRubrikLabels() {
                const maxVal = parseFloat(nilaiMaxInput ? nilaiMaxInput.value : 4) || 4;
                const percentages = { 0: 0, 1: 0.25, 2: 0.50, 3: 0.75, 4: 1.00 };
                const descs = {
                    0: 'Tidak Memenuhi',
                    1: 'Draft / Wacana',
                    2: 'Pelaksanaan Terbatas',
                    3: 'Diterapkan Penuh',
                    4: 'Sempurna & Dievaluasi'
                };

                for (let i = 0; i <= 4; i++) {
                    if (rubrikLabels[i]) {
                        const scoreCalc = (maxVal * percentages[i]).toFixed(2);
                        rubrikLabels[i].innerHTML = `Pedoman Nilai ${i} (${percentages[i]*100}% = ${scoreCalc}) — <small class="fw-normal">${descs[i]}</small>`;
                    }
                }
            }

            if (indukSelect && nilaiMaxInput) {
                indukSelect.addEventListener('change', function() {
                    const selectedOpt = this.options[this.selectedIndex];
                    if (selectedOpt && selectedOpt.dataset.maxScore) {
                        nilaiMaxInput.value = parseFloat(selectedOpt.dataset.maxScore).toFixed(2);
                        updateRubrikLabels();
                    }
                });
            }

            if (nilaiMaxInput) {
                nilaiMaxInput.addEventListener('input', updateRubrikLabels);
                nilaiMaxInput.addEventListener('change', updateRubrikLabels);
            }

            updateRubrikLabels();
        });
    });
</script>
@endpush
@endsection
