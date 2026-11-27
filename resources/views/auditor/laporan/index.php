<?php
$title = 'Laporan Audit Internal SMKP Disahkan — Area Saya';
ob_start();
?>

<div class="mb-4">
    <h4 class="fw-bold text-slate-800 mb-1">
        <i class="bi bi-file-earmark-check-fill text-primary me-2"></i>Laporan Audit Internal SMKP (Disahkan)
    </h4>
    <p class="text-muted small mb-0">Menampilkan daftar Laporan Audit Internal SMKP yang telah <strong>Disahkan</strong>, <strong>Didistribusikan</strong>, atau <strong>Dilaporkan</strong> untuk area kerja Anda.</p>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr class="text-secondary fw-bold text-uppercase">
                    <th>Perusahaan / Area</th>
                    <th>Periode</th>
                    <th>Nomor Laporan</th>
                    <th>Status</th>
                    <th>Tanggal Pengesahan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($laporans->isNotEmpty()): ?>
                    <?php foreach ($laporans as $lap): ?>
                        <?php $s = $lap->auditSesi; ?>
                        <tr>
                            <td class="fw-bold text-slate-800"><?php echo e($s->perusahaan->nama_perusahaan ?? $s->area_audit ?? '-'); ?></td>
                            <td><?php echo e($s->tahun_periode ?? '-'); ?></td>
                            <td class="font-monospace"><?php echo e($lap->nomor_laporan ?: '-'); ?></td>
                            <td>
                                <span class="badge bg-success rounded-pill px-3 py-1"><?php echo e($lap->status_label); ?></span>
                                <?php if ($lap->revisi_ke > 0): ?>
                                    <span class="badge bg-dark rounded-pill px-2 py-1">Rev #<?php echo e($lap->revisi_ke); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($lap->tanggal_pengesahan ? $lap->tanggal_pengesahan->format('d/m/Y H:i') : '-'); ?></td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="<?php echo e(route('auditor.audit-sesi.laporan.show', $s->id)); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Detail</a>
                                    <a href="<?php echo e(route('auditor.audit-sesi.laporan.cetak', $s->id)); ?>" target="_blank" class="btn btn-sm btn-dark rounded-pill px-3">
                                        <i class="bi bi-printer-fill me-1"></i>Cetak
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                            Belum ada Laporan Audit Internal SMKP berstatus Disahkan untuk area kerja Anda.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
