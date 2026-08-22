@extends('layouts.app')

@section('title', 'Matriks Penilaian Audit SMKP')

@section('content')
@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'auditor';
@endphp
<form action="{{ route($routePrefix . '.audit-sesi.matrix.update', $sesi->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Header Section -->
    <div class="card card-custom p-4 mb-4 border-start border-5 border-primary">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <a href="{{ route($routePrefix . '.audit-sesi.index') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Sesi
                </a>
                <h3 class="fw-bold text-slate-800 mb-1 mt-1">Matriks Penilaian Audit Internal SMKP</h3>
                <p class="text-muted small mb-0">Area: <strong>{{ $sesi->area_audit }}</strong> | Periode: <strong>{{ $sesi->tanggal_mulai->format('d M Y') }} - {{ $sesi->tanggal_selesai->format('d M Y') }}</strong> | Status: <span class="badge bg-warning text-dark text-uppercase">{{ $sesi->status }}</span></p>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-3 px-3">
                    <i class="bi bi-save me-1"></i> Simpan Matriks
                </button>

                <button type="submit" name="save_and_rekap" value="1" class="btn btn-info text-dark rounded-3 px-3 fw-semibold">
                    <i class="bi bi-bar-chart-line me-1"></i> Simpan & Lihat Rekap
                </button>

                <a href="{{ route($routePrefix . '.audit-sesi.rekap', $sesi->id) }}" class="btn btn-outline-secondary rounded-3">
                    Rekap
                </a>
            </div>
        </div>
    </div>

    @if($sesi->status === 'selesai')
        <div class="alert alert-warning rounded-3 mb-4">
            <i class="bi bi-lock-fill me-2"></i> Sesi audit ini telah <strong>difinalisasi (selesai)</strong> dan berada dalam mode hanya-baca (*read-only*).
        </div>
    @endif

    <!-- Accordion Matrix per Elemen -->
    <div class="accordion accordion-custom mb-5" id="matrixAccordion">
        @foreach($elemens as $eIndex => $elemen)
            <div class="accordion-item card card-custom mb-3 border-0 overflow-hidden">
                <h2 class="accordion-header" id="headingElemen{{ $elemen->id }}">
                    <button class="accordion-button {{ $eIndex === 0 ? '' : 'collapsed' }} fw-bold text-slate-800 bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseElemen{{ $elemen->id }}">
                        <div class="d-flex align-items-center justify-content-between w-100 me-3">
                            <div>
                                <span class="badge bg-primary me-2">ELEMEN {{ $elemen->kode_elemen }}</span>
                                <span class="fs-6">{{ $elemen->nama_elemen }}</span>
                            </div>
                            <span class="badge bg-info text-dark font-monospace fs-6 px-3">
                                Bobot: {{ number_format($elemen->bobot, 2) }}%
                            </span>
                        </div>
                    </button>
                </h2>
                <div id="collapseElemen{{ $elemen->id }}" class="accordion-collapse collapse {{ $eIndex === 0 ? 'show' : '' }}" data-bs-parent="#matrixAccordion">
                    <div class="accordion-body p-0">
                        @foreach($elemen->subElemens as $sub)
                            <div class="bg-slate-100 p-3 border-bottom border-top fw-bold text-slate-800 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="badge bg-secondary me-2">Sub {{ $sub->kode_sub }}</span>
                                    <span>{{ $sub->nama_sub }}</span>
                                </div>
                                <small class="text-muted font-monospace">{{ $sub->kriterias->count() }} Kriteria</small>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 100px;">Kode</th>
                                            <th>Pertanyaan / Kriteria & Pedoman Kepdirjen 185</th>
                                            <th style="width: 110px;" class="text-center">Nilai Maks</th>
                                            <th style="width: 130px;" class="text-center">Nilai Audit</th>
                                            <th style="width: 90px;" class="text-center">N/A</th>
                                            <th style="width: 250px;">Catatan Temuan & Bukti Lampiran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sub->kriterias as $kriteria)
                                            @php
                                                $detail = $kriteria->auditDetails->first();
                                                $detailId = $detail ? $detail->id : 0;
                                                $nilaiVal = $detail ? $detail->nilai : 0;
                                                $isNa = $detail ? $detail->is_na : false;
                                                $catatanVal = $detail ? $detail->catatan : '';
                                                $lampiranUrl = $detail ? $detail->lampiran_url : null;
                                            @endphp
                                            <tr class="kriteria-row"
                                                data-kriteria-id="{{ $kriteria->id }}"
                                                data-dependency-id="{{ $kriteria->dependency_id ?? '' }}"
                                                data-nilai-maksimal="{{ (int) $kriteria->nilai_maksimal }}"
                                                data-dependency-note="{{ e($kriteria->dependency_note ?? '') }}">
                                                <td class="fw-bold align-top pt-3">
                                                    <span class="badge bg-dark font-monospace fs-6 py-1 px-2">{{ $kriteria->kode_kriteria }}</span>
                                                </td>
                                                <td class="align-top pt-3">
                                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                                        <div class="fw-semibold text-slate-800">{{ $kriteria->deskripsi }}</div>
                                                        <span class="peringatan-konsistensi badge bg-warning text-dark px-2 py-1 flex-shrink-0" style="display:none;" title="" data-bs-toggle="tooltip"></span>
                                                    </div>

                                                    <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2 py-0" data-bs-toggle="modal" data-bs-target="#rubricModal{{ $kriteria->id }}">
                                                        <i class="bi bi-info-circle me-1"></i> Pedoman Penilaian & Bukti Dokumen
                                                    </button>
                                                </td>
                                                <td class="text-center fw-bold align-top pt-3">
                                                    <span class="badge bg-light text-dark border font-monospace fs-6 px-2 py-1">
                                                        {{ (int) $kriteria->nilai_maksimal }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-top pt-2">
                                                    @php
                                                        $pedomanJson = json_encode([
                                                            '0' => $kriteria->pedoman_nilai_0 ?? '0: Tidak dilaksanakan / tidak ada bukti.',
                                                            '1' => $kriteria->pedoman_nilai_1 ?? '1: Ada draft / belum disahkan.',
                                                            '2' => $kriteria->pedoman_nilai_2 ?? '2: Terdokumentasi / penerapan terbatas.',
                                                            '3' => $kriteria->pedoman_nilai_3 ?? '3: Diterapkan penuh / belum dievaluasi.',
                                                            '4' => $kriteria->pedoman_nilai_4 ?? '4: Diterapkan 100% & dievaluasi berkala.'
                                                        ]);
                                                    @endphp
                                                    <input type="number" step="1" min="0" max="{{ (int) $kriteria->nilai_maksimal }}" 
                                                        name="details[{{ $detailId }}][nilai]" 
                                                        class="form-control text-center fw-bold font-monospace input-score nilai-input" 
                                                        value="{{ (int) $nilaiVal }}" 
                                                        data-kriteria-id="{{ $kriteria->id }}"
                                                        data-pedoman="{{ e($pedomanJson) }}"
                                                        {{ $isNa || $sesi->status === 'selesai' ? 'disabled' : '' }}>
                                                    <div class="pedoman-hint small text-muted text-start mt-1 d-none bg-light p-1 rounded border" style="font-size: 0.75rem;"></div>
                                                </td>
                                                <td class="text-center align-top pt-3">
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input check-na na-checkbox" type="checkbox" 
                                                            name="details[{{ $detailId }}][is_na]" 
                                                            value="1" 
                                                            data-kriteria-id="{{ $kriteria->id }}"
                                                            {{ $isNa ? 'checked' : '' }}
                                                            {{ $sesi->status === 'selesai' ? 'disabled' : '' }}>
                                                    </div>
                                                </td>
                                                <td class="align-top pt-2">
                                                    <textarea name="details[{{ $detailId }}][catatan]" 
                                                        class="form-control form-control-sm mb-2" 
                                                        rows="2" 
                                                        placeholder="Catatan temuan..."
                                                        {{ $sesi->status === 'selesai' ? 'disabled' : '' }}>{{ $catatanVal }}</textarea>

                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file" name="details[{{ $detailId }}][lampiran]" class="form-control form-control-sm" accept="image/*,.pdf" {{ $sesi->status === 'selesai' ? 'disabled' : '' }}>
                                                    </div>

                                                    @if($lampiranUrl)
                                                        <div class="mt-1">
                                                            <a href="{{ $lampiranUrl }}" target="_blank" class="small text-info text-decoration-none fw-semibold">
                                                                <i class="bi bi-paperclip me-1"></i> Lihat Bukti Terunggah
                                                            </a>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Permanent Fixed Bottom Action Footer -->
    @if($sesi->status !== 'selesai')
        <div class="fixed-bottom py-3 shadow-lg border-top" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); z-index: 1040;">
            <div class="container d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center bg-info bg-opacity-20 text-info rounded-circle" style="width: 42px; height: 42px;">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white fs-6">Sesi Audit Berjalan: <span class="text-info fw-extrabold">{{ $sesi->area_audit }}</span></div>
                        <div class="small text-slate-300" style="color: #cbd5e1 !important;">Pastikan seluruh kriteria telah diisi sesuai bukti dokumen fisik sebelum difinalisasi.</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold shadow-sm">
                        <i class="bi bi-save me-1"></i> Simpan Matriks
                    </button>
                    <button type="submit" name="save_and_rekap" value="1" class="btn btn-info text-dark rounded-3 px-4 fw-bold shadow-sm">
                        <i class="bi bi-bar-chart-line me-1"></i> Simpan & Lihat Rekap
                    </button>
                </div>
            </div>
        </div>
        <div style="height: 90px;"></div> <!-- Bottom padding spacer -->
    @endif
</form>

<!-- Modal Rubrik Pedoman Penilaian per Kriteria -->
@foreach($elemens as $elemen)
    @foreach($elemen->subElemens as $sub)
        @foreach($sub->kriterias as $kriteria)
            <div class="modal fade" id="rubricModal{{ $kriteria->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content card-custom border-0">
                        <div class="modal-header border-bottom bg-light">
                            <h5 class="modal-title fw-bold text-slate-800">
                                <i class="bi bi-book-half text-primary me-2"></i>Pedoman Penilaian Kriteria {{ $kriteria->kode_kriteria }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-4">
                            <!-- Criterion Question Box -->
                            <div class="p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3 mb-4">
                                <span class="badge bg-primary me-2">Kriteria {{ $kriteria->kode_kriteria }}</span>
                                <span class="badge bg-dark">Maksimal: {{ number_format($kriteria->nilai_maksimal, 2) }}</span>
                                <h6 class="fw-bold text-slate-800 mt-2 mb-0">{{ $kriteria->deskripsi }}</h6>
                            </div>

                            <!-- Required Document Proofs -->
                            @if($kriteria->persyaratan_dokumen)
                                <div class="mb-4">
                                    <h6 class="fw-bold text-slate-800 mb-2"><i class="bi bi-file-earmark-check-fill text-info me-1"></i>Persyaratan Bukti Dokumen & Fisik (Kepdirjen 185):</h6>
                                    <div class="p-3 bg-light rounded-3 border text-slate-800 small">
                                        {{ $kriteria->persyaratan_dokumen }}
                                    </div>
                                </div>
                            @endif

                            <!-- Rubric 0 to 4 Guidelines -->
                            <h6 class="fw-bold text-slate-800 mb-3"><i class="bi bi-bookmark-star-fill text-warning me-1"></i>Acuan Pemberian Nilai Audit (0 s/d 4):</h6>
                            
                            <div class="d-flex flex-column gap-2">
                                <div class="p-3 border-start border-4 border-danger bg-light rounded-2">
                                    <span class="badge bg-danger mb-1">Nilai 0 (0% - Tidak Ada / Tidak Memenuhi)</span>
                                    <p class="mb-0 small text-slate-800">{{ $kriteria->pedoman_nilai_0 ?? 'Nilai 0: Tidak ada dokumen, tidak dilaksanakan, dan tidak ada bukti fisik pelaksanaan.' }}</p>
                                </div>

                                <div class="p-3 border-start border-4 border-warning bg-light rounded-2">
                                    <span class="badge bg-warning text-dark mb-1">Nilai 1 (25% - Pemenuhan Parsial / Draft)</span>
                                    <p class="mb-0 small text-slate-800">{{ $kriteria->pedoman_nilai_1 ?? 'Nilai 1: Ada draft/wacana tetapi belum disahkan atau belum disosialisasikan.' }}</p>
                                </div>

                                <div class="p-3 border-start border-4 border-info bg-light rounded-2">
                                    <span class="badge bg-info text-dark mb-1">Nilai 2 (50% - Terdokumentasi / Pelaksanaan Terbatas)</span>
                                    <p class="mb-0 small text-slate-800">{{ $kriteria->pedoman_nilai_2 ?? 'Nilai 2: Terdokumentasi secara resmi tetapi penerapan di lapangan masih terbatas/parsial.' }}</p>
                                </div>

                                <div class="p-3 border-start border-4 border-primary bg-light rounded-2">
                                    <span class="badge bg-primary mb-1">Nilai 3 (75% - Diterapkan / Belum Dievaluasi)</span>
                                    <p class="mb-0 small text-slate-800">{{ $kriteria->pedoman_nilai_3 ?? 'Nilai 3: Terdokumentasi dan diterapkan penuh tetapi belum dievaluasi secara berkala.' }}</p>
                                </div>

                                <div class="p-3 border-start border-4 border-success bg-light rounded-2">
                                    <span class="badge bg-success mb-1">Nilai 4 (100% - Sempurna, Diterapkan & Dievaluasi)</span>
                                    <p class="mb-0 small text-slate-800">{{ $kriteria->pedoman_nilai_4 ?? 'Nilai 4: Terdokumentasi resmi, diterapkan 100%, dievaluasi berkala, dan ditindaklanjuti.' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
@endforeach

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkNas = document.querySelectorAll('.check-na');

        checkNas.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const row = this.closest('tr');
                const scoreInput = row.querySelector('.input-score');

                if (this.checked) {
                    scoreInput.value = 0;
                    scoreInput.disabled = true;
                } else {
                    scoreInput.disabled = false;
                }
                cekKonsistensi();
            });
        });

        // Score Input Pedoman Focus & Input Listener
        const scoreInputs = document.querySelectorAll('.input-score');
        scoreInputs.forEach(function(input) {
            function updateHint() {
                const hintDiv = input.parentElement.querySelector('.pedoman-hint');
                if (!hintDiv) return;

                try {
                    const pedoman = JSON.parse(input.getAttribute('data-pedoman') || '{}');
                    const val = input.value !== '' ? String(Math.floor(Number(input.value))) : '';
                    if (pedoman[val]) {
                        hintDiv.textContent = pedoman[val];
                        hintDiv.classList.remove('d-none');
                    } else if (Object.keys(pedoman).length > 0) {
                        hintDiv.textContent = 'Acuan (0-4): ' + (pedoman['0'] || '');
                        hintDiv.classList.remove('d-none');
                    } else {
                        hintDiv.classList.add('d-none');
                    }
                } catch(e) {
                    hintDiv.classList.add('d-none');
                }
            }

            input.addEventListener('focus', updateHint);
            input.addEventListener('input', function() {
                updateHint();
                cekKonsistensi();
            });
            input.addEventListener('change', function() {
                cekKonsistensi();
            });
            input.addEventListener('blur', function() {
                const hintDiv = input.parentElement.querySelector('.pedoman-hint');
                if (hintDiv) hintDiv.classList.add('d-none');
            });
        });

        // Logic Peringatan Konsistensi Antar-Kriteria (Advisory Visual Client-Side)
        function cekKonsistensi() {
            document.querySelectorAll('.kriteria-row[data-dependency-id]').forEach(function(row) {
                const depId = row.dataset.dependencyId;
                if (!depId || depId === 'null' || depId === '') {
                    return;
                }

                const depRow = document.querySelector(`.kriteria-row[data-kriteria-id="${depId}"]`);
                if (!depRow) return;

                const depNaInput = depRow.querySelector('.na-checkbox');
                const depIsNa = depNaInput ? depNaInput.checked : false;

                const thisNaInput = row.querySelector('.na-checkbox');
                const thisIsNa = thisNaInput ? thisNaInput.checked : false;

                const thisNilaiInput = row.querySelector('.nilai-input');
                const thisNilai = parseFloat(thisNilaiInput ? thisNilaiInput.value : 0) || 0;
                const thisMax = parseFloat(row.dataset.nilaiMaksimal) || 0;
                const warningEl = row.querySelector('.peringatan-konsistensi');

                if (!warningEl) return;

                let pesan = null;

                if (depIsNa && !thisIsNa && thisNilai > 0) {
                    pesan = 'Kriteria prasyarat berstatus N/A — periksa apakah penilaian ini masih relevan.';
                } else if (!depIsNa) {
                    const depNilaiInput = depRow.querySelector('.nilai-input');
                    const depNilai = parseFloat(depNilaiInput ? depNilaiInput.value : 0) || 0;
                    const depMax = parseFloat(depRow.dataset.nilaiMaksimal) || 0;

                    if (depMax > 0 && thisMax > 0) {
                        const depPersen = depNilai / depMax;
                        const thisPersen = thisNilai / thisMax;

                        if (depPersen < 0.5 && thisPersen >= 0.75) {
                            pesan = 'Nilai kriteria ini cukup tinggi, tapi kriteria prasyaratnya bernilai rendah (di bawah 50%) — periksa konsistensi.';
                        }
                    }
                }

                if (pesan) {
                    const note = row.dataset.dependencyNote ? (' | Catatan: ' + row.dataset.dependencyNote) : '';
                    warningEl.textContent = '⚠ ' + pesan;
                    warningEl.title = pesan + note;
                    warningEl.style.display = 'inline-block';
                } else {
                    warningEl.style.display = 'none';
                }
            });
        }

        // Jalankan pengecekan konsistensi saat halaman dimuat
        cekKonsistensi();
    });
</script>
@endpush
