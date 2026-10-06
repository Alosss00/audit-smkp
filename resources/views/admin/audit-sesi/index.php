<?php
$title = 'Sesi Audit Saya — Admin SMKP Minerba';
ob_start();
?>
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-journal-text text-danger me-2"></i>Sesi Audit Saya
        </h2>
        <p class="text-muted mb-0">Kelola sesi penilaian matriks SMKP yang Anda buat sendiri sebagai administrator.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?php echo e(route('admin.audit-sesi.create')); ?>" class="btn btn-danger rounded-3 shadow-sm px-3 py-2 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> Buat Sesi Audit Baru
        </a>
    </div>
</div>

<div class="card card-custom p-3 mb-4">
    <form method="GET" action="<?php echo e(route('admin.audit-sesi.index')); ?>" class="row g-2 align-items-center">
        <div class="col-md-5">
            <select name="perusahaan_id" class="form-select select-searchable rounded-3" placeholder="-- Cari perusahaan --" data-auto-submit="true">
                <option value="">-- Pilih Perusahaan Area Audit (Semua) --</option>
                <?php foreach($perusahaans as $comp): ?>
                    <option value="<?php echo e($comp->id); ?>" <?php echo e((request('perusahaan_id') == $comp->id || request('area_selection') == 'p:'.$comp->id) ? 'selected' : ''); ?>>
                        <?php echo e($comp->nama_perusahaan); ?> <?php if($comp->kategori): ?> (<?php echo e($comp->kategori); ?>) <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select rounded-3" data-auto-submit="true">
                <option value="">-- Semua Status Sesi --</option>
                <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                <option value="berjalan" <?php echo e(request('status') == 'berjalan' ? 'selected' : ''); ?>>Berjalan</option>
                <option value="selesai" <?php echo e(request('status') == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
            </select>
        </div>
        <?php if(request()->filled('status') || request()->filled('perusahaan_id') || request()->filled('area_selection')): ?>
            <div class="col-md-2">
                <a href="<?php echo e(route('admin.audit-sesi.index')); ?>" class="btn btn-outline-secondary rounded-3 w-100">Reset</a>
            </div>
        <?php endif; ?>
    </form>
</div>

<div class="card card-custom p-4">
    <?php if($auditSesis->isEmpty()): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox display-4 d-block mb-3 opacity-50"></i>
            <p class="mb-2 fs-5 fw-semibold">Belum ada sesi audit.</p>
            <p class="small text-muted mb-3">Klik tombol di bawah ini untuk membuat sesi audit internal baru.</p>
            <a href="<?php echo e(route('admin.audit-sesi.create')); ?>" class="btn btn-danger rounded-3 px-4">
                <i class="bi bi-plus-lg me-1"></i> Buat Sesi Pertama
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Tahun Periode</th>
                        <th>Pelaksanaan Audit</th>
                        <th>Perusahaan (Area Audit)</th>
                        <th>Status</th>
                        <th class="text-center">Nilai Audit Sesi</th>
                        <th>Nilai Audit</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($auditSesis as $index => $sesi): ?>
                        <?php
                            $thn = $sesi->tahun_periode ?? $sesi->tanggal_mulai->format('Y');
                        ?>
                        <tr>
                            <td><?php echo e($auditSesis->firstItem() + $index); ?></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small fw-semibold w-fit mb-1">
                                        <i class="bi bi-calendar-check me-1"></i>Tahun <?php echo e($thn); ?>
                                    </span>
                                    <span class="text-muted small" style="font-size: 0.78rem;">
                                        1 Jan – 31 Des <?php echo e($thn); ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?php
                                    $thnPelaksanaan = $sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y') : date('Y');
                                ?>
                                <div class="d-flex flex-column">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill small fw-semibold w-fit mb-1">
                                        <i class="bi bi-calendar-event me-1"></i>Tahun <?php echo e($thnPelaksanaan); ?>

                                    </span>
                                    <span class="text-slate-800 fw-medium small" style="font-size: 0.78rem;">
                                        <?php echo e($sesi->tanggal_mulai->format('d M Y')); ?> - <?php echo e($sesi->tanggal_selesai->format('d M Y')); ?>

                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-slate-800"><?php echo e($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit); ?></span>
                                </div>
                            </td>
                            <td>
                                <?php if($sesi->status === 'draft'): ?>
                                    <span class="badge bg-secondary badge-role">Draft</span>
                                <?php elseif($sesi->status === 'berjalan'): ?>
                                    <span class="badge bg-warning text-dark badge-role">Berjalan</span>
                                <?php else: ?>
                                    <span class="badge bg-success badge-role">Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php
                                    $skorAkhir = $sesi->hitungSkorAkhir();
                                    $textClass = $skorAkhir >= 85 ? 'text-success' : ($skorAkhir >= 70 ? 'text-warning-emphasis' : 'text-danger');
                                    $barClass = $skorAkhir >= 85 ? 'bg-success' : ($skorAkhir >= 70 ? 'bg-warning' : 'bg-danger');
                                ?>
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <span class="fw-bold fs-6 <?php echo e($textClass); ?>">
                                        <?php echo e(number_format($skorAkhir, 2)); ?>%
                                    </span>
                                    <div class="progress w-100 mt-1" style="height: 5px; max-width: 90px;">
                                        <div class="progress-bar <?php echo e($barClass); ?>" 
                                             role="progressbar" style="width: <?php echo e(min($skorAkhir, 100)); ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group gap-1">
                                    <?php if($sesi->status !== 'selesai'): ?>
                                        <a href="<?php echo e(route('admin.audit-sesi.matrix', $sesi->id)); ?>" class="btn btn-sm btn-danger rounded-2">
                                            <i class="bi bi-pencil-square me-1"></i> Isi Matriks
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('admin.audit-sesi.rekap', $sesi->id)); ?>" class="btn btn-sm btn-outline-secondary rounded-2">
                                        <i class="bi bi-bar-chart-line me-1"></i> Rekap
                                    </a>
                                    <?php if($sesi->status !== 'selesai'): ?>
                                        <form action="<?php echo e(route('admin.audit-sesi.destroy', $sesi->id)); ?>" method="POST" class="d-inline" data-confirm="Apakah Anda yakin ingin menghapus sesi audit ini?">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2"><i class="bi bi-trash"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-muted">
                Menampilkan <?php echo e($auditSesis->firstItem() ?? 0); ?> - <?php echo e($auditSesis->lastItem() ?? 0); ?> dari total <?php echo e($auditSesis->total()); ?> sesi audit
            </div>
            <div>
                <?php echo $auditSesis->links('pagination::bootstrap-5'); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
