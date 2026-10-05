<?php
    // Group findings by Elemen first, then by Sub-Elemen
    $groupedByElemen = $items->groupBy(function($pica) {
        $kri = $pica->auditDetail->kriteria ?? null;
        $sub = $kri->subElemen ?? null;
        return $sub->elemen_id ?? ($kri->elemen_id ?? 0);
    });
    
    $uniquePrefix = 'reportPica_' . uniqid();
?>

<div class="accordion mb-4" id="accordion_<?php echo e($uniquePrefix); ?>">
    <?php $elemIndex = 0; ?>
    <?php foreach($groupedByElemen as $elemenId => $elemenPicas): ?>
        <?php
            $elemIndex++;
            $firstPica = $elemenPicas->first();
            $kriFirst = $firstPica->auditDetail->kriteria ?? null;
            $subFirst = $kriFirst->subElemen ?? null;
            $elemen = $subFirst->elemen ?? null;
            
            $elemenKode = $elemen->kode_elemen ?? '-';
            $elemenNama = $elemen->nama_elemen ?? 'Elemen Non-Terdefinisi';
            $elemenBobot = $elemen->bobot ?? 0;
            
            // Group this Elemen's Picas by Sub-Elemen
            $groupedBySub = $elemenPicas->groupBy(function($pica) {
                $kri = $pica->auditDetail->kriteria ?? null;
                return $kri->sub_elemen_id ?? 0;
            });

            // Count open/progress/closed
            $totalTemuanElem = $elemenPicas->count();
            $closedCount = $elemenPicas->filter(fn($p) => $p->status === 'closed')->count();
        ?>

        <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
            <h2 class="accordion-header" id="heading_<?php echo e($uniquePrefix); ?>_<?php echo e($elemenId); ?>">
                <button class="accordion-button <?php echo e($elemIndex > 1 ? 'collapsed' : ''); ?> bg-light text-slate-800 fw-bold py-3 px-4" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#collapse_<?php echo e($uniquePrefix); ?>_<?php echo e($elemenId); ?>" 
                        aria-expanded="<?php echo e($elemIndex == 1 ? 'true' : 'false'); ?>" 
                        aria-controls="collapse_<?php echo e($uniquePrefix); ?>_<?php echo e($elemenId); ?>">
                    <div class="d-flex flex-wrap align-items-center justify-content-between w-100 me-3 gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark rounded-pill px-3 py-2">Elemen <?php echo e($elemenKode); ?></span>
                            <span class="fs-6 text-slate-900"><?php echo e($elemenNama); ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <?php if($elemenBobot > 0): ?>
                                <small class="text-muted">Bobot: <strong><?php echo e($elemenBobot); ?>%</strong></small>
                            <?php endif; ?>
                            <span class="badge bg-secondary rounded-pill px-3 py-1.5 small">
                                <i class="bi bi-list-check me-1"></i><?php echo e($totalTemuanElem); ?> Temuan
                                <?php if($closedCount > 0): ?>
                                    <span class="text-success ms-1">(<?php echo e($closedCount); ?>/<?php echo e($totalTemuanElem); ?> Closed)</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </button>
            </h2>

            <div id="collapse_<?php echo e($uniquePrefix); ?>_<?php echo e($elemenId); ?>" 
                 class="accordion-collapse collapse <?php echo e($elemIndex == 1 ? 'show' : ''); ?>" 
                 aria-labelledby="heading_<?php echo e($uniquePrefix); ?>_<?php echo e($elemenId); ?>" 
                 data-bs-parent="#accordion_<?php echo e($uniquePrefix); ?>">
                <div class="accordion-body p-0 border-top">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 matrix-tree-table">
                            <thead class="table-light text-slate-700 small text-uppercase">
                                <tr>
                                    <th style="width: 110px;" class="ps-4">Kode</th>
                                    <th style="min-width: 180px;">Deskripsi Kriteria</th>
                                    <th style="width: 100px;" class="text-center">Skor</th>
                                    <th style="width: 90px;" class="text-center">Capaian</th>
                                    <th style="min-width: 220px;">Uraian Ketidaksesuaian & Akar Masalah</th>
                                    <th style="min-width: 230px;">Tindakan Koreksi & Pencegahan</th>
                                    <th style="width: 140px;">PIC & Target</th>
                                    <th style="width: 110px;" class="text-center">Status</th>
                                    <th style="width: 90px;" class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($groupedBySub as $subId => $subPicas): ?>
                                    <?php
                                        $firstSubPica = $subPicas->first();
                                        $kriSub = $firstSubPica->auditDetail->kriteria ?? null;
                                        $subElemen = $kriSub->subElemen ?? null;
                                        $subKode = $subElemen->kode_sub ?? ($kriSub->kode_kriteria ?? '-');
                                        $subNama = $subElemen->nama_sub ?? ($kriSub->deskripsi ?? '-');

                                        $subNilaiAktual = $subPicas->sum(fn($p) => $p->auditDetail->nilai ?? 0);
                                        $subNilaiMaks = $subPicas->sum(fn($p) => $p->auditDetail->kriteria->nilai_maksimal ?? 4);
                                        $subPct = $subNilaiMaks > 0 ? ($subNilaiAktual / $subNilaiMaks) * 100 : 0;
                                        $subBadgeClass = $subPct >= 85 ? 'bg-success' : ($subPct >= 70 ? 'bg-warning text-dark' : 'bg-danger');
                                    ?>

                                    <!-- Sub-Elemen Header Row -->
                                    <tr class="table-secondary fw-semibold bg-slate-100">
                                        <td class="ps-4 text-primary text-nowrap">
                                            <i class="bi bi-folder2-open me-1"></i> <?php echo e($subKode); ?>
                                        </td>
                                        <td class="text-slate-900 fw-bold">
                                            <?php echo e($subNama); ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold"><?php echo e($subNilaiAktual); ?></span> 
                                            <span class="text-muted">/ <?php echo e($subNilaiMaks); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?php echo e($subBadgeClass); ?> rounded-pill px-2 py-1">
                                                <?php echo e(number_format($subPct, 1)); ?>%
                                            </span>
                                        </td>
                                        <td colspan="5" class="text-muted small pe-4 text-end">
                                            Total Sub-Elemen: <strong><?php echo e($subPicas->count()); ?> Temuan</strong>
                                        </td>
                                    </tr>

                                    <!-- Criteria Child Rows -->
                                    <?php foreach($subPicas as $pica): ?>
                                        <?php
                                            $detail = $pica->auditDetail ?? null;
                                            $kri = $detail->kriteria ?? null;
                                            $kriKode = $kri->kode_kriteria ?? '-';
                                            $kriNama = $kri->nama_kriteria ?? ($kri->deskripsi ?? '-');
                                            $kriNilai = $detail->nilai ?? 0;
                                            $kriMaks = $kri->nilai_maksimal ?? 4;
                                            $kriPct = $kriMaks > 0 ? ($kriNilai / $kriMaks) * 100 : 0;
                                            $kriPctBadge = $kriPct == 100 ? 'bg-success bg-opacity-10 text-success' : ($kriPct == 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-warning bg-opacity-10 text-warning');

                                            $status = $pica->status ?? 'open';
                                            $stBadge = $status === 'closed' ? 'bg-success' : ($status === 'in_progress' ? 'bg-warning text-dark' : 'bg-danger');
                                            $stLabel = $status === 'closed' ? 'Closed' : ($status === 'in_progress' ? 'In Progress' : 'Open');
                                        ?>
                                        <tr>
                                            <td class="ps-4 text-nowrap">
                                                <span class="badge bg-light text-dark border px-2 py-1 ms-2 font-monospace">
                                                    <?php echo e($kriKode); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-slate-800" style="max-width: 210px; line-height: 1.3;">
                                                    <?php echo e($kriNama); ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold <?php echo e($kriNilai == $kriMaks ? 'text-success' : ($kriNilai == 0 ? 'text-danger' : 'text-primary')); ?>">
                                                    <?php echo e($kriNilai); ?>
                                                </span>
                                                <span class="text-muted">/ <?php echo e($kriMaks); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?php echo e($kriPctBadge); ?> rounded-pill px-2 py-1">
                                                    <?php echo e(number_format($kriPct, 0)); ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small text-slate-800 fw-medium mb-1">
                                                    <?php echo e($pica->deskripsi_temuan ?? '-'); ?>
                                                </div>
                                                <?php if(!empty($pica->akar_masalah)): ?>
                                                    <div class="small text-muted mt-1" style="font-size: 0.78rem;">
                                                        <strong class="text-secondary"><i class="bi bi-search me-1"></i>Akar Masalah:</strong>
                                                        <span class="text-slate-700"><?php echo e($pica->akar_masalah); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="small mb-1">
                                                    <strong class="text-success d-block" style="font-size: 0.75rem;"><i class="bi bi-shield-check me-1"></i>Koreksi:</strong>
                                                    <span class="text-slate-700"><?php echo e($pica->tindakan_koreksi ?? '-'); ?></span>
                                                </div>
                                                <div class="small">
                                                    <strong class="text-info d-block" style="font-size: 0.75rem;"><i class="bi bi-arrow-repeat me-1"></i>Pencegahan:</strong>
                                                    <span class="text-slate-700"><?php echo e($pica->tindakan_pencegahan ?? '-'); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="small text-slate-800 d-block"><?php echo e($pica->pic_perbaikan ?? ($pica->pj_nama ?? '-')); ?></strong>
                                                <small class="text-muted d-block">
                                                    Target: <?php echo e($pica->tenggat_waktu ? \Carbon\Carbon::parse($pica->tenggat_waktu)->format('d/m/Y') : '-'); ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?php echo e($stBadge); ?> rounded-pill px-3 py-1 small fw-semibold">
                                                    <?php echo e($stLabel); ?>
                                                </span>
                                                <?php if(!empty($pica->catatan_verifikasi_auditor)): ?>
                                                    <small class="d-block text-muted mt-1 fst-italic" title="Catatan Verifikasi: <?php echo e($pica->catatan_verifikasi_auditor); ?>" style="font-size: 0.75rem; cursor: help;">
                                                        <i class="bi bi-chat-dots me-1 text-primary"></i>Verifikasi
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center pe-4">
                                                <?php if(auth()->user()->role === 'admin'): ?>
                                                    <a href="<?php echo e(route('admin.pica.index')); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 small" style="font-size: 0.75rem;">
                                                        <i class="bi bi-tools me-1"></i> PICA
                                                    </a>
                                                <?php elseif(auth()->user()->role === 'auditor'): ?>
                                                    <a href="<?php echo e(route('auditor.pica.edit', $pica->id)); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 small" style="font-size: 0.75rem;">
                                                        <i class="bi bi-pencil-square me-1"></i> Tindak
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
