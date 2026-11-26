<?php
$title = 'Laporan Audit Internal SMKP — Pengesahan & Distribusi';
ob_start();

$isAuditorRole = auth()->check() && auth()->user()->role === 'auditor';
$isAdmin = auth()->check() && auth()->user()->isAdmin();
$backRoute = $isAuditorRole
    ? route('auditor.audit-sesi.index')
    : route('admin.audit-sesi.index');
$profilRoute = $isAuditorRole
    ? route('auditor.audit-sesi.laporan.profil', $sesi->id)
    : route('admin.audit-sesi.laporan.profil', $sesi->id);
$cetakLaporanRoute = $isAuditorRole
    ? route('auditor.audit-sesi.laporan.cetak', $sesi->id)
    : route('admin.audit-sesi.laporan.cetak', $sesi->id);
$lampiranRoute = $isAuditorRole
    ? route('auditor.audit-sesi.laporan.lampiran', $sesi->id)
    : route('admin.audit-sesi.laporan.lampiran', $sesi->id);

$statusBadgeMap = [
    'draft'           => 'bg-secondary',
    'ditinjau'        => 'bg-warning text-dark',
    'disahkan'        => 'bg-primary',
    'didistribusikan' => 'bg-info text-dark',
    'dilaporkan'      => 'bg-success',
];
$badgeClass = $statusBadgeMap[$laporan->status] ?? 'bg-secondary';
$isOverdue = $laporan->isOverdue();
$hariTersisa = $laporan->getHariTersisa();
?>

<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
        <a href="<?php echo e($backRoute); ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Sesi Audit
        </a>
        <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
            <h2 class="fw-bold text-slate-800 mb-0">
                <i class="bi bi-file-earmark-check-fill text-primary me-2"></i>Laporan Audit Internal SMKP
            </h2>
            <span class="badge <?php echo e($badgeClass); ?> rounded-pill px-3 py-2"><?php echo e($laporan->status_label); ?></span>
            <?php if ($laporan->revisi_ke > 0): ?>
                <span class="badge bg-dark rounded-pill px-3 py-2">Revisi ke-<?php echo e($laporan->revisi_ke); ?></span>
            <?php endif; ?>
            <?php if ($isOverdue): ?>
                <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-exclamation-octagon me-1"></i>Melewati Tenggat</span>
            <?php endif; ?>
        </div>
        <p class="text-muted small mb-0 mt-1">
            Auditi: <strong><?php echo e($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit); ?></strong> &bull;
            Periode: <strong><?php echo e($sesi->tahun_periode); ?></strong> &bull;
            Batas Waktu Pelaporan: <strong><?php echo e($laporan->batas_waktu_pelaporan ? $laporan->batas_waktu_pelaporan->format('d/m/Y') : '-'); ?></strong>
            <?php if ($hariTersisa !== null && $laporan->status !== 'dilaporkan'): ?>
                (<?php echo $hariTersisa >= 0 ? 'Sisa ' . e($hariTersisa) . ' hari' : 'Terlambat ' . e(abs($hariTersisa)) . ' hari'; ?>)
            <?php endif; ?>
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo e($cetakLaporanRoute); ?>" target="_blank" class="btn btn-dark rounded-3 px-3 fw-semibold shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> Cetak Laporan Audit
        </a>
        <a href="<?php echo e($profilRoute); ?>" class="btn btn-outline-primary rounded-3 px-3 fw-semibold">
            <i class="bi bi-building-gear me-1"></i> Profil &amp; Gambaran Umum
        </a>
        <a href="<?php echo e($lampiranRoute); ?>" class="btn btn-outline-secondary rounded-3 px-3 fw-semibold">
            <i class="bi bi-paperclip me-1"></i> Lampiran (Daftar Hadir, Respon, IUJP)
        </a>
    </div>
</div>

<!-- Checklist Definisi Audit Selesai -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <h5 class="fw-bold text-slate-800 mb-0">
            <i class="bi bi-clipboard2-check text-primary me-2"></i>Status Kelengkapan Pelaksanaan Audit
        </h5>
        <?php if ($statusSelesai['selesai']): ?>
            <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i>Audit Dinyatakan Selesai</span>
        <?php else: ?>
            <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-hourglass-split me-1"></i>Belum Selesai Sepenuhnya</span>
        <?php endif; ?>
    </div>
    <?php if (! $statusSelesai['selesai']): ?>
        <ul class="small text-muted mb-0 mt-2">
            <?php foreach ($statusSelesai['alasan'] as $al): ?>
                <li><?php echo e($al); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="small text-success mb-0">Seluruh jadwal kegiatan (termasuk Rapat Penutupan) telah terlaksana dan penilaian matriks telah selesai.</p>
    <?php endif; ?>
</div>

<div class="row g-4 mb-4">
    <!-- Metadata Laporan -->
    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-slate-800 mb-3 border-bottom pb-2">
                <i class="bi bi-info-circle text-primary me-2"></i>Metadata Laporan
            </h5>
            <?php if (! $isReadOnly && $laporan->status === 'draft'): ?>
                <form action="<?php echo e(route('admin.audit-sesi.laporan.metadata', $sesi->id)); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>
            <fieldset <?php echo ($isReadOnly || $laporan->status !== 'draft') ? 'disabled' : ''; ?>>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nomor Laporan</label>
                        <input type="text" name="nomor_laporan" class="form-control font-monospace" value="<?php echo e($laporan->nomor_laporan ?? ''); ?>" placeholder="Contoh: LAP-SMKP/2026/001">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tanggal Laporan</label>
                        <input type="date" name="tanggal_laporan" class="form-control" value="<?php echo e($laporan->tanggal_laporan ? $laporan->tanggal_laporan->format('Y-m-d') : ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tempat Penerbitan</label>
                        <input type="text" name="tempat_penerbitan" class="form-control" value="<?php echo e($laporan->tempat_penerbitan ?? ''); ?>" placeholder="Contoh: Jakarta / Site Tambang">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Penanggung Jawab / Ketua Tim</label>
                        <input type="text" name="penanggung_jawab_nama" class="form-control" value="<?php echo e($laporan->penanggung_jawab_nama ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Batas Waktu Pelaporan</label>
                        <input type="date" name="batas_waktu_pelaporan" class="form-control" value="<?php echo e($laporan->batas_waktu_pelaporan ? $laporan->batas_waktu_pelaporan->format('Y-m-d') : ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tanggal Dilaporkan (KaIT / ESDM)</label>
                        <input type="text" class="form-control bg-light" value="<?php echo e($laporan->tanggal_dilaporkan ? $laporan->tanggal_dilaporkan->format('d/m/Y') : 'Belum dilaporkan'); ?>" readonly>
                    </div>
                </div>
            </fieldset>
            <?php if (! $isReadOnly && $laporan->status === 'draft'): ?>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Metadata
                    </button>
                </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Panel Alur Pengesahan & Integritas Snapshot -->
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-slate-800 mb-3 border-bottom pb-2">
                <i class="bi bi-diagram-3 text-primary me-2"></i>Alur Pengesahan &amp; Snapshot
            </h5>
            <div class="small mb-3">
                <div>Diajukan: <strong><?php echo e($laporan->pengaju?->name ?? '-'); ?></strong> <?php echo $laporan->tanggal_diajukan ? '(' . e($laporan->tanggal_diajukan->format('d/m/Y H:i')) . ')' : ''; ?></div>
                <div>Disahkan: <strong><?php echo e($laporan->pengesah?->name ?? '-'); ?></strong> <?php echo $laporan->tanggal_pengesahan ? '(' . e($laporan->tanggal_pengesahan->format('d/m/Y H:i')) . ')' : ''; ?></div>
                <?php if ($laporan->catatan_revisi): ?>
                    <div class="mt-2 p-2 bg-light rounded border">
                        <span class="text-muted">Catatan Revisi Terakhir:</span> <?php echo e($laporan->catatan_revisi); ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($integrity['has_snapshot']): ?>
                <div class="p-3 rounded-3 border mb-3 <?php echo $integrity['valid'] ? 'bg-success bg-opacity-10 border-success' : 'bg-danger bg-opacity-10 border-danger'; ?>">
                    <div class="fw-bold small <?php echo $integrity['valid'] ? 'text-success' : 'text-danger'; ?>">
                        <i class="bi <?php echo $integrity['valid'] ? 'bi-shield-check' : 'bi-shield-x'; ?> me-1"></i>
                        Snapshot Revisi #<?php echo e($integrity['revisi_ke']); ?>: <?php echo $integrity['valid'] ? 'Integritas SHA-256 Valid' : 'Integritas Tidak Cocok!'; ?>
                    </div>
                    <div class="font-monospace text-muted small text-break mt-1"><?php echo e($integrity['hash_stored']); ?></div>
                    <?php if (! $isReadOnly): ?>
                        <form action="<?php echo e(route('admin.audit-sesi.laporan.verify-hash', $sesi->id)); ?>" method="POST" class="mt-2">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <button type="submit" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                <i class="bi bi-fingerprint me-1"></i> Verifikasi Integritas Hash
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (! $isReadOnly): ?>
                <div class="d-flex flex-column gap-2">
                    <?php if ($laporan->status === 'draft'): ?>
                        <form action="<?php echo e(route('admin.audit-sesi.laporan.submit-review', $sesi->id)); ?>" method="POST" data-confirm="Ajukan laporan audit untuk ditinjau?">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <button type="submit" class="btn btn-warning text-dark w-100 rounded-3 fw-semibold">
                                <i class="bi bi-send me-1"></i> Ajukan Tinjauan (Draft → Ditinjau)
                            </button>
                        </form>
                    <?php elseif ($laporan->status === 'ditinjau'): ?>
                        <form action="<?php echo e(route('admin.audit-sesi.laporan.approve', $sesi->id)); ?>" method="POST" data-confirm="Sahkan laporan ini? Snapshot JSON dan hash SHA-256 akan dikunci secara permanen.">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-semibold">
                                <i class="bi bi-patch-check-fill me-1"></i> Sahkan &amp; Kunci Snapshot SHA-256
                            </button>
                        </form>
                    <?php elseif (in_array($laporan->status, ['disahkan', 'didistribusikan'], true)): ?>
                        <form action="<?php echo e(route('admin.audit-sesi.laporan.mark-reported', $sesi->id)); ?>" method="POST" class="d-flex gap-2">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <input type="date" name="tanggal_dilaporkan" class="form-control form-control-sm" value="<?php echo e(date('Y-m-d')); ?>" required>
                            <button type="submit" class="btn btn-success btn-sm rounded-3 fw-semibold text-nowrap px-3">
                                <i class="bi bi-check2-all me-1"></i> Tandai Dilaporkan
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ($isAdmin && $laporan->status !== 'draft'): ?>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalRevisiLaporan">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Buka Revisi Baru (Khusus Admin)
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Penyesuaian Narasi (teks_override — khusus Draft) -->
<?php if (! $isReadOnly): ?>
<?php $override = is_array($laporan->teks_override) ? $laporan->teks_override : []; ?>
<div class="card card-custom p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2 border-bottom pb-2">
        <div>
            <h5 class="fw-bold text-slate-800 mb-0">
                <i class="bi bi-pencil-square text-primary me-2"></i>Penyesuaian Narasi Laporan (Opsional)
            </h5>
            <small class="text-muted">Kosongkan untuk menggunakan template otomatis dari Master Template Laporan. Hanya dapat diubah saat status Draft.</small>
        </div>
    </div>
    <form action="<?php echo e(route('admin.audit-sesi.laporan.teks-override', $sesi->id)); ?>" method="POST">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="_method" value="PUT">
        <fieldset <?php echo $laporan->status !== 'draft' ? 'disabled' : ''; ?>>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small fw-bold">Override Bab 1 — Latar Belakang</label>
                    <textarea name="teks_override[latar_belakang]" class="form-control" rows="3" placeholder="Biarkan kosong untuk menggunakan template otomatis..."><?php echo e($override['latar_belakang'] ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Override Tujuan Audit</label>
                    <textarea name="teks_override[tujuan_audit]" class="form-control" rows="2" placeholder="Biarkan kosong untuk menggunakan template otomatis..."><?php echo e($override['tujuan_audit'] ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Override Bab 6 — Penutup</label>
                    <textarea name="teks_override[penutup]" class="form-control" rows="3" placeholder="Biarkan kosong untuk menggunakan template otomatis..."><?php echo e($override['penutup'] ?? ''); ?></textarea>
                </div>
            </div>
        </fieldset>
        <?php if ($laporan->status === 'draft'): ?>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-semibold">
                    <i class="bi bi-save me-1"></i> Simpan Penyesuaian Narasi
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>
<?php endif; ?>

<!-- Distribusi & Penyampaian Laporan -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 border-bottom pb-2">
        <h5 class="fw-bold text-slate-800 mb-0">
            <i class="bi bi-envelope-paper text-primary me-2"></i>Catatan Distribusi &amp; Penyampaian Laporan
        </h5>
        <?php if (! $isReadOnly && $laporan->isDisahkanAtauLebih()): ?>
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAddDistribusi">
                <i class="bi bi-plus-lg me-1"></i> Catat Distribusi
            </button>
        <?php endif; ?>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Penerima</th>
                    <th>Kategori</th>
                    <th>Metode &amp; No. Tanda Terima</th>
                    <th>Bukti</th>
                    <?php if (! $isReadOnly): ?><th class="text-end">Aksi</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($laporan->distribusis->isNotEmpty()): ?>
                    <?php foreach ($laporan->distribusis as $d): ?>
                        <tr>
                            <td><?php echo e($d->tanggal_distribusi ? $d->tanggal_distribusi->format('d/m/Y') : '-'); ?></td>
                            <td>
                                <div class="fw-bold"><?php echo e($d->penerima_nama); ?></div>
                                <div class="text-muted"><?php echo e(implode(' — ', array_filter([$d->penerima_jabatan, $d->penerima_instansi]))); ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border rounded-pill px-2 py-1"><?php echo e($d->jenis_penerima_label); ?></span></td>
                            <td>
                                <div><?php echo e($d->metode_label); ?></div>
                                <?php if ($d->nomor_tanda_terima): ?>
                                    <div class="text-muted font-monospace"><?php echo e($d->nomor_tanda_terima); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($d->bukti_url): ?>
                                    <a href="<?php echo e($d->bukti_url); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0">
                                        <i class="bi bi-file-earmark-arrow-down me-1"></i>Lihat
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <?php if (! $isReadOnly): ?>
                                <td class="text-end">
                                    <form action="<?php echo e(route('admin.audit-sesi.laporan.distribusi.destroy', [$sesi->id, $d->id])); ?>" method="POST" class="d-inline" data-confirm="Hapus catatan distribusi ini?">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-light rounded-circle"><i class="bi bi-trash-fill text-danger"></i></button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?php echo $isReadOnly ? 5 : 6; ?>" class="text-center py-4 text-muted">
                            <?php echo $laporan->isDisahkanAtauLebih() ? 'Belum ada catatan distribusi laporan.' : 'Distribusi dapat dicatat setelah laporan berstatus Disahkan.'; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Status Pelaksanaan Jadwal Audit -->
<?php if ($sesi->rencana && $sesi->rencana->jadwals->isNotEmpty()): ?>
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold text-slate-800 mb-3 border-bottom pb-2">
        <i class="bi bi-calendar2-check text-primary me-2"></i>Status Pelaksanaan Jadwal Audit
    </h5>
    <?php if (! $isReadOnly): ?>
        <form action="<?php echo e(route('admin.audit-sesi.laporan.jadwal-status', $sesi->id)); ?>" method="POST">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="_method" value="PATCH">
    <?php endif; ?>
    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">Hari</th>
                    <th style="width: 110px;">Tanggal</th>
                    <th>Kegiatan</th>
                    <th style="width: 190px;">Status Pelaksanaan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sesi->rencana->jadwals as $j): ?>
                    <tr>
                        <td class="text-center">Hari <?php echo e($j->hari_ke); ?></td>
                        <td><?php echo e($j->tanggal ? $j->tanggal->format('d/m/Y') : '-'); ?></td>
                        <td><?php echo e($j->kegiatan); ?></td>
                        <td>
                            <select name="statuses[<?php echo e($j->id); ?>]" class="form-select form-select-sm" <?php echo $isReadOnly ? 'disabled' : ''; ?>>
                                <?php foreach (\App\Models\AuditRencanaJadwal::STATUS_PELAKSANAAN_OPTIONS as $stKey => $stLabel): ?>
                                    <option value="<?php echo e($stKey); ?>" <?php echo ($j->status_pelaksanaan ?? 'direncanakan') === $stKey ? 'selected' : ''; ?>><?php echo e($stLabel); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (! $isReadOnly): ?>
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-semibold">
                <i class="bi bi-save me-1"></i> Simpan Status Jadwal
            </button>
        </div>
        </form>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Modal Catat Distribusi -->
<?php if (! $isReadOnly && $laporan->isDisahkanAtauLebih()): ?>
<div class="modal fade" id="modalAddDistribusi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800"><i class="bi bi-envelope-paper-fill text-primary me-2"></i>Catat Distribusi Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.audit-sesi.laporan.distribusi.store', $sesi->id)); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="penerima_nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jabatan Penerima</label>
                            <input type="text" name="penerima_jabatan" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Instansi / Unit</label>
                            <input type="text" name="penerima_instansi" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kategori Penerima <span class="text-danger">*</span></label>
                            <select name="jenis_penerima" class="form-select" required>
                                <?php foreach (\App\Models\AuditLaporanDistribusi::JENIS_PENERIMA_OPTIONS as $jpKey => $jpLabel): ?>
                                    <option value="<?php echo e($jpKey); ?>"><?php echo e($jpLabel); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tanggal Distribusi <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_distribusi" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Metode <span class="text-danger">*</span></label>
                            <select name="metode" class="form-select" required>
                                <?php foreach (\App\Models\AuditLaporanDistribusi::METODE_OPTIONS as $mKey => $mLabel): ?>
                                    <option value="<?php echo e($mKey); ?>"><?php echo e($mLabel); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">No. Tanda Terima</label>
                            <input type="text" name="nomor_tanda_terima" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Unggah Bukti Distribusi (PDF/JPG/PNG, maks. 5 MB)</label>
                            <input type="file" name="bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Distribusi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Revisi Laporan (Khusus Admin) -->
<?php if ($isAdmin && $laporan->status !== 'draft'): ?>
<div class="modal fade" id="modalRevisiLaporan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-arrow-counterclockwise me-2"></i>Buka Revisi Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.audit-sesi.laporan.reopen-revision', $sesi->id)); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body py-3">
                    <p class="small text-muted">Status laporan akan kembali ke <strong>Draft</strong> dengan nomor revisi bertambah (+1). Seluruh snapshot pengesahan sebelumnya tetap tersimpan utuh sebagai jejak audit.</p>
                    <label class="form-label small fw-bold">Alasan / Catatan Revisi <span class="text-danger">*</span></label>
                    <textarea name="catatan_revisi" class="form-control" rows="3" required placeholder="Jelaskan alasan pembukaan revisi..."></textarea>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Buka Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
