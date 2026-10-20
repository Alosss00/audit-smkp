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

<!-- Quick Company Selector Navigation (Semua Perusahaan, MSM, TTN, dsb) -->
<div class="d-flex align-items-center gap-2 mb-3 overflow-auto pb-1">
    <span class="text-muted small fw-semibold me-1"><i class="bi bi-building me-1 text-danger"></i>Perusahaan:</span>
    <a href="<?php echo e(route('admin.pica.index', array_merge(request()->except(['perusahaan_id', 'page']), []))); ?>" 
       class="btn btn-sm rounded-pill px-3 fw-semibold <?php echo e(!request('perusahaan_id') ? 'btn-danger shadow-sm text-white' : 'btn-outline-secondary bg-white text-slate-700'); ?>">
        Semua Perusahaan
    </a>
    <?php foreach($perusahaans as $comp): ?>
        <?php
            $isSelected = request('perusahaan_id') == $comp->id;
        ?>
        <a href="<?php echo e(route('admin.pica.index', array_merge(request()->except(['page']), ['perusahaan_id' => $comp->id]))); ?>" 
           class="btn btn-sm rounded-pill px-3 fw-semibold <?php echo e($isSelected ? 'btn-danger shadow-sm text-white' : 'btn-outline-secondary bg-white text-slate-700'); ?>">
            🏢 <?php echo e($comp->nama_perusahaan); ?>

        </a>
    <?php endforeach; ?>
</div>

<!-- Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form action="<?php echo e(route('admin.pica.index')); ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-lg-3 col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control bg-light border-start-0" 
                    placeholder="Cari area, temuan, PIC..." value="<?php echo e(request('search')); ?>">
            </div>
        </div>
        <div class="col-lg-2 col-md-6">
            <select name="perusahaan_id" class="form-select bg-light select-searchable" data-auto-submit="true">
                <option value="">🏢 Semua Perusahaan</option>
                <?php foreach($perusahaans as $comp): ?>
                    <option value="<?php echo e($comp->id); ?>" <?php echo e(request('perusahaan_id') == $comp->id ? 'selected' : ''); ?>>
                        🏢 <?php echo e($comp->nama_perusahaan); ?> <?php if($comp->kategori): ?> (<?php echo e($comp->kategori); ?>) <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select name="tahun_periode" class="form-select bg-light" data-auto-submit="true">
                <option value="">🗓️ Semua Periode Tahun</option>
                <?php foreach($tahunPeriodes as $yr): ?>
                    <option value="<?php echo e($yr); ?>" <?php echo e(request('tahun_periode') == $yr ? 'selected' : ''); ?>>
                        Tahun <?php echo e($yr); ?>

                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select name="user_id" class="form-select bg-light" data-auto-submit="true">
                <option value="">👤 Semua Auditor</option>
                <?php foreach($auditors as $auditor): ?>
                    <option value="<?php echo e($auditor->id); ?>" <?php echo e(request('user_id') == $auditor->id ? 'selected' : ''); ?>>
                        <?php echo e($auditor->name); ?>

                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select name="status" class="form-select bg-light" data-auto-submit="true">
                <option value="">⚡ Semua Status PICA</option>
                <option value="open" <?php echo e(request('status') == 'open' ? 'selected' : ''); ?>>Ada Temuan Open</option>
                <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>Ada Temuan In Progress</option>
                <option value="closed" <?php echo e(request('status') == 'closed' ? 'selected' : ''); ?>>Seluruh Temuan Closed</option>
            </select>
        </div>
        <div class="col-lg-1 col-md-12 d-flex gap-1">
            <button type="submit" class="btn btn-danger w-100 fw-semibold px-2" title="Terapkan Filter">
                <i class="bi bi-filter"></i>
            </button>
            <?php if(request()->hasAny(['search', 'status', 'user_id', 'perusahaan_id', 'tahun_periode'])): ?>
                <a href="<?php echo e(route('admin.pica.index')); ?>" class="btn btn-outline-secondary px-2" title="Reset Filter">
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
                    <th>Perusahaan, Area & Periode Tahun</th>
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
                                <div class="mb-1">
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-0.5 small fw-bold d-inline-flex align-items-center">
                                        <i class="bi bi-calendar-check me-1"></i>Periode Tahun <?php echo e($sesi->tahun_periode ?? $sesi->tanggal_mulai->format('Y')); ?>

                                    </span>
                                </div>
                                <strong class="d-block text-slate-800 fs-6"><?php echo e($sesi->area_audit); ?></strong>
                                <div class="text-muted small mt-0.5" style="font-size: 0.78rem;">
                                    <i class="bi bi-calendar-event me-1 text-secondary"></i>Waktu Pelaksanaan: <span class="fw-medium text-slate-700"><?php echo e($sesi->tanggal_mulai->format('d M Y')); ?> - <?php echo e($sesi->tanggal_selesai->format('d M Y')); ?></span>
                                </div>
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

                            <span class="badge bg-danger border border-light border-opacity-50 ms-1 fw-bold fs-6">
                                <i class="bi bi-calendar-check me-1"></i>Periode Tahun: <?php echo e($sesi->tahun_periode ?? $sesi->tanggal_mulai->format('Y')); ?>

                            </span>
                        </h5>
                        <small class="text-slate-300" style="font-size: 0.8rem; color: #cbd5e1;">
                            <i class="bi bi-person me-1"></i> Auditor: <?php echo e($auditor ? $auditor->name : '-'); ?> | 
                            <i class="bi bi-calendar-event me-1"></i> Waktu Pelaksanaan: <?php echo e($sesi->tanggal_mulai->format('d M Y')); ?> - <?php echo e($sesi->tanggal_selesai->format('d M Y')); ?> | 
                            <strong>Total <?php echo e($subElemenCount); ?> Temuan Sub-Elemen (Maks. 51)</strong> &bull; <?php echo e($totalPicaCount); ?> Tindakan Kriteria
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    
                    <div id="picaModalAlert_<?php echo e($sesi->id); ?>" style="display: none;"></div>

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
                                    || ($subElemen && ($subElemen->relationLoaded('kriterias') ? $subElemen->kriterias->count() : $subElemen->kriterias()->count()) > 1) 
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

                            <div class="accordion-item border rounded-3 mb-3 shadow-sm overflow-hidden sub-elemen-accordion-item" data-sesi-id="<?php echo e($sesi->id); ?>" data-sub-elemen-id="<?php echo e($subElemenId); ?>" id="subElemenItem_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>">
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
                                                    <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1 small fw-bold sub-kriteria-counter" id="subCounter_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>" data-total="<?php echo e($kriTotalCount); ?>">
                                                        <i class="bi bi-diagram-3 me-1"></i><?php echo e($kriTotalCount); ?> Sub-Sub Kriteria
                                                        <span class="sub-counter-closed <?php echo e($kriClosedCount > 0 && $kriClosedCount < $kriTotalCount ? 'text-success ms-1' : 'd-none'); ?>">(<span class="closed-num"><?php echo e($kriClosedCount); ?></span>/<?php echo e($kriTotalCount); ?> Closed)</span>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-1 small">
                                                        Penilaian Langsung
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if($subKategori === 'kritikal'): ?>
                                                    <span class="badge bg-danger rounded-pill px-3 py-1 badge-sub-kat-<?php echo e($sesi->id); ?>-<?php echo e($subElemenId); ?> <?php echo e(!$hasSubSub && $firstDetail->pica ? 'badge-kategori-pica-'.$firstDetail->pica->id : ''); ?>" id="badgeSubKategori_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>"><i class="bi bi-shield-exclamation me-1"></i> Kritikal</span>
                                                <?php elseif($subKategori === 'mayor'): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1 badge-sub-kat-<?php echo e($sesi->id); ?>-<?php echo e($subElemenId); ?> <?php echo e(!$hasSubSub && $firstDetail->pica ? 'badge-kategori-pica-'.$firstDetail->pica->id : ''); ?>" id="badgeSubKategori_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>"><i class="bi bi-exclamation-triangle-fill me-1"></i> Mayor</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info text-white rounded-pill px-3 py-1 badge-sub-kat-<?php echo e($sesi->id); ?>-<?php echo e($subElemenId); ?> <?php echo e(!$hasSubSub && $firstDetail->pica ? 'badge-kategori-pica-'.$firstDetail->pica->id : ''); ?>" id="badgeSubKategori_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>"><i class="bi bi-info-circle-fill me-1"></i> Minor</span>
                                                <?php endif; ?>

                                                <?php if($isSubOverdue): ?>
                                                    <span class="badge bg-danger badge-sub-overdue-<?php echo e($sesi->id); ?>-<?php echo e($subElemenId); ?> <?php echo e(!$hasSubSub && $firstDetail->pica ? 'badge-overdue-pica-'.$firstDetail->pica->id : ''); ?>" id="badgeSubOverdue_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>">Overdue</span>
                                                <?php endif; ?>

                                                <?php if($subStatus === 'open'): ?>
                                                    <span class="badge bg-danger rounded-pill px-3 py-1 badge-sub-status-<?php echo e($sesi->id); ?>-<?php echo e($subElemenId); ?> <?php echo e(!$hasSubSub && $firstDetail->pica ? 'badge-status-pica-'.$firstDetail->pica->id : ''); ?>" id="badgeSubStatus_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>">Open</span>
                                                <?php elseif($subStatus === 'in_progress'): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1 badge-sub-status-<?php echo e($sesi->id); ?>-<?php echo e($subElemenId); ?> <?php echo e(!$hasSubSub && $firstDetail->pica ? 'badge-status-pica-'.$firstDetail->pica->id : ''); ?>" id="badgeSubStatus_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>">In Progress</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success rounded-pill px-3 py-1 badge-sub-status-<?php echo e($sesi->id); ?>-<?php echo e($subElemenId); ?> <?php echo e(!$hasSubSub && $firstDetail->pica ? 'badge-status-pica-'.$firstDetail->pica->id : ''); ?>" id="badgeSubStatus_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>">Closed</span>
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
                                                                        <span class="badge <?php echo e($pica->kategori_temuan === 'kritikal' ? 'bg-danger' : ($pica->kategori_temuan === 'mayor' ? 'bg-warning text-dark' : 'bg-info text-white')); ?> rounded-pill px-2.5 py-1 small badge-kategori-pica-<?php echo e($pica->id); ?>" id="badgeKategori_<?php echo e($pica->id); ?>">
                                                                            <?php echo e(ucfirst($pica->kategori_temuan)); ?>
                                                                        </span>

                                                                        <?php if($isOverdue): ?>
                                                                            <span class="badge bg-danger small badge-overdue-pica-<?php echo e($pica->id); ?>">Overdue</span>
                                                                        <?php endif; ?>

                                                                        <span class="badge <?php echo e($pica->status === 'open' ? 'bg-danger' : ($pica->status === 'in_progress' ? 'bg-warning text-dark' : 'bg-success')); ?> rounded-pill px-2.5 py-1 small badge-status-pica-<?php echo e($pica->id); ?>" id="badgeStatus_<?php echo e($pica->id); ?>">
                                                                            <?php echo e($pica->status === 'in_progress' ? 'In Progress' : ucfirst($pica->status)); ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h3>
                                                        <div id="adminNestedCollapse<?php echo e($pica->id); ?>" class="accordion-collapse collapse <?php echo e($kriIndex == 0 ? 'show' : ''); ?>"
                                                            data-bs-parent="#adminNestedAccordion_<?php echo e($sesi->id); ?>_<?php echo e($subElemenId); ?>">
                                                            <div class="accordion-body bg-white p-4 border-top">
                                                                
                                                                <form action="<?php echo e(route('admin.pica.update', $pica->id)); ?>" method="POST" class="pica-form-item" data-sesi-id="<?php echo e($sesi->id); ?>" data-pica-id="<?php echo e($pica->id); ?>" id="formPica_<?php echo e($pica->id); ?>">
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
                                                                            <select name="status" class="form-select bg-light fw-bold status-select-toggle" data-pica-id="<?php echo e($pica->id); ?>" id="statusSelect_<?php echo e($pica->id); ?>">
                                                                                <option value="open" <?php echo e(old('status', $pica->status) == 'open' ? 'selected' : ''); ?>>🔴 Open (Belum Ditindaklanjuti)</option>
                                                                                <option value="in_progress" <?php echo e(old('status', $pica->status) == 'in_progress' ? 'selected' : ''); ?>>🟡 In Progress (Dalam Perbaikan)</option>
                                                                                <option value="closed" <?php echo e(old('status', $pica->status) == 'closed' ? 'selected' : ''); ?>>🟢 Closed (Selesai Verifikasi)</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label fw-semibold small text-secondary">
                                                                                Catatan Verifikasi / Pengawasan (Admin)
                                                                                <span class="text-danger fw-bold verification-required-star" id="starVerif_<?php echo e($pica->id); ?>" style="<?php echo e(old('status', $pica->status) == 'closed' ? '' : 'display: none;'); ?>">* (Wajib diisi saat Closed)</span>
                                                                            </label>
                                                                            <textarea name="catatan_verifikasi_auditor" id="catatanVerif_<?php echo e($pica->id); ?>" rows="2" class="form-control bg-light" 
                                                                                placeholder="Wajib diisi saat status diubah ke Closed..."><?php echo e(old('catatan_verifikasi_auditor', $pica->catatan_verifikasi_auditor)); ?></textarea>
                                                                            <div class="text-danger small mt-1 d-none" id="errorMsgVerif_<?php echo e($pica->id); ?>">
                                                                                <i class="bi bi-exclamation-circle-fill me-1"></i>Catatan verifikasi auditor wajib diisi untuk mengubah status ke Closed.
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="mt-3 text-end">
                                                                        <button type="submit" class="btn btn-danger px-4 rounded-3 fw-semibold btn-simpan-pica-single shadow-sm" data-pica-id="<?php echo e($pica->id); ?>" id="btnSimpanPica_<?php echo e($pica->id); ?>">
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
                                                    <form action="<?php echo e(route('admin.pica.update', $pica->id)); ?>" method="POST" class="pica-form-item" data-sesi-id="<?php echo e($sesi->id); ?>" data-pica-id="<?php echo e($pica->id); ?>" id="formPica_<?php echo e($pica->id); ?>">
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
                                                                <select name="status" class="form-select bg-light fw-bold status-select-toggle" data-pica-id="<?php echo e($pica->id); ?>" id="statusSelect_<?php echo e($pica->id); ?>">
                                                                    <option value="open" <?php echo e(old('status', $pica->status) == 'open' ? 'selected' : ''); ?>>🔴 Open (Belum Ditindaklanjuti)</option>
                                                                    <option value="in_progress" <?php echo e(old('status', $pica->status) == 'in_progress' ? 'selected' : ''); ?>>🟡 In Progress (Dalam Perbaikan)</option>
                                                                    <option value="closed" <?php echo e(old('status', $pica->status) == 'closed' ? 'selected' : ''); ?>>🟢 Closed (Selesai Verifikasi)</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold small text-secondary">
                                                                    Catatan Verifikasi / Pengawasan (Admin)
                                                                    <span class="text-danger fw-bold verification-required-star" id="starVerif_<?php echo e($pica->id); ?>" style="<?php echo e(old('status', $pica->status) == 'closed' ? '' : 'display: none;'); ?>">* (Wajib diisi saat Closed)</span>
                                                                </label>
                                                                <textarea name="catatan_verifikasi_auditor" id="catatanVerif_<?php echo e($pica->id); ?>" rows="2" class="form-control bg-light" 
                                                                    placeholder="Wajib diisi saat status diubah ke Closed..."><?php echo e(old('catatan_verifikasi_auditor', $pica->catatan_verifikasi_auditor)); ?></textarea>
                                                                <div class="text-danger small mt-1 d-none" id="errorMsgVerif_<?php echo e($pica->id); ?>">
                                                                    <i class="bi bi-exclamation-circle-fill me-1"></i>Catatan verifikasi auditor wajib diisi untuk mengubah status ke Closed.
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mt-3 text-end">
                                                            <button type="submit" class="btn btn-danger px-4 rounded-3 fw-semibold btn-simpan-pica-single shadow-sm" data-pica-id="<?php echo e($pica->id); ?>" id="btnSimpanPica_<?php echo e($pica->id); ?>">
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
                <div class="modal-footer bg-light rounded-bottom-4 d-flex justify-content-between align-items-center">
                    <div class="text-muted small d-none d-md-flex align-items-center gap-2">
                        <i class="bi bi-info-circle text-primary fs-6"></i>
                        <span>Perubahan form dapat disimpan per kriteria atau <strong>sekaligus seluruhnya</strong>.</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Tutup
                        </button>
                        <button type="button" class="btn btn-danger px-4 rounded-3 fw-semibold btn-simpan-semua-pica shadow-sm" data-sesi-id="<?php echo e($sesi->id); ?>" id="btnSimpanSemua_<?php echo e($sesi->id); ?>">
                            <i class="bi bi-save2-fill me-1"></i> Simpan Semua PICA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
    let hasMadeChangesInModal = false;

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

        // Live validation and visual cue when status is set to closed
        if (e.target && e.target.classList.contains('status-select-toggle')) {
            const picaId = e.target.dataset.picaId;
            const star = document.getElementById('starVerif_' + picaId);
            const textarea = document.getElementById('catatanVerif_' + picaId);
            const errorMsg = document.getElementById('errorMsgVerif_' + picaId);

            if (e.target.value === 'closed') {
                if (star) star.style.display = 'inline';
                if (textarea) {
                    textarea.placeholder = 'Catatan verifikasi auditor wajib diisi sebelum menyimpan dengan status Closed...';
                    if (!textarea.value.trim()) {
                        textarea.classList.add('border-danger');
                    }
                }
            } else {
                if (star) star.style.display = 'none';
                if (textarea) {
                    textarea.classList.remove('border-danger', 'is-invalid');
                    textarea.placeholder = 'Wajib diisi saat status diubah ke Closed...';
                }
                if (errorMsg) errorMsg.classList.add('d-none');
            }
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target && e.target.name === 'catatan_verifikasi_auditor') {
            const picaId = e.target.id ? e.target.id.replace('catatanVerif_', '') : null;
            if (e.target.value.trim().length > 0) {
                e.target.classList.remove('border-danger', 'is-invalid');
                if (picaId) {
                    const errorMsg = document.getElementById('errorMsgVerif_' + picaId);
                    if (errorMsg) errorMsg.classList.add('d-none');
                }
            }
        }
    });

    // Helper: Update status & category badges in the UI and recalculate parent Sub-Elemen
    function updatePicaBadges(picaId, status, kategori) {
        if (status) {
            const statusBadges = document.querySelectorAll('#badgeStatus_' + picaId + ', .badge-status-pica-' + picaId);
            statusBadges.forEach(b => {
                b.className = 'badge rounded-pill px-2.5 py-1 small ' + 
                    (status === 'open' ? 'bg-danger' : (status === 'in_progress' ? 'bg-warning text-dark' : 'bg-success'));
                b.textContent = status === 'in_progress' ? 'In Progress' : (status.charAt(0).toUpperCase() + status.slice(1));
            });

            // If closed, hide overdue badge
            if (status === 'closed') {
                document.querySelectorAll('.badge-overdue-pica-' + picaId).forEach(ob => {
                    ob.style.display = 'none';
                });
            }
        }
        if (kategori) {
            const katBadges = document.querySelectorAll('#badgeKategori_' + picaId + ', .badge-kategori-pica-' + picaId);
            katBadges.forEach(b => {
                b.className = 'badge rounded-pill px-2.5 py-1 small ' + 
                    (kategori === 'kritikal' ? 'bg-danger' : (kategori === 'mayor' ? 'bg-warning text-dark' : 'bg-info text-white'));
                b.textContent = kategori.charAt(0).toUpperCase() + kategori.slice(1);
            });
        }

        // Recalculate parent Sub-Elemen status & counter
        const form = document.getElementById('formPica_' + picaId);
        if (form) {
            const subElemenItem = form.closest('.sub-elemen-accordion-item');
            if (subElemenItem) {
                const sid = subElemenItem.dataset.sesiId;
                const subId = subElemenItem.dataset.subElemenId;
                const childForms = Array.from(subElemenItem.querySelectorAll('form.pica-form-item'));

                if (childForms.length > 0) {
                    let countClosed = 0;
                    let countProgress = 0;
                    let countOpen = 0;

                    childForms.forEach(cf => {
                        const stVal = cf.querySelector('select[name="status"]')?.value;
                        if (stVal === 'closed') countClosed++;
                        else if (stVal === 'in_progress') countProgress++;
                        else countOpen++;
                    });

                    // Update Sub-Elemen Status Badge
                    const subBadge = document.getElementById(`badgeSubStatus_${sid}_${subId}`);
                    if (subBadge) {
                        if (countClosed === childForms.length) {
                            subBadge.className = `badge bg-success rounded-pill px-3 py-1 badge-sub-status-${sid}-${subId}`;
                            subBadge.textContent = 'Closed';
                            const subOverdue = document.getElementById(`badgeSubOverdue_${sid}_${subId}`);
                            if (subOverdue) subOverdue.style.display = 'none';
                        } else if (countProgress > 0) {
                            subBadge.className = `badge bg-warning text-dark rounded-pill px-3 py-1 badge-sub-status-${sid}-${subId}`;
                            subBadge.textContent = 'In Progress';
                        } else {
                            subBadge.className = `badge bg-danger rounded-pill px-3 py-1 badge-sub-status-${sid}-${subId}`;
                            subBadge.textContent = 'Open';
                        }
                    }

                    // Update Sub-Elemen Counter (X/Y Closed)
                    const counterBox = document.getElementById(`subCounter_${sid}_${subId}`);
                    if (counterBox) {
                        const closedWrap = counterBox.querySelector('.sub-counter-closed');
                        const closedNum = counterBox.querySelector('.closed-num');
                        if (closedWrap && closedNum) {
                            closedNum.textContent = countClosed;
                            if (countClosed > 0 && countClosed < childForms.length) {
                                closedWrap.className = 'sub-counter-closed text-success ms-1';
                            } else {
                                closedWrap.className = 'sub-counter-closed d-none';
                            }
                        }
                    }
                }
            }
        }
    }

    // Helper: Show floating alert inside the modal
    function showModalAlert(sesiId, type, message) {
        const container = document.getElementById('picaModalAlert_' + sesiId);
        if (!container) return;

        const icon = type === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger';
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';

        container.className = `alert ${alertClass} alert-dismissible fade show d-flex align-items-center gap-2 rounded-3 shadow-sm py-2.5 px-3 mb-3`;
        container.innerHTML = `
            <i class="bi ${icon} fs-5 flex-shrink-0"></i>
            <div class="flex-grow-1 small">${message}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        container.style.display = 'flex';

        // Auto scroll smoothly to the alert
        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        if (type === 'success') {
            setTimeout(() => {
                if (container && container.style.display !== 'none') {
                    container.style.display = 'none';
                }
            }, 6000);
        }
    }

    // Helper: Safely close a collapse element
    function closeCollapse(el) {
        if (!el) return;
        if (window.bootstrap && bootstrap.Collapse) {
            const instance = bootstrap.Collapse.getInstance(el) || new bootstrap.Collapse(el, { toggle: false });
            instance.hide();
        } else {
            el.classList.remove('show');
            const btn = el.previousElementSibling ? el.previousElementSibling.querySelector('button[data-bs-toggle="collapse"]') : null;
            if (btn) {
                btn.classList.add('collapsed');
                btn.setAttribute('aria-expanded', 'false');
            }
        }
    }

    // Helper: Safely open a collapse element
    function openCollapse(el) {
        if (!el) return;
        if (window.bootstrap && bootstrap.Collapse) {
            const instance = bootstrap.Collapse.getInstance(el) || new bootstrap.Collapse(el, { toggle: false });
            instance.show();
        } else {
            el.classList.add('show');
            const btn = el.previousElementSibling ? el.previousElementSibling.querySelector('button[data-bs-toggle="collapse"]') : null;
            if (btn) {
                btn.classList.remove('collapsed');
                btn.setAttribute('aria-expanded', 'true');
            }
        }
    }

    // Helper: Close current PICA dropdown and automatically open the next finding
    function autoAdvanceToNextPica(form, sesiId) {
        const modal = form.closest('.modal') || document.getElementById('adminAreaPicaModal' + sesiId);
        if (!modal) return;

        const allForms = Array.from(modal.querySelectorAll('form.pica-form-item'));
        const currentIndex = allForms.indexOf(form);

        // 1. Tutup collapse / dropdown item saat ini
        const currentCollapseEl = form.closest('.accordion-collapse');
        if (currentCollapseEl) {
            closeCollapse(currentCollapseEl);
        }

        // 2. Buka temuan berikutnya jika ada
        if (currentIndex !== -1 && currentIndex + 1 < allForms.length) {
            const nextForm = allForms[currentIndex + 1];

            // Kumpulkan semua collapse induk dari nextForm (level Sub-Elemen hingga Sub-Sub Kriteria)
            const collapsesToOpen = [];
            let parentEl = nextForm.parentElement;
            while (parentEl && parentEl !== modal) {
                if (parentEl.classList.contains('accordion-collapse')) {
                    collapsesToOpen.push(parentEl);
                }
                parentEl = parentEl.parentElement;
            }

            // Buka dari yang paling luar (Sub-Elemen) ke yang dalam (Sub-Sub Kriteria)
            collapsesToOpen.reverse().forEach(collEl => {
                openCollapse(collEl);
            });

            // Scroll halus ke header atau form temuan berikutnya
            const nextCollapseEl = nextForm.closest('.accordion-collapse');
            const targetToScroll = (nextCollapseEl && nextCollapseEl.previousElementSibling) ? nextCollapseEl.previousElementSibling : nextForm;
            setTimeout(() => {
                targetToScroll.scrollIntoView({ behavior: 'smooth', block: 'start' });
                const firstInput = nextForm.querySelector('select:not([disabled]), textarea:not([disabled]), input:not([type="hidden"]):not([disabled])');
                if (firstInput) {
                    firstInput.focus({ preventScroll: true });
                }
            }, 300);
        } else {
            showModalAlert(sesiId, 'success', `<strong>Selesai!</strong> Data PICA berhasil disimpan. Seluruh temuan pada sesi audit ini telah tercapai.`);
        }
    }

    // 1. Single PICA Form AJAX Submission
    document.addEventListener('submit', function(e) {
        const form = e.target.closest('form.pica-form-item');
        if (!form) return;

        e.preventDefault();

        const picaId = form.dataset.picaId;
        const sesiId = form.dataset.sesiId;
        const statusSelect = form.querySelector('select[name="status"]');
        const catatanTextarea = form.querySelector('textarea[name="catatan_verifikasi_auditor"]');
        const errorMsg = document.getElementById('errorMsgVerif_' + picaId);

        // Client-side validation: Closed status MUST have catatan_verifikasi_auditor
        if (statusSelect && statusSelect.value === 'closed' && (!catatanTextarea || !catatanTextarea.value.trim())) {
            if (catatanTextarea) {
                catatanTextarea.classList.add('is-invalid');
                catatanTextarea.focus();
            }
            if (errorMsg) errorMsg.classList.remove('d-none');
            showModalAlert(sesiId, 'danger', '<strong>Validasi Gagal:</strong> Status diubah ke <strong>Closed</strong>, namun <strong>Catatan Verifikasi Auditor</strong> masih kosong. Silakan isi catatan verifikasi terlebih dahulu agar status dapat ditutup.');
            return;
        }

        if (catatanTextarea) {
            catatanTextarea.classList.remove('is-invalid', 'border-danger');
        }
        if (errorMsg) errorMsg.classList.add('d-none');

        const submitBtn = form.querySelector('button[type="submit"]');
        const origBtnHtml = submitBtn ? submitBtn.innerHTML : '';
        const origBtnClass = submitBtn ? submitBtn.className : '';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
        }

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                let errMsg = data.message || 'Gagal menyimpan data PICA.';
                if (data.errors) {
                    const firstErr = Object.values(data.errors)[0];
                    if (firstErr) errMsg = typeof firstErr === 'string' ? firstErr : firstErr[0];
                }
                throw new Error(errMsg);
            }
            return data;
        })
        .then(data => {
            hasMadeChangesInModal = true;

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.className = 'btn btn-success px-4 rounded-3 fw-semibold shadow-sm';
                submitBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Tersimpan! ✓';
                setTimeout(() => {
                    submitBtn.className = origBtnClass;
                    submitBtn.innerHTML = origBtnHtml;
                }, 2500);
            }

            if (data.pica) {
                updatePicaBadges(data.pica.id, data.pica.status, data.pica.kategori_temuan);
            }

            showModalAlert(sesiId, 'success', `<strong>Berhasil!</strong> ${data.message || 'Data PICA berhasil disimpan.'}`);

            // Tutup dropdown PICA ini dan buka temuan berikutnya secara otomatis
            setTimeout(() => {
                autoAdvanceToNextPica(form, sesiId);
            }, 450);
        })
        .catch(err => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.className = origBtnClass;
                submitBtn.innerHTML = origBtnHtml;
            }
            if (err.message && err.message.toLowerCase().includes('catatan verifikasi')) {
                if (catatanTextarea) {
                    catatanTextarea.classList.add('is-invalid');
                    catatanTextarea.focus();
                }
                if (errorMsg) errorMsg.classList.remove('d-none');
            }
            showModalAlert(sesiId, 'danger', `<strong>Peringatan Validasi:</strong> ${err.message}`);
        });
    });

    // 2. Batch Save All PICA for Session
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-simpan-semua-pica');
        if (!btn) return;

        const sesiId = btn.dataset.sesiId;
        const modal = document.getElementById('adminAreaPicaModal' + sesiId);
        if (!modal) return;

        const forms = modal.querySelectorAll('form.pica-form-item');
        if (forms.length === 0) return;

        // Pre-validate: ensure any form with status Closed has catatan_verifikasi_auditor
        let invalidClosedForm = null;
        forms.forEach(f => {
            const st = f.querySelector('select[name="status"]')?.value;
            const note = f.querySelector('textarea[name="catatan_verifikasi_auditor"]')?.value.trim();
            if (st === 'closed' && !note && !invalidClosedForm) {
                invalidClosedForm = f;
            }
        });

        if (invalidClosedForm) {
            const invalidPicaId = invalidClosedForm.dataset.picaId;
            const invalidTextarea = invalidClosedForm.querySelector('textarea[name="catatan_verifikasi_auditor"]');
            const invalidErrorMsg = document.getElementById('errorMsgVerif_' + invalidPicaId);
            
            // Expand accordion containing this invalid form
            const collEl = invalidClosedForm.closest('.accordion-collapse');
            if (collEl) openCollapse(collEl);

            if (invalidTextarea) {
                invalidTextarea.classList.add('is-invalid');
                setTimeout(() => invalidTextarea.focus(), 300);
            }
            if (invalidErrorMsg) invalidErrorMsg.classList.remove('d-none');

            showModalAlert(sesiId, 'danger', '<strong>Validasi Simpan Semua:</strong> Terdapat temuan dengan status <strong>Closed</strong> yang belum memiliki <strong>Catatan Verifikasi Auditor</strong>. Mohon lengkapi catatan verifikasi terlebih dahulu.');
            return;
        }

        const origBtnHtml = btn.innerHTML;
        const origBtnClass = btn.className;

        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan Semua (${forms.length} Form)...`;

        const picas = [];
        forms.forEach(f => {
            const fd = new FormData(f);
            const picaObj = { id: f.dataset.picaId };
            for (let [k, v] of fd.entries()) {
                if (k !== '_token' && k !== '_method') {
                    picaObj[k] = v;
                }
            }
            picas.push(picaObj);
        });

        fetch('<?php echo route('admin.pica.batch-update'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sesi_id: sesiId,
                picas: picas
            })
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                let errMsg = data.message || 'Gagal menyimpan seluruh data PICA.';
                if (data.errors) {
                    const firstErr = Object.values(data.errors)[0];
                    if (firstErr) errMsg = typeof firstErr === 'string' ? firstErr : firstErr[0];
                }
                throw new Error(errMsg);
            }
            return data;
        })
        .then(data => {
            hasMadeChangesInModal = true;

            btn.disabled = false;
            btn.className = 'btn btn-success px-4 rounded-3 fw-semibold shadow-sm';
            btn.innerHTML = '<i class="bi bi-check2-all me-1"></i> Semua PICA Tersimpan! ✓';

            setTimeout(() => {
                btn.className = origBtnClass;
                btn.innerHTML = origBtnHtml;
            }, 3000);

            if (data.picas) {
                Object.values(data.picas).forEach(p => {
                    updatePicaBadges(p.id, p.status, p.kategori_temuan);
                });
            }

            showModalAlert(sesiId, 'success', `<strong>Berhasil Disimpan!</strong> ${data.message || 'Seluruh data pengisian PICA pada sesi ini telah berhasil diperbarui.'}`);
        })
        .catch(err => {
            btn.disabled = false;
            btn.className = origBtnClass;
            btn.innerHTML = origBtnHtml;
            showModalAlert(sesiId, 'danger', `<strong>Peringatan Validasi:</strong> ${err.message}`);
        });
    });

    // Refresh background table & statistics when modal closes if changes were saved
    document.addEventListener('hidden.bs.modal', function(e) {
        if (hasMadeChangesInModal && e.target && e.target.classList.contains('modal')) {
            window.location.reload();
        }
    });

    <?php if(session('open_pica_modal')): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('adminAreaPicaModal<?php echo e(session('open_pica_modal')); ?>');
        if (modalEl) {
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        }
    });
    <?php endif; ?>
</script>
<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
