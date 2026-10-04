<?php
$title = 'Monitoring PICA — SMKP Minerba Administrator';
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-tools text-danger me-2"></i> Monitoring PICA per Area Audit
        </h3>
        <p class="text-muted small mb-0">Pemantauan dan pengawasan terpusat seluruh temuan PICA yang dikelompokkan berdasarkan Area Audit</p>
    </div>
    <?php if($stats['overdue'] > 0): ?>
        <div>
            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo e($stats['overdue']); ?> Temuan Melewati Tenggat (Overdue)
            </span>
        </div>
    <?php endif; ?>
</div>

<!-- Stat Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block">Total Temuan (Sub-Elemen)</span>
                    <h3 class="fw-bold text-slate-800 mb-0 mt-1"><?php echo e(number_format($stats['total'])); ?></h3>
                    <small class="text-muted" style="font-size: 0.72rem;"><?php echo e(number_format($stats['total_kriteria'])); ?> Tindakan Kriteria</small>
                </div>
                <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-card-checklist"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block">Status Open</span>
                    <h3 class="fw-bold text-danger mb-0 mt-1"><?php echo e(number_format($stats['open'])); ?></h3>
                    <small class="text-muted" style="font-size: 0.72rem;"><?php echo e(number_format($stats['open_kriteria'])); ?> Kriteria Open</small>
                </div>
                <div class="stat-icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-octagon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block">In Progress</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1"><?php echo e(number_format($stats['in_progress'])); ?></h3>
                    <small class="text-muted" style="font-size: 0.72rem;"><?php echo e(number_format($stats['in_progress_kriteria'])); ?> Kriteria In Progress</small>
                </div>
                <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block">Closed / Verifikasi</span>
                    <h3 class="fw-bold text-success mb-0 mt-1"><?php echo e(number_format($stats['closed'])); ?></h3>
                    <small class="text-muted" style="font-size: 0.72rem;"><?php echo e(number_format($stats['closed_kriteria'])); ?> Kriteria Selesai</small>
                </div>
                <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form action="<?php echo e(route('admin.pica.index')); ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control bg-light border-start-0" 
                    placeholder="Cari area audit, temuan, PIC, atau auditor..." value="<?php echo e(request('search')); ?>">
            </div>
        </div>
        <div class="col-md-3">
            <select name="user_id" class="form-select bg-light">
                <option value="">-- Semua Auditor --</option>
                <?php foreach($auditors as $auditor): ?>
                    <option value="<?php echo e($auditor->id); ?>" <?php echo e(request('user_id') == $auditor->id ? 'selected' : ''); ?>>
                        <?php echo e($auditor->name); ?>

                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select bg-light">
                <option value="">-- Semua Status PICA --</option>
                <option value="open" <?php echo e(request('status') == 'open' ? 'selected' : ''); ?>>Ada Temuan Open</option>
                <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>Ada Temuan In Progress</option>
                <option value="closed" <?php echo e(request('status') == 'closed' ? 'selected' : ''); ?>>Seluruh Temuan Closed</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-danger w-100 fw-semibold">
                <i class="bi bi-filter me-1"></i> Filter
            </button>
            <?php if(request()->hasAny(['search', 'status', 'user_id'])): ?>
                <a href="<?php echo e(route('admin.pica.index')); ?>" class="btn btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card (Grouped by Area Audit) -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Auditor</th>
                    <th>Area & Tanggal Audit</th>
                    <th>Total Temuan</th>
                    <th>Breakdown Status</th>
                    <th>Progress Penyelesaian</th>
                    <th class="pe-4 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if($auditSesis->isEmpty()): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard-check display-4 d-block mb-3 text-secondary opacity-50"></i>
                            <h6 class="fw-bold">Belum Ada Sesi Audit dengan PICA</h6>
                            <p class="small mb-0">Seluruh temuan PICA akan dikelompokkan berdasarkan Area Audit di sini.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($auditSesis as $index => $sesi): ?>
                        <?php
                            $auditor = $sesi->user;
                            $detailsWithPica = $sesi->auditDetails->filter(fn($d) => $d->pica != null);
                            $groupedBySubElemen = $detailsWithPica->groupBy(fn($d) => $d->kriteria->sub_elemen_id ?? 0);
                            $subElemenCount = $groupedBySubElemen->count();
                            $totalPica = $detailsWithPica->count();
                            
                            $subOpenCount = 0;
                            $subProgressCount = 0;
                            $subClosedCount = 0;
                            
                            foreach ($groupedBySubElemen as $subDetails) {
                                $hasOpen = $subDetails->contains(fn($d) => $d->pica && $d->pica->status === 'open');
                                $hasProgress = $subDetails->contains(fn($d) => $d->pica && $d->pica->status === 'in_progress');
                                
                                if ($hasProgress) {
                                    $subProgressCount++;
                                } elseif ($hasOpen) {
                                    $subOpenCount++;
                                } else {
                                    $subClosedCount++;
                                }
                            }
                            
                            $overdueCount = $detailsWithPica->filter(fn($d) => $d->pica->status !== 'closed' && $d->pica->tenggat_waktu && $d->pica->tenggat_waktu->isPast())->count();
                            $pctClosed = $subElemenCount > 0 ? round(($subClosedCount / $subElemenCount) * 100) : 0;
                        ?>
                        <tr>
                            <td class="ps-4 fw-semibold text-muted"><?php echo e($auditSesis->firstItem() + $index); ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-info bg-opacity-10 text-dark rounded-circle p-2 d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                        <?php echo e($auditor ? strtoupper(substr($auditor->name, 0, 2)) : '??'); ?>

                                    </div>
                                    <div>
                                        <strong class="d-block small text-slate-800"><?php echo e($auditor ? $auditor->name : '-'); ?></strong>
                                        <small class="text-muted" style="font-size: 0.75rem;">Auditor</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong class="d-block text-slate-800 fs-6"><?php echo e($sesi->area_audit); ?></strong>
                                <small class="text-muted"><i class="bi bi-calendar me-1"></i> <?php echo e($sesi->tanggal_mulai->format('d M Y')); ?> - <?php echo e($sesi->tanggal_selesai->format('d M Y')); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-bold" title="<?php echo e($totalPica); ?> rincian tindakan kriteria">
                                    <?php echo e($subElemenCount); ?> Sub-Elemen <small class="text-muted font-normal">(<?php echo e($totalPica); ?> Kriteria)</small>
                                </span>
                                <?php if($overdueCount > 0): ?>
                                    <span class="badge bg-danger rounded-pill px-2 py-1 ms-1" style="font-size: 0.65rem;" title="<?php echo e($overdueCount); ?> temuan overdue">
                                        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo e($overdueCount); ?> Overdue
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <span class="badge bg-danger rounded-pill px-2 py-1" title="<?php echo e($subOpenCount); ?> Sub-Elemen Open"><?php echo e($subOpenCount); ?> Open</span>
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1" title="<?php echo e($subProgressCount); ?> Sub-Elemen In Progress"><?php echo e($subProgressCount); ?> In Progress</span>
                                    <span class="badge bg-success rounded-pill px-2 py-1" title="<?php echo e($subClosedCount); ?> Sub-Elemen Closed"><?php echo e($subClosedCount); ?> Closed</span>
                                </div>
                            </td>
                            <td style="min-width: 180px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($pctClosed); ?>%"></div>
                                    </div>
                                    <span class="small fw-bold text-slate-700"><?php echo e($pctClosed); ?>%</span>
                                </div>
                                <small class="text-muted d-block mt-0.5" style="font-size: 0.7rem;"><?php echo e($subClosedCount); ?>/<?php echo e($subElemenCount); ?> Sub-Elemen Selesai</small>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold" 
                                    data-bs-toggle="modal" data-bs-target="#adminAreaPicaModal<?php echo e($sesi->id); ?>">
                                    <i class="bi bi-pencil-square me-1"></i> Detail & Edit PICA (<?php echo e($subElemenCount); ?>)
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($auditSesis->hasPages()): ?>
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-muted">
                Menampilkan <?php echo e($auditSesis->firstItem() ?? 0); ?> - <?php echo e($auditSesis->lastItem() ?? 0); ?> dari total <?php echo e($auditSesis->total()); ?> sesi PICA
            </div>
            <div>
                <?php echo $auditSesis->links('pagination::bootstrap-5'); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Detail & Admin Edit PICA per Area Audit -->
<?php foreach($auditSesis as $sesi): ?>
    <?php
        $auditor = $sesi->user;
        $detailsWithPica = $sesi->auditDetails->filter(fn($d) => $d->pica != null);
        $groupedBySubElemen = $detailsWithPica->groupBy(fn($d) => $d->kriteria->sub_elemen_id ?? 0);
        $subElemenCount = $groupedBySubElemen->count();
        $totalPicaCount = $detailsWithPica->count();
    ?>
    <div class="modal fade" id="adminAreaPicaModal<?php echo e($sesi->id); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            <i class="bi bi-tools text-danger me-2"></i> Pengawasan PICA — Area: <?php echo e($sesi->area_audit); ?>

                        </h5>
                        <small class="text-slate-300" style="font-size: 0.8rem; color: #cbd5e1;">
                            <i class="bi bi-person me-1"></i> Auditor: <?php echo e($auditor ? $auditor->name : '-'); ?> | <i class="bi bi-calendar me-1"></i> <?php echo e($sesi->tanggal_mulai->format('d M Y')); ?> - <?php echo e($sesi->tanggal_selesai->format('d M Y')); ?> | <strong>Total <?php echo e($subElemenCount); ?> Temuan Sub-Elemen (Maks. 51)</strong> &bull; <?php echo e($totalPicaCount); ?> Tindakan Kriteria
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    
                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2 small">
                        <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                        <div>
                            Daftar temuan ditampilkan per <strong>Sub-Elemen</strong> (maksimal 51 temuan). Jika salah satu temuan sub-sub kriteria berstatus <em>In Progress</em>, maka sub-elemen tersebut tercatat <strong>In Progress</strong> meskipun masih ada yang <em>Open</em>. Status akan <strong>Closed</strong> hanya jika seluruh tindakannya telah diverifikasi selesai.
                        </div>
                    </div>

                    <!-- Accordion Tingkat 1: Sub-Elemen -->
                    <div class="accordion" id="adminAccordionSubSesi<?php echo e($sesi->id); ?>">
                        <?php $subIndex = 0; ?>
                        <?php foreach($groupedBySubElemen as $subElemenId => $subDetails): ?>
                            <?php
                                $subIndex++;
                                $firstDetail = $subDetails->first();
                                $subElemen = $firstDetail->kriteria->subElemen ?? null;
                                $subKode = $subElemen->kode_sub ?? '-';
                                $subNama = $subElemen->nama_sub ?? '-';
                                
                                $hasSubSub = $subDetails->count() > 1 
                                    || ($subElemen && $subElemen->kriterias && $subElemen->kriterias->count() > 1) 
                                    || ($firstDetail->kriteria && $firstDetail->kriteria->kode_kriteria !== $subKode);
                                
                                $hasKritikal = $subDetails->contains(fn($d) => $d->pica && $d->pica->kategori_temuan === 'kritikal');
                                $hasMayor = $subDetails->contains(fn($d) => $d->pica && $d->pica->kategori_temuan === 'mayor');
                                $subKategori = $hasKritikal ? 'kritikal' : ($hasMayor ? 'mayor' : 'minor');
                                
                                $hasOpen = $subDetails->contains(fn($d) => $d->pica && $d->pica->status === 'open');
                                $hasProgress = $subDetails->contains(fn($d) => $d->pica && $d->pica->status === 'in_progress');
                                $subStatus = $hasProgress ? 'in_progress' : ($hasOpen ? 'open' : 'closed');
                                
                                $kriClosedCount = $subDetails->filter(fn($d) => $d->pica && $d->pica->status === 'closed')->count();
                                $kriTotalCount = $subDetails->count();
                                
                                $isSubOverdue = $subDetails->contains(fn($d) => $d->pica && $d->pica->status !== 'closed' && $d->pica->tenggat_waktu && $d->pica->tenggat_waktu->isPast());
                            ?>

                            <div class="accordion-item border rounded-3 mb-3 shadow-sm overflow-hidden">
                                <h2 class="accordion-header" id="adminHeadingSub<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>">
                                    <button class="accordion-button <?php echo e($subIndex > 1 ? 'collapsed' : ''); ?> bg-white py-3" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#adminCollapseSub<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>" 
                                        aria-expanded="<?php echo e($subIndex == 1 ? 'true' : 'false'); ?>">
                                        <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-secondary font-monospace fs-6 px-2.5 py-1"><?php echo e($subKode); ?></span>
                                                <strong class="text-slate-800 text-truncate" style="max-width: 480px;" title="<?php echo e($subNama); ?>">
                                                    <?php echo e($subNama); ?>

                                                </strong>
                                                <?php if($hasSubSub): ?>
                                                    <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1 small fw-bold">
                                                        <i class="bi bi-diagram-3 me-1"></i><?php echo e($kriTotalCount); ?> Sub-Sub Kriteria
                                                        <?php if($kriClosedCount > 0 && $kriClosedCount < $kriTotalCount): ?>
                                                            <span class="text-success ms-1">(<?php echo e($kriClosedCount); ?>/<?php echo e($kriTotalCount); ?> Closed)</span>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                                                        Penilaian Langsung
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if($subKategori === 'kritikal'): ?>
                                                    <span class="badge bg-danger rounded-pill px-3 py-1"><i class="bi bi-shield-exclamation me-1"></i> Kritikal</span>
                                                <?php elseif($subKategori === 'mayor'): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Mayor</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info text-white rounded-pill px-3 py-1"><i class="bi bi-info-circle-fill me-1"></i> Minor</span>
                                                <?php endif; ?>

                                                <?php if($isSubOverdue): ?>
                                                    <span class="badge bg-danger">Overdue</span>
                                                <?php endif; ?>

                                                <?php if($subStatus === 'open'): ?>
                                                    <span class="badge bg-danger rounded-pill px-3 py-1">Open</span>
                                                <?php elseif($subStatus === 'in_progress'): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1">In Progress</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success rounded-pill px-3 py-1">Closed</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="adminCollapseSub<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>" class="accordion-collapse collapse <?php echo e($subIndex == 1 ? 'show' : ''); ?>" 
                                    data-bs-parent="#adminAccordionSubSesi<?php echo e($sesi->id); ?>">
                                    <div class="accordion-body bg-light p-3 border-top">

                                        <?php if($hasSubSub): ?>
                                            <div class="mb-2 px-1 text-muted small fw-semibold d-flex align-items-center gap-1">
                                                <i class="bi bi-chevron-down text-primary"></i> Rincian Sub-Sub Elemen / Kriteria Temuan:
                                            </div>
                                            <div class="accordion" id="adminNestedAccordion_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>">
                                                <?php foreach($subDetails as $kriIndex => $detail): ?>
                                                    <?php
                                                        $pica = $detail->pica;
                                                        $kriteria = $detail->kriteria;
                                                        $isOverdue = $pica->status !== 'closed' && $pica->tenggat_waktu && $pica->tenggat_waktu->isPast();
                                                    ?>
                                                    <div class="accordion-item border rounded-3 mb-2 bg-white shadow-xs overflow-hidden">
                                                        <h3 class="accordion-header" id="adminNestedHeading<?php echo e($pica->id); ?>">
                                                            <button class="accordion-button <?php echo e($kriIndex > 0 ? 'collapsed' : ''); ?> bg-white py-2.5 px-3" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#adminNestedCollapse<?php echo e($pica->id); ?>"
                                                                aria-expanded="<?php echo e($kriIndex == 0 ? 'true' : 'false'); ?>">
                                                                <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span class="badge bg-dark font-monospace"><?php echo e($kriteria ? $kriteria->kode_kriteria : '-'); ?></span>
                                                                        <span class="text-slate-800 fw-semibold small text-truncate" style="max-width: 420px;" title="<?php echo e($pica->deskripsi_temuan); ?>">
                                                                            Ketidaksesuaian <?php echo e($kriteria ? $kriteria->kode_kriteria : '-'); ?> (Skor <?php echo e($detail->nilai); ?> / <?php echo e($kriteria->nilai_maksimal ?? 4); ?>)
                                                                        </span>
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <?php if($pica->kategori_temuan === 'kritikal'): ?>
                                                                            <span class="badge bg-danger rounded-pill px-2.5 py-1 small">Kritikal</span>
                                                                        <?php elseif($pica->kategori_temuan === 'mayor'): ?>
                                                                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 small">Mayor</span>
                                                                        <?php else: ?>
                                                                            <span class="badge bg-info text-white rounded-pill px-2.5 py-1 small">Minor</span>
                                                                        <?php endif; ?>

                                                                        <?php if($isOverdue): ?>
                                                                            <span class="badge bg-danger small">Overdue</span>
                                                                        <?php endif; ?>

                                                                        <?php if($pica->status === 'open'): ?>
                                                                            <span class="badge bg-danger rounded-pill px-2.5 py-1 small">Open</span>
                                                                        <?php elseif($pica->status === 'in_progress'): ?>
                                                                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 small">In Progress</span>
                                                                        <?php else: ?>
                                                                            <span class="badge bg-success rounded-pill px-2.5 py-1 small">Closed</span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h3>
                                                        <div id="adminNestedCollapse<?php echo e($pica->id); ?>" class="accordion-collapse collapse <?php echo e($kriIndex == 0 ? 'show' : ''); ?>"
                                                            data-bs-parent="#adminNestedAccordion_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>">
                                                            <div class="accordion-body bg-white p-4 border-top">
                                                                
                                                                <form action="<?php echo e(route('admin.pica.update', $pica->id)); ?>" method="POST">
                                                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                                    <input type="hidden" name="_method" value="PUT">
                                                                    
                                                                    <div class="p-3 bg-light rounded-3 border mb-4">
                                                                        <div class="row g-2">
                                                                            <div class="col-md-3">
                                                                                <small class="text-muted d-block">Kode Kriteria / Sub-Sub:</small>
                                                                                <strong class="text-slate-800"><?php echo e($kriteria ? $kriteria->kode_kriteria : '-'); ?></strong>
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <small class="text-muted d-block">Persyaratan Dokumen / Deskripsi Kriteria:</small>
                                                                                <div class="small text-slate-700 fw-semibold"><?php echo e($kriteria ? $kriteria->deskripsi : '-'); ?></div>
                                                                            </div>
                                                                            <div class="col-12 mt-2">
                                                                                <small class="text-muted d-block">Deskripsi Temuan Audit:</small>
                                                                                <div class="fw-bold text-danger p-2 bg-white rounded border border-danger border-opacity-25">
                                                                                    <?php echo e($pica->deskripsi_temuan); ?>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row g-3">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label fw-semibold small text-secondary">Kategori Temuan Audit <span class="text-danger">*</span></label>
                                                                            <select name="kategori_temuan" class="form-select bg-light fw-bold kategori-select-toggle" data-pica-id="<?php echo e($pica->id); ?>" id="kategoriSelect<?php echo e($pica->id); ?>">
                                                                                <option value="kritikal" <?php echo e(old('kategori_temuan', $pica->kategori_temuan) == 'kritikal' ? 'selected' : ''); ?>>🔴 Kritikal (Potensi Fatality)</option>
                                                                                <option value="mayor" <?php echo e(old('kategori_temuan', $pica->kategori_temuan) == 'mayor' ? 'selected' : ''); ?>>🟠 Mayor (Sub Elemen < 50% / >30% Lokasi)</option>
                                                                                <option value="minor" <?php echo e(old('kategori_temuan', $pica->kategori_temuan) == 'minor' ? 'selected' : ''); ?>>🔵 Minor (Ketidaksesuaian Standard)</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6" id="justifikasiContainer<?php echo e($pica->id); ?>" style="<?php echo e(old('kategori_temuan', $pica->kategori_temuan) == 'kritikal' || $pica->kategori_ditetapkan_manual ? '' : 'display: none;'); ?>">
                                                                            <label class="form-label fw-semibold small text-secondary">Justifikasi Penetapan Kategori <span class="text-danger" id="justifikasiReqLabel<?php echo e($pica->id); ?>">*</span></label>
                                                                            <textarea name="justifikasi_kategori" rows="2" class="form-control bg-light" 
                                                                                placeholder="Alasan penetapan kategori manual (wajib diisi untuk Kritikal)..."><?php echo e(old('justifikasi_kategori', $pica->justifikasi_kategori)); ?></textarea>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <label class="form-label fw-semibold small text-secondary">Deskripsi Ketidaksesuaian <span class="text-danger">*</span></label>
                                                                            <textarea name="deskripsi_ketidaksesuaian" rows="2" class="form-control bg-light" 
                                                                                placeholder="Isi deskripsi ketidaksesuaian..."><?php echo e(old('deskripsi_ketidaksesuaian', $pica->deskripsi_ketidaksesuaian)); ?></textarea>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <label class="form-label fw-semibold small text-secondary">Akar Masalah (Root Cause) <span class="text-danger">*</span></label>
                                                                            <textarea name="akar_masalah" rows="2" class="form-control bg-light" 
                                                                                placeholder="Jelaskan analisis penyebab utama..."><?php echo e(old('akar_masalah', $pica->akar_masalah)); ?></textarea>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label fw-semibold small text-secondary">Tindakan Koreksi (Corrective Action)</label>
                                                                            <textarea name="tindakan_koreksi" rows="2" class="form-control bg-light" 
                                                                                placeholder="Tindakan langsung perbaikan..."><?php echo e(old('tindakan_koreksi', $pica->tindakan_koreksi)); ?></textarea>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label fw-semibold small text-secondary">Tindakan Pencegahan (Preventive Action)</label>
                                                                            <textarea name="tindakan_pencegahan" rows="2" class="form-control bg-light" 
                                                                                placeholder="Tindakan agar temuan tidak terulang..."><?php echo e(old('tindakan_pencegahan', $pica->tindakan_pencegahan)); ?></textarea>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label fw-semibold small text-secondary">PIC Penanggung Jawab Perbaikan</label>
                                                                            <input type="text" name="pic_perbaikan" class="form-control bg-light" 
                                                                                placeholder="Nama PIC perbaikan..." value="<?php echo e(old('pic_perbaikan', $pica->pic_perbaikan)); ?>">
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label fw-semibold small text-secondary">Tenggat Waktu Selesai (Target Date)</label>
                                                                            <input type="date" name="tenggat_waktu" class="form-control bg-light" 
                                                                                value="<?php echo e(old('tenggat_waktu', $pica->tenggat_waktu ? $pica->tenggat_waktu->format('Y-m-d') : '')); ?>">
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label fw-semibold small text-secondary">Status PICA <span class="text-danger">*</span></label>
                                                                            <select name="status" class="form-select bg-light fw-bold">
                                                                                <option value="open" <?php echo e(old('status', $pica->status) == 'open' ? 'selected' : ''); ?>>🔴 Open (Belum Ditindaklanjuti)</option>
                                                                                <option value="in_progress" <?php echo e(old('status', $pica->status) == 'in_progress' ? 'selected' : ''); ?>>🟡 In Progress (Dalam Perbaikan)</option>
                                                                                <option value="closed" <?php echo e(old('status', $pica->status) == 'closed' ? 'selected' : ''); ?>>🟢 Closed (Selesai Verifikasi)</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label fw-semibold small text-secondary">Catatan Verifikasi / Pengawasan (Admin)</label>
                                                                            <textarea name="catatan_verifikasi_auditor" rows="2" class="form-control bg-light" 
                                                                                placeholder="Wajib diisi saat status diubah ke Closed..."><?php echo e(old('catatan_verifikasi_auditor', $pica->catatan_verifikasi_auditor)); ?></textarea>
                                                                        </div>
                                                                    </div>

                                                                    <div class="mt-3 text-end">
                                                                        <button type="submit" class="btn btn-danger px-4 rounded-3 fw-semibold">
                                                                            <i class="bi bi-save me-1"></i> Simpan PICA <?php echo e($kriteria ? $kriteria->kode_kriteria : ''); ?>

                                                                        </button>
                                                                    </div>
                                                                </form>

                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <?php
                                                $singleDetail = $subDetails->first();
                                                $pica = $singleDetail->pica;
                                                $kriteria = $singleDetail->kriteria;
                                                $isOverdue = $pica->status !== 'closed' && $pica->tenggat_waktu && $pica->tenggat_waktu->isPast();
                                            ?>
                                            <div class="card border-0 shadow-sm rounded-3">
                                                <div class="card-body p-4 bg-white">
                                                    <form action="<?php echo e(route('admin.pica.update', $pica->id)); ?>" method="POST">
                                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                        <input type="hidden" name="_method" value="PUT">
                                                        
                                                        <div class="p-3 bg-light rounded-3 border mb-4">
                                                            <div class="row g-2">
                                                                <div class="col-md-3">
                                                                    <small class="text-muted d-block">Kode Sub-Elemen:</small>
                                                                    <strong class="text-slate-800"><?php echo e($subKode); ?></strong>
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <small class="text-muted d-block">Deskripsi Persyaratan Standar:</small>
                                                                    <div class="small text-slate-700 fw-semibold"><?php echo e($subNama); ?></div>
                                                                </div>
                                                                <div class="col-12 mt-2">
                                                                    <small class="text-muted d-block">Deskripsi Temuan Audit:</small>
                                                                    <div class="fw-bold text-danger p-2 bg-white rounded border border-danger border-opacity-25">
                                                                        <?php echo e($pica->deskripsi_temuan); ?>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row g-3">

                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-secondary">Kategori Temuan Audit <span class="text-danger">*</span></label>
                                                                <select name="kategori_temuan" class="form-select bg-light fw-bold kategori-select-toggle" data-pica-id="<?php echo e($pica->id); ?>" id="kategoriSelect<?php echo e($pica->id); ?>">
                                                                    <option value="kritikal" <?php echo e(old('kategori_temuan', $pica->kategori_temuan) == 'kritikal' ? 'selected' : ''); ?>>🔴 Kritikal (Potensi Fatality)</option>
                                                                    <option value="mayor" <?php echo e(old('kategori_temuan', $pica->kategori_temuan) == 'mayor' ? 'selected' : ''); ?>>🟠 Mayor (Sub Elemen < 50% / >30% Lokasi)</option>
                                                                    <option value="minor" <?php echo e(old('kategori_temuan', $pica->kategori_temuan) == 'minor' ? 'selected' : ''); ?>>🔵 Minor (Ketidaksesuaian Standard)</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6" id="justifikasiContainer<?php echo e($pica->id); ?>" style="<?php echo e(old('kategori_temuan', $pica->kategori_temuan) == 'kritikal' || $pica->kategori_ditetapkan_manual ? '' : 'display: none;'); ?>">
                                                                <label class="form-label fw-semibold small text-secondary">Justifikasi Penetapan Kategori <span class="text-danger" id="justifikasiReqLabel<?php echo e($pica->id); ?>">*</span></label>
                                                                <textarea name="justifikasi_kategori" rows="2" class="form-control bg-light" 
                                                                    placeholder="Alasan penetapan kategori manual..."><?php echo e(old('justifikasi_kategori', $pica->justifikasi_kategori)); ?></textarea>
                                                            </div>

                                                            <div class="col-12">
                                                                <label class="form-label fw-semibold small text-secondary">Deskripsi Ketidaksesuaian <span class="text-danger">*</span></label>
                                                                <textarea name="deskripsi_ketidaksesuaian" rows="2" class="form-control bg-light" 
                                                                    placeholder="Isi deskripsi ketidaksesuaian..."><?php echo e(old('deskripsi_ketidaksesuaian', $pica->deskripsi_ketidaksesuaian)); ?></textarea>
                                                            </div>

                                                            <div class="col-12">
                                                                <label class="form-label fw-semibold small text-secondary">Akar Masalah (Root Cause) <span class="text-danger">*</span></label>
                                                                <textarea name="akar_masalah" rows="2" class="form-control bg-light" 
                                                                    placeholder="Jelaskan analisis penyebab utama..."><?php echo e(old('akar_masalah', $pica->akar_masalah)); ?></textarea>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-secondary">Tindakan Koreksi (Corrective Action)</label>
                                                                <textarea name="tindakan_koreksi" rows="2" class="form-control bg-light" 
                                                                    placeholder="Tindakan langsung perbaikan..."><?php echo e(old('tindakan_koreksi', $pica->tindakan_koreksi)); ?></textarea>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-secondary">Tindakan Pencegahan (Preventive Action)</label>
                                                                <textarea name="tindakan_pencegahan" rows="2" class="form-control bg-light" 
                                                                    placeholder="Tindakan agar temuan tidak terulang..."><?php echo e(old('tindakan_pencegahan', $pica->tindakan_pencegahan)); ?></textarea>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-secondary">PIC Penanggung Jawab Perbaikan</label>
                                                                <input type="text" name="pic_perbaikan" class="form-control bg-light" 
                                                                    placeholder="Nama PIC perbaikan..." value="<?php echo e(old('pic_perbaikan', $pica->pic_perbaikan)); ?>">
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-secondary">Tenggat Waktu Selesai (Target Date)</label>
                                                                <input type="date" name="tenggat_waktu" class="form-control bg-light" 
                                                                    value="<?php echo e(old('tenggat_waktu', $pica->tenggat_waktu ? $pica->tenggat_waktu->format('Y-m-d') : '')); ?>">
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-secondary">Status PICA <span class="text-danger">*</span></label>
                                                                <select name="status" class="form-select bg-light fw-bold">
                                                                    <option value="open" <?php echo e(old('status', $pica->status) == 'open' ? 'selected' : ''); ?>>🔴 Open (Belum Ditindaklanjuti)</option>
                                                                    <option value="in_progress" <?php echo e(old('status', $pica->status) == 'in_progress' ? 'selected' : ''); ?>>🟡 In Progress (Dalam Perbaikan)</option>
                                                                    <option value="closed" <?php echo e(old('status', $pica->status) == 'closed' ? 'selected' : ''); ?>>🟢 Closed (Selesai Verifikasi)</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-secondary">Catatan Verifikasi / Pengawasan (Admin)</label>
                                                                <textarea name="catatan_verifikasi_auditor" rows="2" class="form-control bg-light" 
                                                                    placeholder="Wajib diisi saat status diubah ke Closed..."><?php echo e(old('catatan_verifikasi_auditor', $pica->catatan_verifikasi_auditor)); ?></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="mt-3 text-end">
                                                            <button type="submit" class="btn btn-danger px-4 rounded-3 fw-semibold">
                                                                <i class="bi bi-save me-1"></i> Simpan PICA Sub-Elemen <?php echo e($subKode); ?>

                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
    function toggleJustifikasi(picaId) {
        const select = document.getElementById('kategoriSelect' + picaId);
        const container = document.getElementById('justifikasiContainer' + picaId);
        if (select && container) {
            if (select.value === 'kritikal') {
                container.style.display = 'block';
            } else {
                container.style.display = 'block';
            }
        }
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('kategori-select-toggle')) {
            const picaId = e.target.dataset.picaId;
            if (picaId) toggleJustifikasi(picaId);
        }
    });
</script>
<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
