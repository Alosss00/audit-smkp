@php
    $groupedBySub = $items->groupBy(fn($p) => $p->auditDetail->kriteria->sub_elemen_id ?? 0);
    $uniquePrefix = 'reportPica_' . uniqid();
@endphp

<div class="accordion mb-3" id="accordion{{ $uniquePrefix }}">
    @php $subItemIndex = 0; @endphp
    @foreach($groupedBySub as $subElemenId => $subPicas)
        @php
            $subItemIndex++;
            $firstPica = $subPicas->first();
            $kriFirst = $firstPica->auditDetail->kriteria ?? null;
            $subElemen = $kriFirst->subElemen ?? null;
            $subKode = $subElemen->kode_sub ?? ($kriFirst->kode_kriteria ?? '-');
            $subNama = $subElemen->nama_sub ?? ($kriFirst->deskripsi ?? '-');
            
            $hasSubSub = $subPicas->count() > 1 
                || ($subElemen && $subElemen->kriterias && $subElemen->kriterias->count() > 1) 
                || ($kriFirst && $kriFirst->kode_kriteria !== $subKode);
                
            $hasOpen = $subPicas->contains(fn($p) => $p->status === 'open');
            $hasProgress = $subPicas->contains(fn($p) => $p->status === 'in_progress');
            $subStatus = $hasOpen ? 'open' : ($hasProgress ? 'in_progress' : 'closed');
            $stBadge = $subStatus === 'closed' ? 'bg-success' : ($subStatus === 'in_progress' ? 'bg-warning text-dark' : 'bg-danger');
            $stLabel = $subStatus === 'closed' ? 'Closed' : ($subStatus === 'in_progress' ? 'In Progress' : 'Open');
            
            $kriClosedCount = $subPicas->filter(fn($p) => $p->status === 'closed')->count();
            $kriTotalCount = $subPicas->count();
        @endphp

        <div class="accordion-item border rounded-3 mb-2 shadow-xs overflow-hidden">
            <h2 class="accordion-header" id="heading{{ $uniquePrefix }}_{{ $subElemenId }}">
                <button class="accordion-button {{ $subItemIndex > 1 ? 'collapsed' : '' }} bg-white py-3 px-3.5" type="button" 
                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $uniquePrefix }}_{{ $subElemenId }}" 
                    aria-expanded="{{ $subItemIndex == 1 ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center justify-content-between w-100 me-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary font-monospace">{{ $subKode }}</span>
                            <strong class="text-slate-800 text-truncate" style="max-width: 520px;" title="{{ $subNama }}">
                                {{ $subNama }}
                            </strong>
                            @if($hasSubSub)
                                <span class="badge bg-light text-primary border rounded-pill px-2.5 py-0.5 small fw-bold">
                                    <i class="bi bi-diagram-3 me-1"></i>{{ $kriTotalCount }} Sub-Sub Kriteria
                                    @if($kriClosedCount > 0 && $kriClosedCount < $kriTotalCount)
                                        <span class="text-success ms-1">({{ $kriClosedCount }}/{{ $kriTotalCount }} Closed)</span>
                                    @endif
                                </span>
                            @else
                                <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 small">
                                    Penilaian Langsung
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ $stBadge }} rounded-pill px-3 py-1 small fw-semibold">
                                {{ $stLabel }}
                            </span>
                        </div>
                    </div>
                </button>
            </h2>
            <div id="collapse{{ $uniquePrefix }}_{{ $subElemenId }}" class="accordion-collapse collapse {{ $subItemIndex == 1 ? 'show' : '' }}" 
                data-bs-parent="#accordion{{ $uniquePrefix }}">
                <div class="accordion-body p-0 border-top">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 matrix-tree-table">
                            <thead class="table-light text-slate-700 small text-uppercase">
                                <tr>
                                    <th style="width: 45px;" class="ps-3 text-center">No</th>
                                    <th style="width: 150px;">Kriteria / Sub-Sub</th>
                                    <th style="min-width: 220px;">Uraian Ketidaksesuaian</th>
                                    <th style="min-width: 180px;">Akar Masalah</th>
                                    <th style="min-width: 230px;">Tindakan Koreksi & Pencegahan</th>
                                    <th style="width: 140px;">PIC & Target</th>
                                    <th style="width: 110px;" class="text-center">Status</th>
                                    <th style="width: 90px;" class="text-center pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subPicas as $idx => $pica)
                                    @php
                                        $status = $pica->status ?? 'open';
                                        $itemBadge = $status === 'closed' ? 'bg-success' : ($status === 'in_progress' ? 'bg-warning text-dark' : 'bg-danger');
                                        $itemLabel = $status === 'closed' ? 'Closed' : ($status === 'in_progress' ? 'In Progress' : 'Open');
                                        $kri = $pica->auditDetail->kriteria ?? null;
                                    @endphp
                                    <tr>
                                        <td class="ps-3 text-center text-muted small fw-semibold">{{ $idx + 1 }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold d-inline-block mb-1">
                                                {{ $kri->kode_kriteria ?? '-' }}
                                            </span>
                                            <div class="small fw-semibold text-slate-800" style="max-width: 170px; line-height: 1.25;">
                                                {{ $kri->nama_kriteria ?? ($kri->deskripsi ?? '-') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-slate-800 fw-medium mb-1">
                                                {{ $pica->deskripsi_temuan ?? '-' }}
                                            </div>
                                            <span class="badge bg-light text-danger border px-2 py-0 small">
                                                Skor: {{ $pica->auditDetail->nilai ?? 0 }} / {{ $kri->nilai_maksimal ?? 4 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small text-slate-700">
                                                {{ $pica->akar_masalah ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small mb-1">
                                                <strong class="text-success d-block" style="font-size: 0.75rem;"><i class="bi bi-shield-check me-1"></i>Koreksi:</strong>
                                                <span class="text-slate-700">{{ $pica->tindakan_koreksi ?? '-' }}</span>
                                            </div>
                                            <div class="small">
                                                <strong class="text-info d-block" style="font-size: 0.75rem;"><i class="bi bi-arrow-repeat me-1"></i>Pencegahan:</strong>
                                                <span class="text-slate-700">{{ $pica->tindakan_pencegahan ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="small text-slate-800 d-block">{{ $pica->pic_perbaikan ?? ($pica->pj_nama ?? '-') }}</strong>
                                            <small class="text-muted d-block">
                                                Target: {{ $pica->tenggat_waktu ? \Carbon\Carbon::parse($pica->tenggat_waktu)->format('d/m/Y') : '-' }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $itemBadge }} rounded-pill px-3 py-1 small fw-semibold">
                                                {{ $itemLabel }}
                                            </span>
                                            @if(!empty($pica->catatan_verifikasi_auditor))
                                                <small class="d-block text-muted mt-1 fst-italic" title="Catatan Verifikasi: {{ $pica->catatan_verifikasi_auditor }}" style="font-size: 0.75rem; cursor: help;">
                                                    <i class="bi bi-chat-dots me-1 text-primary"></i>Verifikasi
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center pe-3">
                                            @if(auth()->user()->role === 'admin')
                                                <a href="{{ route('admin.pica.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 small" style="font-size: 0.75rem;">
                                                    <i class="bi bi-tools me-1"></i> PICA
                                                </a>
                                            @elseif(auth()->user()->role === 'auditor')
                                                <a href="{{ route('auditor.pica.edit', $pica->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 small" style="font-size: 0.75rem;">
                                                    <i class="bi bi-pencil-square me-1"></i> Tindak
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
