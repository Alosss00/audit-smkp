<?php
$title = 'Master Hari Kerja Audit — SMKP Minerba';
ob_start();
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-calendar2-week-fill text-primary me-2"></i>Master Hari Kerja Audit (Mandays)
        </h2>
        <p class="text-muted mb-0 small">Kelola tabel acuan jumlah hari kerja audit berdasarkan rentang pekerja dan kelas risiko (Kepdirjen 185 Lampiran II).</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo route('admin.master-hari-kerja.template'); ?>" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
            <i class="bi bi-download me-1"></i> Template CSV
        </a>
        <button type="button" class="btn btn-outline-primary rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalImportCsv">
            <i class="bi bi-upload me-1"></i> Impor CSV
        </button>
        <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateHariKerja">
            <i class="bi bi-plus-lg me-1"></i> Tambah Rentang
        </button>
    </div>
</div>

<!-- Info Banner -->
<div class="alert alert-info border-0 shadow-sm rounded-4 d-flex align-items-start gap-3 mb-4">
    <i class="bi bi-info-circle-fill fs-4 text-info"></i>
    <div class="small">
        <strong>Catatan Verifikasi:</strong> Baris hasil impor CSV otomatis berstatus <span class="badge bg-warning text-dark rounded-pill">Belum Terverifikasi</span>.
        Verifikasi setiap rentang terhadap dokumen resmi Kepdirjen 185 Lampiran II sebelum menandainya terverifikasi. Rentang jumlah pekerja tidak boleh saling tumpang tindih.
    </div>
</div>

<!-- Main Table Card -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold text-slate-800 mb-0">
            <i class="bi bi-table text-primary me-2"></i>Daftar Rentang Hari Kerja Audit
        </h5>
        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
            Total: <?php echo e($items->count()); ?> rentang aktif
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 55px;">No</th>
                    <th class="text-start">Rentang Jumlah Pekerja</th>
                    <th style="width: 120px;">Risiko Tinggi<br><small class="text-muted fw-normal">(Hari)</small></th>
                    <th style="width: 120px;">Risiko Menengah<br><small class="text-muted fw-normal">(Hari)</small></th>
                    <th style="width: 120px;">Risiko Rendah<br><small class="text-muted fw-normal">(Hari)</small></th>
                    <th class="text-start">Sumber</th>
                    <th style="width: 145px;">Verifikasi</th>
                    <th style="width: 170px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items->isEmpty()): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Belum ada data acuan hari kerja audit. Tambahkan manual atau impor dari berkas CSV terverifikasi.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $idx => $item): ?>
                        <tr>
                            <td class="text-center fw-bold"><?php echo e($idx + 1); ?></td>
                            <td class="fw-semibold text-slate-800"><?php echo e($item->rentang_label); ?></td>
                            <td class="text-center fw-bold text-danger"><?php echo e(number_format((float) $item->hari_tinggi, 1)); ?></td>
                            <td class="text-center fw-bold text-warning-emphasis"><?php echo e(number_format((float) $item->hari_menengah, 1)); ?></td>
                            <td class="text-center fw-bold text-success"><?php echo e(number_format((float) $item->hari_rendah, 1)); ?></td>
                            <td class="small text-muted"><?php echo e($item->sumber ?: '-'); ?></td>
                            <td class="text-center">
                                <?php if ($item->is_verified): ?>
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="bi bi-patch-check-fill me-1"></i>Terverifikasi
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                        <i class="bi bi-exclamation-circle me-1"></i>Belum Verifikasi
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <form action="<?php echo route('admin.master-hari-kerja.toggle-verify', $item->id); ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="PATCH">
                                        <button type="submit" class="btn btn-sm <?php echo $item->is_verified ? 'btn-outline-warning' : 'btn-outline-success'; ?> rounded-3"
                                                title="<?php echo $item->is_verified ? 'Batalkan verifikasi' : 'Tandai terverifikasi'; ?>">
                                            <i class="bi <?php echo $item->is_verified ? 'bi-x-circle' : 'bi-check2-circle'; ?>"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3"
                                            data-bs-toggle="modal" data-bs-target="#modalEditHariKerja<?php echo e($item->id); ?>" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="<?php echo route('admin.master-hari-kerja.destroy', $item->id); ?>" method="POST" class="d-inline"
                                          data-confirm="Pindahkan rentang <?php echo e($item->rentang_label); ?> ke tempat sampah?">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($trashedItems->isNotEmpty()): ?>
    <div class="card card-custom p-4 border-warning border-opacity-50">
        <h6 class="fw-bold text-slate-800 mb-3">
            <i class="bi bi-trash3 text-warning me-2"></i>Data Terhapus (Soft Delete) — <?php echo e($trashedItems->count()); ?> baris
        </h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>Rentang</th>
                        <th>Tinggi</th>
                        <th>Menengah</th>
                        <th>Rendah</th>
                        <th>Dihapus Pada</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trashedItems as $t): ?>
                        <tr>
                            <td><?php echo e($t->rentang_label); ?></td>
                            <td class="text-center"><?php echo e(number_format((float) $t->hari_tinggi, 1)); ?></td>
                            <td class="text-center"><?php echo e(number_format((float) $t->hari_menengah, 1)); ?></td>
                            <td class="text-center"><?php echo e(number_format((float) $t->hari_rendah, 1)); ?></td>
                            <td class="text-center small text-muted"><?php echo e($t->deleted_at?->format('d/m/Y H:i')); ?></td>
                            <td class="text-center">
                                <form action="<?php echo route('admin.master-hari-kerja.restore', $t->id); ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0">Pulihkan</button>
                                </form>
                                <form action="<?php echo route('admin.master-hari-kerja.force-delete', $t->id); ?>" method="POST" class="d-inline"
                                      data-confirm="Hapus permanen baris acuan ini?">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0">Hapus Permanen</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Edit per Item -->
<?php foreach ($items as $item): ?>
    <div class="modal fade" id="modalEditHariKerja<?php echo e($item->id); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-slate-800">Edit Rentang Hari Kerja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo route('admin.master-hari-kerja.update', $item->id); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="modal-body py-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Jumlah Pekerja Min <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah_min" min="1" class="form-control" value="<?php echo e($item->jumlah_min); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Jumlah Pekerja Max (Kosong = &ge;)</label>
                                <input type="number" name="jumlah_max" min="1" class="form-control" value="<?php echo e($item->jumlah_max ?? ''); ?>" placeholder="Tanpa batas atas">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Hari Tinggi <span class="text-danger">*</span></label>
                                <input type="number" step="0.1" min="0.1" name="hari_tinggi" class="form-control" value="<?php echo e($item->hari_tinggi); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Hari Menengah <span class="text-danger">*</span></label>
                                <input type="number" step="0.1" min="0.1" name="hari_menengah" class="form-control" value="<?php echo e($item->hari_menengah); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Hari Rendah <span class="text-danger">*</span></label>
                                <input type="number" step="0.1" min="0.1" name="hari_rendah" class="form-control" value="<?php echo e($item->hari_rendah); ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Sumber Acuan</label>
                                <input type="text" name="sumber" class="form-control" maxlength="255" value="<?php echo e($item->sumber ?? ''); ?>" placeholder="Contoh: Kepdirjen 185 Lampiran II">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="editVerify<?php echo e($item->id); ?>" <?php echo $item->is_verified ? 'checked' : ''; ?>>
                                    <label class="form-check-label small fw-semibold" for="editVerify<?php echo e($item->id); ?>">Tandai Terverifikasi</label>
                                </div>
                            </div>
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

<!-- Modal Create -->
<div class="modal fade" id="modalCreateHariKerja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800">Tambah Rentang Hari Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo route('admin.master-hari-kerja.store'); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jumlah Pekerja Min <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_min" min="1" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jumlah Pekerja Max (Kosong = &ge;)</label>
                            <input type="number" name="jumlah_max" min="1" class="form-control" placeholder="Tanpa batas atas">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hari Tinggi <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" min="0.1" name="hari_tinggi" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hari Menengah <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" min="0.1" name="hari_menengah" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hari Rendah <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" min="0.1" name="hari_rendah" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Sumber Acuan</label>
                            <input type="text" name="sumber" class="form-control" maxlength="255" placeholder="Contoh: Kepdirjen 185 Lampiran II">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_verified" value="1" id="createVerify">
                                <label class="form-check-label small fw-semibold" for="createVerify">Tandai langsung terverifikasi</label>
                            </div>
                        </div>
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

<!-- Modal Import CSV -->
<div class="modal fade" id="modalImportCsv" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800">
                    <i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i>Impor Tabel Hari Kerja (CSV)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo route('admin.master-hari-kerja.import-preview'); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body py-3">
                    <p class="small text-muted mb-3">
                        Unggah berkas CSV (maks. 1 MB) dengan header:
                        <code>jumlah_min,jumlah_max,hari_tinggi,hari_menengah,hari_rendah,sumber</code>.
                        Sistem akan menampilkan halaman pratinjau validasi sebelum menyimpan.
                    </p>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Pilih File CSV <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-eye me-1"></i> Pratinjau Impor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
