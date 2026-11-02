<?php
$title = 'Master Data Dokumen Tahap Awal Audit — SMKP Minerba';

ob_start();
?>
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold text-slate-800 mb-0">
                <i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>Master Data Dokumen Tahap Awal Audit
            </h4>
            <span class="badge bg-primary rounded-pill px-3 py-1">TT-MGT-FRS-026L</span>
        </div>
        <p class="text-muted mb-0 small">
            Kelola master data dan profil penyusunan Dokumen Tahap Awal Audit (Tahap I) untuk masing-masing perusahaan yang terdaftar di sistem auditor.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.perusahaans.index')); ?>" class="btn btn-outline-primary rounded-pill px-4 fw-semibold shadow-sm">
            <i class="bi bi-building me-1"></i> Kelola Master Perusahaan
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-buildings-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Total Perusahaan</span>
                    <h5 class="fw-bold mb-0 text-slate-800"><?php echo e($totalPerusahaan); ?> Perusahaan</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check2-all fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Data Kustom Tersimpan</span>
                    <h5 class="fw-bold mb-0 text-slate-800"><?php echo e($totalCustomized); ?> Perusahaan</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-info bg-opacity-10 text-info">
                    <i class="bi bi-award-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Pemegang IUP</span>
                    <h5 class="fw-bold mb-0 text-slate-800"><?php echo e($totalIup); ?> Perusahaan</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-truck-front-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Kontraktor & Subkon</span>
                    <h5 class="fw-bold mb-0 text-slate-800"><?php echo e($totalKontraktor); ?> Perusahaan</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card card-custom p-4 mb-4">
    <form action="<?php echo e(route('admin.master-tahap-awal.index')); ?>" method="GET" class="row g-3 align-items-center">
        <div class="col-md-5 col-lg-4">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama perusahaan, kode, atau komoditas..." value="<?php echo e(request('search')); ?>">
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <select name="kategori" class="form-select" data-auto-submit="true">
                <option value="">-- Semua Kategori Perusahaan --</option>
                <option value="Pemegang IUP" <?php echo request('kategori') == 'Pemegang IUP' ? 'selected' : ''; ?>>Pemegang IUP</option>
                <option value="Kontraktor" <?php echo request('kategori') == 'Kontraktor' ? 'selected' : ''; ?>>Kontraktor</option>
                <option value="Subkontraktor" <?php echo request('kategori') == 'Subkontraktor' ? 'selected' : ''; ?>>Subkontraktor</option>
                <option value="Lainnya" <?php echo request('kategori') == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
            </select>
        </div>
        <div class="col-md-2 col-lg-3">
            <select name="status_master" class="form-select" data-auto-submit="true">
                <option value="">-- Semua Status Data --</option>
                <option value="customized" <?php echo request('status_master') == 'customized' ? 'selected' : ''; ?>>Sudah Disesuaikan</option>
                <option value="default" <?php echo request('status_master') == 'default' ? 'selected' : ''; ?>>Preset Standar</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-pill px-3 w-100 fw-semibold">Filter</button>
            <?php if (request('search') || request('kategori') || request('status_master')): ?>
                <a href="<?php echo e(route('admin.master-tahap-awal.index')); ?>" class="btn btn-light rounded-pill px-3 border" title="Reset Filter"><i class="bi bi-x-circle"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Data Table Card -->
<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-secondary small fw-bold text-uppercase">
                    <th style="width: 50px;">#</th>
                    <th>Perusahaan Terdaftar</th>
                    <th>Komoditas & Perizinan</th>
                    <th>Pekerja & Mandays</th>
                    <th>Tim Audit & KTT</th>
                    <th>Status Master</th>
                    <th style="width: 220px;" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($perusahaans->isNotEmpty()): ?>
                    <?php foreach ($perusahaans as $index => $perusahaan): ?>
                        <?php
                            $m = $perusahaan->masterTahapAwal;
                            $komoditas = ($m && $m->jenis_komoditas) ? $m->jenis_komoditas : ($perusahaan->jenis_komoditas ?: 'Emas DMP');
                            $perizinan = ($m && $m->jenis_perizinan) ? $m->jenis_perizinan : ($perusahaan->jenis_perizinan ?: 'B-43/Pres/11/1986');
                            $pekerja = $m ? ($m->jumlah_pekerja ?? 3086) : 3086;
                            $kelasRisiko = $m ? ($m->kelas_risiko ?: 'Tinggi') : 'Tinggi';
                            $totalMandays = $m ? ($m->total_mandays ?? 27.5) : 27.5;
                            $ketuaTim = $m ? ($m->ketua_tim_audit ?: 'Fiska Polii') : 'Fiska Polii';
                            $namaKtt = ($m && $m->nama_ktt) ? $m->nama_ktt : ($perusahaan->nama_ktt_default ?: 'Tejo Prihantoro');
                            $jmlTim = ($m && is_array($m->tim_audit_tahap_1)) ? count($m->tim_audit_tahap_1) : 5;
                            $isCustomized = $m && $m->is_customized;
                        ?>
                        <tr>
                            <td class="text-muted fw-bold"><?php echo e($index + 1); ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon-box bg-light text-primary flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                        <i class="bi bi-building-check"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-slate-800"><?php echo e($perusahaan->nama_perusahaan); ?></div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <?php if ($perusahaan->kode_perusahaan): ?>
                                                <span class="badge bg-light text-dark font-monospace border" style="font-size: 0.7rem;"><?php echo e($perusahaan->kode_perusahaan); ?></span>
                                            <?php endif; ?>
                                            <?php if ($perusahaan->kategori === 'Pemegang IUP'): ?>
                                                <span class="badge rounded-pill bg-info text-dark" style="font-size: 0.7rem;"><?php echo e($perusahaan->kategori); ?></span>
                                            <?php elseif ($perusahaan->kategori === 'Kontraktor'): ?>
                                                <span class="badge rounded-pill bg-primary" style="font-size: 0.7rem;"><?php echo e($perusahaan->kategori); ?></span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-secondary" style="font-size: 0.7rem;"><?php echo e($perusahaan->kategori); ?></span>
                                            <?php endif; ?>
                                            <span class="text-muted" style="font-size: 0.75rem;">&bull; <?php echo e($perusahaan->auditSesis->count()); ?> Sesi Audit</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-slate-800 small"><?php echo e($komoditas); ?></div>
                                <div class="text-muted text-truncate" style="max-width: 200px; font-size: 0.76rem;" title="<?php echo e($perizinan); ?>">
                                    <?php echo e(str_replace("\n", ' / ', $perizinan)); ?>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-slate-800 small"><?php echo e(number_format((int)$pekerja)); ?> Pekerja</span>
                                    <span class="badge <?php echo $kelasRisiko === 'Tinggi' ? 'bg-danger' : ($kelasRisiko === 'Sedang' ? 'bg-warning text-dark' : 'bg-success'); ?> rounded-pill" style="font-size: 0.68rem;">
                                        Risiko <?php echo e($kelasRisiko); ?>
                                    </span>
                                </div>
                                <div class="text-muted" style="font-size: 0.76rem;">
                                    Total Mandays: <strong><?php echo e($totalMandays); ?> Hari</strong>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-semibold text-slate-800">
                                    <i class="bi bi-person-badge text-primary me-1"></i>Ketua: <?php echo e($ketuaTim); ?> (<?php echo e($jmlTim); ?> Auditor)
                                </div>
                                <div class="text-muted" style="font-size: 0.76rem;">
                                    <i class="bi bi-person-check me-1"></i>KTT/PJO: <?php echo e($namaKtt); ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($isCustomized): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i> Disesuaikan
                                    </span>
                                    <div class="text-muted mt-1" style="font-size: 0.7rem;">
                                        Update: <?php echo e($m->updated_at ? $m->updated_at->format('d/m/Y H:i') : '-'); ?>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-3 py-1">
                                        <i class="bi bi-sliders me-1"></i> Preset Standar
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="<?php echo e(route('admin.master-tahap-awal.edit', $perusahaan->id)); ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-sm">
                                        <i class="bi bi-pencil-square me-1"></i> Kelola Data Tahap Awal
                                    </a>
                                    <?php if ($isCustomized): ?>
                                        <form action="<?php echo e(route('admin.master-tahap-awal.reset', $perusahaan->id)); ?>" method="POST" class="d-inline" data-confirm="Reset Master Data Tahap Awal untuk <?php echo e($perusahaan->nama_perusahaan); ?> ke preset standar?">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                            <button type="submit" class="btn btn-sm btn-light border rounded-circle" title="Reset ke Preset Standar">
                                                <i class="bi bi-arrow-counterclockwise text-warning"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-building-exclamation fs-1 text-muted d-block mb-2"></i>
                            <span class="text-muted fw-semibold">Tidak ada perusahaan yang ditemukan sesuai filter pencarian.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();

echo view('layouts.app', get_defined_vars())->render();

