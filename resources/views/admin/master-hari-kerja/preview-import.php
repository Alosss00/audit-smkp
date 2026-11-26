<?php
$title = 'Pratinjau Impor CSV Hari Kerja Audit — SMKP Minerba';
ob_start();
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="<?php echo route('admin.master-hari-kerja.index'); ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Master Hari Kerja Audit
        </a>
        <h2 class="fw-bold text-slate-800 mb-0 mt-1">
            <i class="bi bi-file-earmark-check text-primary me-2"></i>Pratinjau Impor CSV Hari Kerja Audit
        </h2>
    </div>
</div>

<?php if ($hasErrors): ?>
    <div class="alert alert-danger rounded-4 shadow-sm mb-4">
        <i class="bi bi-exclamation-octagon-fill me-2"></i>
        <strong>Impor belum dapat dilanjutkan.</strong> Terdapat baris yang melanggar aturan validasi atau bertumpuk dengan rentang lain. Perbaiki berkas CSV lalu unggah ulang.
    </div>
<?php else: ?>
    <div class="alert alert-success rounded-4 shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Seluruh <?php echo e($validCount); ?> baris lolos validasi.</strong> Semua baris yang diimpor akan masuk dengan status <em>Belum Terverifikasi</em> hingga diverifikasi oleh admin.
    </div>
<?php endif; ?>

<div class="card card-custom p-4 mb-4">
    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 70px;">Baris CSV</th>
                    <th>Jumlah Min</th>
                    <th>Jumlah Max</th>
                    <th>Hari Tinggi</th>
                    <th>Hari Menengah</th>
                    <th>Hari Rendah</th>
                    <th>Sumber</th>
                    <th>Status Validasi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($parsedRows as $row): ?>
                    <tr class="<?php echo !empty($row['errors']) ? 'table-danger' : ''; ?>">
                        <td class="text-center fw-bold"><?php echo e($row['line']); ?></td>
                        <td class="text-center"><?php echo e($row['jumlah_min'] ?? '-'); ?></td>
                        <td class="text-center"><?php echo e($row['jumlah_max'] !== null ? $row['jumlah_max'] : 'Tanpa batas (≥)'); ?></td>
                        <td class="text-center"><?php echo e($row['hari_tinggi']); ?></td>
                        <td class="text-center"><?php echo e($row['hari_menengah']); ?></td>
                        <td class="text-center"><?php echo e($row['hari_rendah']); ?></td>
                        <td class="small"><?php echo e($row['sumber'] ?: '-'); ?></td>
                        <td>
                            <?php if (empty($row['errors'])): ?>
                                <span class="badge bg-success rounded-pill px-3 py-1">Valid</span>
                            <?php else: ?>
                                <ul class="mb-0 ps-3 small text-danger">
                                    <?php foreach ($row['errors'] as $err): ?>
                                        <li><?php echo e($err); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="<?php echo route('admin.master-hari-kerja.index'); ?>" class="btn btn-light rounded-pill px-4">
            Batal &amp; Kembali
        </a>
        <?php if (! $hasErrors): ?>
            <form action="<?php echo route('admin.master-hari-kerja.import-confirm'); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                    <i class="bi bi-check2-all me-1"></i> Konfirmasi &amp; Simpan <?php echo e($validCount); ?> Baris
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
