<?php
$title = 'Master Data Perusahaan — Audit SMKP';

ob_start();
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-building me-2 text-primary"></i>Master Data Perusahaan
        </h4>
        <p class="text-muted mb-0 small">Kelola 42 daftar perusahaan mitra, pemegang IUP, kontraktor, dan subkontraktor terdaftar.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCreatePerusahaan">
            <i class="bi bi-plus-circle me-1"></i> Tambah Perusahaan
        </button>
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
                    <h5 class="fw-bold mb-0 text-slate-800"><?php echo e($perusahaans->count()); ?></h5>
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
                    <h5 class="fw-bold mb-0 text-slate-800"><?php echo e($perusahaans->where('kategori', 'Pemegang IUP')->count()); ?></h5>
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
                    <span class="text-muted small d-block">Kontraktor Utama</span>
                    <h5 class="fw-bold mb-0 text-slate-800"><?php echo e($perusahaans->where('kategori', 'Kontraktor')->count()); ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon-box bg-secondary bg-opacity-10 text-secondary">
                    <i class="bi bi-diagram-3-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Subkontraktor / Jasa</span>
                    <h5 class="fw-bold mb-0 text-slate-800"><?php echo e($perusahaans->where('kategori', 'Subkontraktor')->count()); ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card card-custom p-4 mb-4">
    <form action="<?php echo route('admin.perusahaans.index'); ?>" method="GET" class="row g-3 align-items-center">
        <div class="col-md-6 col-lg-5">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama perusahaan atau kode..." value="<?php echo e(request('search')); ?>">
            </div>
        </div>
        <div class="col-md-4 col-lg-3">
            <select name="kategori" class="form-select" data-auto-submit="true">
                <option value="">-- Semua Kategori --</option>
                <option value="Pemegang IUP" <?php echo request('kategori') == 'Pemegang IUP' ? 'selected' : ''; ?>>Pemegang IUP</option>
                <option value="Kontraktor" <?php echo request('kategori') == 'Kontraktor' ? 'selected' : ''; ?>>Kontraktor</option>
                <option value="Subkontraktor" <?php echo request('kategori') == 'Subkontraktor' ? 'selected' : ''; ?>>Subkontraktor</option>
                <option value="Lainnya" <?php echo request('kategori') == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-pill px-3 w-100 fw-semibold">Filter</button>
            <?php if (request('search') || request('kategori')): ?>
                <a href="<?php echo route('admin.perusahaans.index'); ?>" class="btn btn-light rounded-pill px-3" title="Reset Filter"><i class="bi bi-x-circle"></i></a>
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
                    <th>Nama Perusahaan</th>
                    <th>Kode</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Jumlah Audit</th>
                    <th style="width: 130px;" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($perusahaans->isNotEmpty()): ?>
                    <?php foreach ($perusahaans as $index => $perusahaan): ?>
                        <tr>
                            <td class="text-muted fw-bold"><?php echo e($index + 1); ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="stat-icon-box bg-light text-primary" style="width: 38px; height: 38px; font-size: 1rem;">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <span class="fw-bold text-slate-800"><?php echo e($perusahaan->nama_perusahaan); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark font-monospace px-2 py-1 border"><?php echo e($perusahaan->kode_perusahaan ?? '-'); ?></span>
                            </td>
                            <td>
                                <?php if ($perusahaan->kategori === 'Pemegang IUP'): ?>
                                    <span class="badge rounded-pill bg-info text-dark px-3 py-1"><i class="bi bi-award-fill me-1"></i> <?php echo e($perusahaan->kategori); ?></span>
                                <?php elseif ($perusahaan->kategori === 'Kontraktor'): ?>
                                    <span class="badge rounded-pill bg-primary px-3 py-1"><i class="bi bi-truck-front-fill me-1"></i> <?php echo e($perusahaan->kategori); ?></span>
                                <?php elseif ($perusahaan->kategori === 'Subkontraktor'): ?>
                                    <span class="badge rounded-pill bg-secondary px-3 py-1"><i class="bi bi-diagram-3 me-1"></i> <?php echo e($perusahaan->kategori); ?></span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-light text-secondary px-3 py-1 border"><?php echo e($perusahaan->kategori); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($perusahaan->is_active): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-bold">
                                    <?php echo e($perusahaan->auditSesis->count()); ?> Sesi
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" class="btn btn-sm btn-light rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEditPerusahaan<?php echo e($perusahaan->id); ?>" title="Edit">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </button>
                                    <form action="<?php echo route('admin.perusahaans.toggle-status', $perusahaan->id); ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="PATCH">
                                        <button type="submit" class="btn btn-sm btn-light rounded-circle" title="<?php echo $perusahaan->is_active ? 'Nonaktifkan' : 'Aktifkan'; ?>">
                                            <i class="bi <?php echo $perusahaan->is_active ? 'bi-toggle-on text-success' : 'bi-toggle-off text-muted'; ?> fs-5"></i>
                                        </button>
                                    </form>
                                    <form action="<?php echo route('admin.perusahaans.destroy', $perusahaan->id); ?>" method="POST" class="d-inline" data-confirm="Apakah Anda yakin ingin menghapus perusahaan <?php echo e($perusahaan->nama_perusahaan); ?>?">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-light rounded-circle" title="Hapus">
                                            <i class="bi bi-trash-fill text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Perusahaan -->
                        <div class="modal fade" id="modalEditPerusahaan<?php echo e($perusahaan->id); ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold text-slate-800">
                                            <i class="bi bi-pencil-square text-warning me-2"></i>Edit Data Perusahaan
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="<?php echo route('admin.perusahaans.update', $perusahaan->id); ?>" method="POST">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="PUT">
                                        <div class="modal-body py-3">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-slate-800">Nama Perusahaan <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_perusahaan" class="form-control" value="<?php echo e($perusahaan->nama_perusahaan); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-slate-800">Kode Perusahaan (Opsional)</label>
                                                <input type="text" name="kode_perusahaan" class="form-control" value="<?php echo e($perusahaan->kode_perusahaan); ?>" placeholder="Contoh: PT-MSM">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-slate-800">Kategori Perusahaan <span class="text-danger">*</span></label>
                                                <select name="kategori" class="form-select" required>
                                                    <option value="Pemegang IUP" <?php echo $perusahaan->kategori == 'Pemegang IUP' ? 'selected' : ''; ?>>Pemegang IUP</option>
                                                    <option value="Kontraktor" <?php echo $perusahaan->kategori == 'Kontraktor' ? 'selected' : ''; ?>>Kontraktor</option>
                                                    <option value="Subkontraktor" <?php echo $perusahaan->kategori == 'Subkontraktor' ? 'selected' : ''; ?>>Subkontraktor</option>
                                                    <option value="Lainnya" <?php echo $perusahaan->kategori == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                                </select>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="editActive<?php echo e($perusahaan->id); ?>" value="1" <?php echo $perusahaan->is_active ? 'checked' : ''; ?>>
                                                <label class="form-check-label fw-semibold text-slate-800" for="editActive<?php echo e($perusahaan->id); ?>">Status Aktif</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-building-exclamation fs-1 text-muted d-block mb-2"></i>
                            <span class="text-muted fw-semibold">Belum ada data perusahaan yang sesuai.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create Perusahaan -->
<div class="modal fade" id="modalCreatePerusahaan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800">
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Perusahaan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo route('admin.perusahaans.store'); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-800">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_perusahaan" class="form-control" placeholder="Contoh: PT. Meares Soputan Mining" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-800">Kode Perusahaan (Opsional)</label>
                        <input type="text" name="kode_perusahaan" class="form-control" placeholder="Contoh: MSM">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-slate-800">Kategori Perusahaan <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            <option value="Subkontraktor" selected>Subkontraktor</option>
                            <option value="Kontraktor">Kontraktor</option>
                            <option value="Pemegang IUP">Pemegang IUP</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perusahaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

echo view('layouts.app', get_defined_vars())->render();
