<?php
$title = 'Master Aturan & Tenggat Pelaporan — SMKP Minerba';
ob_start();
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-calendar-event me-2 text-primary"></i>Master Aturan &amp; Tenggat Pelaporan
        </h4>
        <p class="text-muted mb-0 small">Atur batas waktu penyampaian Laporan Audit Internal SMKP setelah akhir tahun periode (dapat disesuaikan dengan regulasi terbaru).</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCreateAturan">
            <i class="bi bi-plus-circle me-1"></i> Tambah Aturan
        </button>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-secondary small fw-bold text-uppercase">
                    <th>Kode</th>
                    <th>Nama Aturan</th>
                    <th class="text-center">Tenggat (Hari setelah 31 Des)</th>
                    <th>Status</th>
                    <th>Verifikasi</th>
                    <th style="width: 150px;" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items->isNotEmpty()): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border font-monospace"><?php echo e($item->kode); ?></span></td>
                            <td>
                                <div class="fw-bold text-slate-800 small"><?php echo e($item->nama_aturan); ?></div>
                                <?php if ($item->keterangan): ?>
                                    <div class="text-muted small mt-1"><?php echo e($item->keterangan); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-bold">
                                    <?php echo $item->hari_setelah_akhir_periode !== null ? e($item->hari_setelah_akhir_periode) . ' hari' : '-'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($item->is_active): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item->is_verified): ?>
                                    <span class="badge bg-primary rounded-pill px-3 py-1"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="bi bi-clock me-1"></i>Belum Diverifikasi</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <form action="<?php echo e(route('admin.master-aturan-pelaporan.toggle-verify', $item->id)); ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="PATCH">
                                        <button type="submit" class="btn btn-sm btn-light rounded-circle" title="Toggle Verifikasi">
                                            <i class="bi <?php echo $item->is_verified ? 'bi-patch-check-fill text-primary' : 'bi-patch-check text-muted'; ?>"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-light rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEditAturan<?php echo e($item->id); ?>" title="Edit">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </button>
                                    <form action="<?php echo e(route('admin.master-aturan-pelaporan.destroy', $item->id)); ?>" method="POST" class="d-inline" data-confirm="Hapus aturan pelaporan ini?">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-light rounded-circle" title="Hapus">
                                            <i class="bi bi-trash-fill text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada aturan pelaporan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modals Edit -->
<?php foreach ($items as $item): ?>
<div class="modal fade" id="modalEditAturan<?php echo e($item->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800">Edit Aturan Pelaporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.master-aturan-pelaporan.update', $item->id)); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="kode" class="form-control font-monospace" value="<?php echo e($item->kode); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Aturan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_aturan" class="form-control" value="<?php echo e($item->nama_aturan); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jumlah Hari Setelah 31 Desember</label>
                        <input type="number" min="1" max="365" name="hari_setelah_akhir_periode" class="form-control" value="<?php echo e($item->hari_setelah_akhir_periode); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"><?php echo e($item->keterangan ?? ''); ?></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="editAtActive<?php echo e($item->id); ?>" value="1" <?php echo $item->is_active ? 'checked' : ''; ?>>
                        <label class="form-check-label small fw-bold" for="editAtActive<?php echo e($item->id); ?>">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Modal Create -->
<div class="modal fade" id="modalCreateAturan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800">Tambah Aturan Pelaporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.master-aturan-pelaporan.store')); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="kode" class="form-control font-monospace" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Aturan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_aturan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jumlah Hari Setelah 31 Desember</label>
                        <input type="number" min="1" max="365" name="hari_setelah_akhir_periode" class="form-control" value="30">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
