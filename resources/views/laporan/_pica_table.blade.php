<div class="table-responsive">
    <table class="table table-hover align-middle mb-0 matrix-tree-table">
        <thead class="table-light text-slate-700 small text-uppercase">
            <tr>
                <th style="width: 45px;" class="ps-3 text-center">No</th>
                <th style="width: 150px;">Kriteria</th>
                <th style="min-width: 220px;">Uraian Ketidaksesuaian</th>
                <th style="min-width: 180px;">Akar Masalah</th>
                <th style="min-width: 230px;">Tindakan Koreksi & Pencegahan</th>
                <th style="width: 140px;">PIC & Target</th>
                <th style="width: 120px;" class="text-center">Status</th>
                <th style="width: 90px;" class="text-center pe-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $idx => $pica)
                @php
                    $status = $pica->status ?? 'open';
                    $stBadge = $status === 'closed' ? 'bg-success' : ($status === 'in_progress' ? 'bg-warning text-dark' : 'bg-danger');
                    $stLabel = $status === 'closed' ? 'Closed' : ($status === 'in_progress' ? 'In Progress' : 'Open');
                    $kri = $pica->auditDetail->kriteria ?? null;
                    $sub = $kri->subElemen ?? null;
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
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                            {{ $sub->nama_sub ?? ($sub->nama_sub_elemen ?? '') }}
                        </small>
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
                        <strong class="small text-slate-800 d-block">{{ $pica->pj_nama ?? '-' }}</strong>
                        <small class="text-muted d-block">
                            Target: {{ $pica->target_selesai ? \Carbon\Carbon::parse($pica->target_selesai)->format('d/m/Y') : '-' }}
                        </small>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $stBadge }} rounded-pill px-3 py-1 small fw-semibold">
                            {{ $stLabel }}
                        </span>
                        @if(!empty($pica->auditor_catatan))
                            <small class="d-block text-muted mt-1 fst-italic" title="Catatan Auditor: {{ $pica->auditor_catatan }}" style="font-size: 0.75rem; cursor: help;">
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
