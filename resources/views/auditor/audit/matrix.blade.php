@extends('layouts.app')

@section('title', 'Matriks Penilaian Audit SMKP')

@section('content')
<form action="{{ route('admin.audit-sesi.matrix.update', $sesi->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Header Section -->
    <div class="card card-custom p-4 mb-4 border-start border-5 border-primary">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <a href="{{ route('admin.audit-sesi.index') }}" class="text-decoration-none text-muted small">
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

                <a href="{{ route('admin.audit-sesi.rekap', $sesi->id) }}" class="btn btn-outline-secondary rounded-3">
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
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        <th style="width: 100px;">Kode</th>
                                        <th>Pertanyaan / Kriteria & Pedoman Kepdirjen 185</th>
                                        <th style="width: 100px;" class="text-center">Nilai Maks</th>
                                        <th style="width: 220px;" class="text-center">Nilai Audit</th>
                                        <th style="width: 90px;" class="text-center">N/A</th>
                                        <th style="width: 280px;">Catatan Temuan & Bukti Lampiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($elemen->subElemens as $sub)
                                        @php
                                            $isPenilaianLangsung = ($sub->kriterias->count() === 1 && $sub->kriterias->first()->kode_kriteria === $sub->kode_sub);
                                        @endphp

                                        @if(!$isPenilaianLangsung)
                                            <tr class="table-light">
                                                <td colspan="6" class="py-2.5 px-3 fw-bold text-slate-800 bg-slate-100 border-top border-bottom shadow-none">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <span class="badge bg-secondary me-2">Sub {{ $sub->kode_sub }}</span>
                                                            <span>{{ $sub->nama_sub }}</span>
                                                        </div>
                                                        <span class="badge bg-white text-dark border font-monospace">{{ $sub->kriterias->count() }} Kriteria</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif

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
                                                <td class="text-center align-top pt-2" style="width: 220px;">
                                                    @php
                                                        $pedomanJson = json_encode($kriteria->pedoman_array);
                                                        $maxScore = (int) $kriteria->nilai_maksimal;
                                                    @endphp

                                                    <!-- Quick Score Selection Buttons (0-4) -->
                                                    <div class="btn-group btn-group-sm mb-1 w-100 score-btn-group" role="group">
                                                        @for($s = 0; $s <= $maxScore; $s++)
                                                            <button type="button" 
                                                                class="btn btn-outline-secondary btn-score-quick py-0 px-1 {{ (int)$nilaiVal === $s && !$isNa ? 'active btn-primary text-white fw-bold' : '' }}" 
                                                                data-score="{{ $s }}"
                                                                {{ $isNa || $sesi->status === 'selesai' ? 'disabled' : '' }}>
                                                                {{ $s }}
                                                            </button>
                                                        @endfor
                                                    </div>

                                                    <!-- Score Input -->
                                                    <input type="number" step="1" min="0" max="{{ $maxScore }}" 
                                                        name="details[{{ $detailId }}][nilai]" 
                                                        class="form-control text-center fw-bold font-monospace input-score nilai-input" 
                                                        value="{{ (int) $nilaiVal }}" 
                                                        data-kriteria-id="{{ $kriteria->id }}"
                                                        data-pedoman="{{ $pedomanJson }}"
                                                        data-dokumen="{{ $kriteria->persyaratan_dokumen ?? '' }}"
                                                        {{ $isNa || $sesi->status === 'selesai' ? 'disabled' : '' }}>

                                                    <!-- Live Pedoman & Bukti Dokumen Card -->
                                                    <div class="pedoman-hint mt-2 p-2 rounded-3 border text-start shadow-sm d-none" style="font-size: 0.78rem;"></div>
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
                                                <td class="align-top pt-2" style="min-width: 270px;">
                                                    @php
                                                        $catatanList = $detail ? $detail->catatan_array : [];
                                                        if (empty($catatanList)) {
                                                            $catatanList = [''];
                                                        }
                                                    @endphp

                                                    <!-- List Catatan Temuan Dinamis -->
                                                    <div class="catatan-wrapper" id="catatanWrapper{{ $detailId }}">
                                                        @foreach($catatanList as $cIdx => $cItem)
                                                            <div class="catatan-item mb-2 p-2 bg-light rounded-2 border position-relative">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <span class="small fw-bold text-slate-700 font-monospace catatan-label">
                                                                        <i class="bi bi-journal-text me-1 text-primary"></i>Temuan #{{ $cIdx + 1 }}
                                                                    </span>
                                                                    @if($sesi->status !== 'selesai')
                                                                        <button type="button" class="btn btn-xs text-danger p-0 border-0 btn-remove-catatan" title="Hapus catatan ini" style="{{ count($catatanList) <= 1 ? 'display: none;' : '' }}">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                                <textarea name="details[{{ $detailId }}][catatans][]" 
                                                                    class="form-control form-control-sm bg-white" 
                                                                    rows="2" 
                                                                    placeholder="Tulis uraian temuan / ketidaksesuaian..."
                                                                    {{ $sesi->status === 'selesai' ? 'disabled' : '' }}>{{ $cItem }}</textarea>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    @if($sesi->status !== 'selesai')
                                                        <div class="mb-2">
                                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 btn-add-catatan" data-detail-id="{{ $detailId }}">
                                                                <i class="bi bi-plus-circle me-1"></i> Tambah Catatan Temuan
                                                            </button>
                                                        </div>
                                                    @endif

                                                    <!-- Bukti Lampiran Dokumen/Foto -->
                                                    <div class="p-2 border rounded-2 bg-light mt-2">
                                                        <label class="form-label small fw-bold text-slate-700 mb-1 d-block" style="font-size: 0.75rem;">
                                                            <i class="bi bi-paperclip text-info me-1"></i>Bukti Lampiran (Foto/PDF):
                                                        </label>

                                                        @if($detail && !empty($detail->lampiran_urls))
                                                            <div class="d-flex flex-column gap-1 mb-2">
                                                                @foreach($detail->lampiran_urls as $lFile)
                                                                    <div class="d-flex align-items-center justify-content-between bg-white px-2 py-1 rounded border small">
                                                                        <a href="{{ $lFile['url'] }}" target="_blank" class="text-info text-decoration-none fw-semibold text-truncate me-2" style="max-width: 170px;" title="{{ $lFile['name'] }}">
                                                                            <i class="bi bi-file-earmark-arrow-down me-1"></i>{{ $lFile['name'] }}
                                                                        </a>
                                                                        @if($sesi->status !== 'selesai')
                                                                            <label class="text-danger small m-0 cursor-pointer" title="Centang untuk menghapus file ini saat simpan" style="font-size: 0.7rem;">
                                                                                <input type="checkbox" name="details[{{ $detailId }}][hapus_lampiran][]" value="{{ $lFile['path'] }}" class="form-check-input me-1"> Hapus
                                                                            </label>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        @if($sesi->status !== 'selesai')
                                                            <input type="file" name="details[{{ $detailId }}][lampirans][]" class="form-control form-control-sm" accept="image/*,.pdf" multiple>
                                                            <div class="text-muted" style="font-size: 0.7rem;">Bisa pilih lebih dari 1 file lampiran</div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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

                            @php
                                $pedomanArr = $kriteria->pedoman_array ?? [];
                                $maxVal = !empty($pedomanArr) ? max(array_map('intval', array_keys($pedomanArr))) : (int) ceil($kriteria->nilai_maksimal ?? 4);
                                if ($maxVal <= 0) $maxVal = 4;
                            @endphp

                            <!-- Rubric Guidelines -->
                            <h6 class="fw-bold text-slate-800 mb-3"><i class="bi bi-bookmark-star-fill text-warning me-1"></i>Acuan Pemberian Nilai Audit (0 s/d {{ $maxVal }}):</h6>
                            
                            <div class="d-flex flex-column gap-3">
                                @foreach($pedomanArr as $skor => $deskripsiRubrik)
                                    @php
                                        $numSkor = (int)$skor;
                                        $pct = $maxVal > 0 ? round(($numSkor / $maxVal) * 100) : 0;
                                        $borderClass = 'border-danger';
                                        $badgeClass = 'bg-danger';
                                        $statusText = 'Tidak Ada / Tidak Memenuhi';

                                        if ($numSkor == $maxVal || $pct >= 100) {
                                            $borderClass = 'border-success';
                                            $badgeClass = 'bg-success';
                                            $statusText = 'Pemenuhan 100% / Sesuai Standar';
                                        } elseif ($pct >= 75) {
                                            $borderClass = 'border-primary';
                                            $badgeClass = 'bg-primary';
                                            $statusText = 'Penerapan Baik / Hampir Lengkap';
                                        } elseif ($pct >= 50) {
                                            $borderClass = 'border-info';
                                            $badgeClass = 'bg-info text-dark';
                                            $statusText = 'Terdokumentasi / Pelaksanaan Terbatas';
                                        } elseif ($pct > 0) {
                                            $borderClass = 'border-warning';
                                            $badgeClass = 'bg-warning text-dark';
                                            $statusText = 'Pemenuhan Parsial / Draft';
                                        }
                                    @endphp
                                    <div class="p-3 border-start border-4 {{ $borderClass }} bg-light rounded-3 shadow-none">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge {{ $badgeClass }} px-2 py-1">Nilai {{ $skor }} ({{ $pct }}% — {{ $statusText }})</span>
                                        </div>
                                        <div class="text-slate-800 small" style="white-space: pre-line; line-height: 1.6;">{{ $deskripsiRubrik }}</div>
                                    </div>
                                @endforeach
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
<script nonce="{{ $cspNonce ?? '' }}">
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

                scoreInput.dispatchEvent(new Event('input'));
                cekKonsistensi();
            });
        });

        // Score Input Pedoman & Document Proof Live Preview Listener
        const scoreInputs = document.querySelectorAll('.input-score');
        scoreInputs.forEach(function(input) {
            const row = input.closest('tr');
            const btnGroup = row.querySelector('.score-btn-group');

            function updateHint() {
                const hintDiv = input.parentElement.querySelector('.pedoman-hint');
                if (!hintDiv) return;

                const isNa = input.disabled && row.querySelector('.na-checkbox')?.checked;

                if (isNa) {
                    hintDiv.innerHTML = `<span class="badge bg-secondary mb-1">N/A (Tidak Berlaku)</span><div class="small text-muted">Kriteria ini tidak dinilai pada sesi ini.</div>`;
                    hintDiv.className = 'pedoman-hint mt-2 p-2 rounded-3 border bg-light text-start shadow-sm';
                    hintDiv.classList.remove('d-none');

                    if (btnGroup) {
                        btnGroup.querySelectorAll('.btn-score-quick').forEach(b => {
                            b.classList.remove('active', 'btn-primary', 'text-white');
                            b.classList.add('btn-outline-secondary');
                        });
                    }
                    return;
                }

                const rawVal = input.value !== '' ? String(Math.floor(Number(input.value))) : '';

                if (rawVal === '') {
                    hintDiv.classList.add('d-none');
                    if (btnGroup) {
                        btnGroup.querySelectorAll('.btn-score-quick').forEach(b => {
                            b.classList.remove('active', 'btn-primary', 'text-white');
                            b.classList.add('btn-outline-secondary');
                        });
                    }
                    return;
                }

                // Update quick buttons active state
                if (btnGroup) {
                    btnGroup.querySelectorAll('.btn-score-quick').forEach(b => {
                        if (b.dataset.score === rawVal) {
                            b.classList.add('active', 'btn-primary', 'text-white', 'fw-bold');
                            b.classList.remove('btn-outline-secondary');
                        } else {
                            b.classList.remove('active', 'btn-primary', 'text-white', 'fw-bold');
                            b.classList.add('btn-outline-secondary');
                        }
                    });
                }

                let pedoman = {};
                try {
                    const rawPedoman = input.getAttribute('data-pedoman') || '{}';
                    if (rawPedoman.startsWith('{') || rawPedoman.startsWith('[')) {
                        pedoman = JSON.parse(rawPedoman);
                    } else {
                        const tempEl = document.createElement('textarea');
                        tempEl.innerHTML = rawPedoman;
                        pedoman = JSON.parse(tempEl.value);
                    }
                } catch(e) {
                    console.error('Pedoman parse error:', e);
                }

                const dokumen = input.getAttribute('data-dokumen') || '';
                const maxScore = parseFloat(input.getAttribute('max') || '4') || 4;
                const numScore = parseFloat(rawVal) || 0;
                const pct = maxScore > 0 ? Math.round((numScore / maxScore) * 100) : 0;

                let badgeClass = 'bg-danger text-white';
                let statusLabel = 'Tidak Ada / Tidak Memenuhi';

                if (numScore >= maxScore || pct >= 100) {
                    badgeClass = 'bg-success text-white';
                    statusLabel = 'Pemenuhan 100% / Sesuai Standar';
                } else if (pct >= 75) {
                    badgeClass = 'bg-primary text-white';
                    statusLabel = 'Penerapan Baik / Hampir Lengkap';
                } else if (pct >= 50) {
                    badgeClass = 'bg-info text-dark';
                    statusLabel = 'Terdokumentasi / Pelaksanaan Terbatas';
                } else if (pct > 0) {
                    badgeClass = 'bg-warning text-dark';
                    statusLabel = 'Pemenuhan Parsial / Draft';
                }

                const textDesc = pedoman[rawVal] 
                    ?? (Array.isArray(pedoman) ? pedoman[parseInt(rawVal, 10)] : null) 
                    ?? (pedoman[String(rawVal)] ?? ('Acuan Nilai ' + rawVal));

                let html = `<div class="d-flex align-items-center justify-content-between mb-1">`;
                html += `<span class="badge ${badgeClass} px-2 py-1">Nilai ${rawVal} (${pct}% — ${statusLabel})</span>`;
                html += `</div>`;
                html += `<div class="text-slate-800" style="line-height: 1.45; font-size: 0.78rem; white-space: pre-line;">${textDesc}</div>`;

                if (dokumen.trim() !== '') {
                    html += `<div class="border-top pt-1 mt-1 text-slate-600" style="font-size: 0.72rem;">`;
                    html += `<i class="bi bi-file-earmark-check-fill text-primary me-1"></i><strong>Bukti Dokumen Wajib:</strong> ${dokumen}`;
                    html += `</div>`;
                }

                hintDiv.innerHTML = html;
                hintDiv.className = 'pedoman-hint mt-2 p-2 rounded-3 border bg-white text-start shadow-sm border-primary border-opacity-25';
                hintDiv.classList.remove('d-none');
            }

            // Quick Score Button Click Handler
            if (btnGroup) {
                btnGroup.querySelectorAll('.btn-score-quick').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        if (input.disabled) return;
                        input.value = this.dataset.score;
                        updateHint();
                        cekKonsistensi();
                    });
                });
            }

            input.addEventListener('focus', function() { updateHint(); });
            input.addEventListener('input', function() {
                updateHint();
                cekKonsistensi();
            });
            input.addEventListener('change', function() {
                updateHint();
                cekKonsistensi();
            });

            // Initial update on page load
            updateHint();
        });

        // Dynamic Add / Remove Catatan Temuan
        document.addEventListener('click', function(e) {
            // Add Catatan Button
            const addBtn = e.target.closest('.btn-add-catatan');
            if (addBtn) {
                const detailId = addBtn.dataset.detailId;
                const wrapper = document.getElementById('catatanWrapper' + detailId);
                if (wrapper) {
                    const currentItems = wrapper.querySelectorAll('.catatan-item');
                    const nextNum = currentItems.length + 1;
                    
                    const newItem = document.createElement('div');
                    newItem.className = 'catatan-item mb-2 p-2 bg-light rounded-2 border position-relative';
                    newItem.innerHTML = `
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="small fw-bold text-slate-700 font-monospace catatan-label">
                                <i class="bi bi-journal-text me-1 text-primary"></i>Temuan #${nextNum}
                            </span>
                            <button type="button" class="btn btn-xs text-danger p-0 border-0 btn-remove-catatan" title="Hapus catatan ini">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <textarea name="details[${detailId}][catatans][]" 
                            class="form-control form-control-sm bg-white" 
                            rows="2" 
                            placeholder="Tulis uraian temuan / ketidaksesuaian..."></textarea>
                    `;
                    wrapper.appendChild(newItem);

                    // Show remove buttons on all items in this wrapper if > 1
                    wrapper.querySelectorAll('.btn-remove-catatan').forEach(btn => btn.style.display = 'inline-block');
                    
                    const textarea = newItem.querySelector('textarea');
                    if (textarea) textarea.focus();
                }
            }

            // Remove Catatan Button
            const removeBtn = e.target.closest('.btn-remove-catatan');
            if (removeBtn) {
                const item = removeBtn.closest('.catatan-item');
                const wrapper = item.closest('.catatan-wrapper');
                if (item && wrapper) {
                    item.remove();
                    // Re-index remaining labels
                    const remainingItems = wrapper.querySelectorAll('.catatan-item');
                    remainingItems.forEach((it, idx) => {
                        const label = it.querySelector('.catatan-label');
                        if (label) {
                            label.innerHTML = `<i class="bi bi-journal-text me-1 text-primary"></i>Temuan #${idx + 1}`;
                        }
                    });
                    if (remainingItems.length <= 1) {
                        remainingItems.forEach(it => {
                            const btn = it.querySelector('.btn-remove-catatan');
                            if (btn) btn.style.display = 'none';
                        });
                    }
                }
            }
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
