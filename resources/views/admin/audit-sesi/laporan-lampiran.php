<?php
$title = 'Lampiran Laporan Audit Internal SMKP — Daftar Hadir, Respon Manajemen, IUJP';
ob_start();

$isAuditorRole = auth()->check() && auth()->user()->role === 'auditor';
$laporanRoute = $isAuditorRole
    ? route('auditor.audit-sesi.laporan.show', $sesi->id)
    : route('admin.audit-sesi.laporan.show', $sesi->id);

$dhSets = [
    'pembukaan' => ['model' => $dhPembukaan, 'label' => 'Daftar Hadir Rapat Pembukaan', 'warning' => $warnings['pembukaan']],
    'penutupan' => ['model' => $dhPenutupan, 'label' => 'Daftar Hadir Rapat Penutupan', 'warning' => $warnings['penutupan']],
];
?>

<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
        <a href="<?php echo e($laporanRoute); ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Laporan Audit
        </a>
        <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
            <h2 class="fw-bold text-slate-800 mb-0">
                <i class="bi bi-paperclip text-primary me-2"></i>Lampiran Laporan Audit Internal SMKP
            </h2>
            <span class="badge bg-primary rounded-pill px-3 py-2">Lampiran C, D, E<?php echo $iujpEligible ? ', F' : ''; ?></span>
        </div>
        <p class="text-muted small mb-0 mt-1">
            Auditi: <strong><?php echo e($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit); ?></strong> &bull;
            Pelaksanaan: <strong><?php echo e($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('d/m/Y') : '-'); ?> s/d <?php echo e($sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('d/m/Y') : '-'); ?></strong>
        </p>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="card card-custom p-2 mb-4 bg-white">
    <ul class="nav nav-pills nav-fill gap-2" id="lampiranTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-3 py-2 fw-bold" data-bs-toggle="pill" data-bs-target="#pane-dh-pembukaan" type="button" role="tab">
                <i class="bi bi-person-lines-fill me-1"></i> C. Daftar Hadir Pembukaan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2 fw-bold" data-bs-toggle="pill" data-bs-target="#pane-dh-penutupan" type="button" role="tab">
                <i class="bi bi-person-check-fill me-1"></i> D. Daftar Hadir Penutupan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2 fw-bold" data-bs-toggle="pill" data-bs-target="#pane-respon" type="button" role="tab">
                <i class="bi bi-chat-square-text-fill me-1"></i> E. Respon Manajemen
            </button>
        </li>
        <?php if ($iujpEligible): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-3 py-2 fw-bold" data-bs-toggle="pill" data-bs-target="#pane-iujp" type="button" role="tab">
                    <i class="bi bi-buildings-fill me-1"></i> F. Hasil Audit IUJP
                </button>
            </li>
        <?php endif; ?>
    </ul>
</div>

<div class="tab-content">
    <?php foreach ($dhSets as $jenis => $set): ?>
        <?php
        $dh = $set['model'];
        $pesertaList = $dh->pesertas->isNotEmpty()
            ? $dh->pesertas
            : collect([(object) ['nama' => '', 'jabatan' => '', 'unit_instansi' => '', 'peran' => 'auditi', 'hadir' => true]]);
        $cetakDhRoute = $isAuditorRole
            ? route('auditor.audit-sesi.laporan.daftar-hadir.cetak', [$sesi->id, $jenis])
            : route('admin.audit-sesi.laporan.daftar-hadir.cetak', [$sesi->id, $jenis]);
        ?>
        <div class="tab-pane fade <?php echo $jenis === 'pembukaan' ? 'show active' : ''; ?>" id="pane-dh-<?php echo e($jenis); ?>" role="tabpanel">
            <div class="card card-custom p-4 mb-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 border-bottom pb-2">
                    <div>
                        <h5 class="fw-bold text-slate-800 mb-0"><?php echo e($set['label']); ?></h5>
                        <small class="text-muted">Judul otomatis: <strong><?php echo e($dh->judul_otomatis); ?></strong> &bull; Hari: <strong><?php echo e($dh->nama_hari_indonesia); ?></strong></small>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo e($cetakDhRoute); ?>" target="_blank" class="btn btn-sm btn-dark rounded-3 px-3 fw-semibold">
                            <i class="bi bi-printer-fill me-1"></i> Cetak Daftar Hadir
                        </a>
                        <?php if (! $isReadOnly): ?>
                            <form action="<?php echo e(route('admin.audit-sesi.laporan.daftar-hadir.prefill', [$sesi->id, $jenis])); ?>" method="POST" class="d-inline">
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-info text-dark rounded-3 px-3 fw-semibold">
                                    <i class="bi bi-people me-1"></i> Prefill dari Rencana Audit
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (! empty($set['warning'])): ?>
                    <div class="alert alert-warning rounded-3 small py-2 mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo e($set['warning']); ?>
                    </div>
                <?php endif; ?>

                <?php if (! $isReadOnly): ?>
                <form action="<?php echo e(route('admin.audit-sesi.laporan.daftar-hadir.save', [$sesi->id, $jenis])); ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <fieldset <?php echo $isReadOnly ? 'disabled' : ''; ?>>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tanggal Rapat</label>
                            <input type="date" name="tanggal" class="form-control" value="<?php echo e($dh->tanggal ? $dh->tanggal->format('Y-m-d') : ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Lokasi / Ruang Rapat</label>
                            <input type="text" name="lokasi" class="form-control" value="<?php echo e($dh->lokasi ?? ''); ?>" placeholder="Contoh: Ruang Meeting Kantor Site">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Unggah Scan Daftar Hadir (PDF/JPG/PNG, maks 5 MB)</label>
                            <input type="file" name="scan" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <?php if ($dh->scan_url): ?>
                            <div class="col-12">
                                <div class="d-flex flex-wrap align-items-center gap-3 p-2 bg-light rounded border small">
                                    <a href="<?php echo e($dh->scan_url); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Lihat Scan Daftar Hadir
                                    </a>
                                    <?php if (! $isReadOnly): ?>
                                        <div class="form-check mb-0 ms-auto">
                                            <input class="form-check-input" type="checkbox" name="hapus_scan" value="1" id="hapusScan<?php echo e($jenis); ?>">
                                            <label class="form-check-label text-danger" for="hapusScan<?php echo e($jenis); ?>">Hapus scan saat simpan</label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-slate-800 mb-0">Daftar Peserta</h6>
                        <?php if (! $isReadOnly): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-add-peserta" data-target="tablePeserta<?php echo e(ucfirst($jenis)); ?>">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Peserta
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 small" id="tablePeserta<?php echo e(ucfirst($jenis)); ?>">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 45px;">No</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Unit / Instansi</th>
                                    <th style="width: 170px;">Peran</th>
                                    <?php if (! $isReadOnly): ?><th style="width: 50px;"></th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pesertaList as $idx => $p): ?>
                                    <tr>
                                        <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                        <td><input type="text" name="pesertas[<?php echo e($idx); ?>][nama]" class="form-control form-control-sm" value="<?php echo e($p->nama ?? ''); ?>"></td>
                                        <td><input type="text" name="pesertas[<?php echo e($idx); ?>][jabatan]" class="form-control form-control-sm" value="<?php echo e($p->jabatan ?? ''); ?>"></td>
                                        <td><input type="text" name="pesertas[<?php echo e($idx); ?>][unit_instansi]" class="form-control form-control-sm" value="<?php echo e($p->unit_instansi ?? ''); ?>"></td>
                                        <td>
                                            <select name="pesertas[<?php echo e($idx); ?>][peran]" class="form-select form-select-sm">
                                                <?php foreach (\App\Models\AuditDaftarHadirPeserta::PERAN_OPTIONS as $pKey => $pLbl): ?>
                                                    <option value="<?php echo e($pKey); ?>" <?php echo ($p->peran ?? 'auditi') === $pKey ? 'selected' : ''; ?>><?php echo e($pLbl); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <?php if (! $isReadOnly): ?>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-peserta"><i class="bi bi-trash"></i></button></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </fieldset>

                <?php if (! $isReadOnly): ?>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Daftar Hadir <?php echo e(ucfirst($jenis)); ?>
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- TAB E: RESPON MANAJEMEN -->
    <div class="tab-pane fade" id="pane-respon" role="tabpanel">
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 border-bottom pb-2">
                <h5 class="fw-bold text-slate-800 mb-0">Lampiran E — Respon Manajemen terhadap Hasil Audit</h5>
                <?php if (! $isReadOnly): ?>
                    <form action="<?php echo e(route('admin.audit-sesi.laporan.respon-manajemen.prefill', $sesi->id)); ?>" method="POST" class="d-inline">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <button type="submit" class="btn btn-sm btn-outline-info text-dark rounded-3 px-3 fw-semibold">
                            <i class="bi bi-magic me-1"></i> Prefill dari Template
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <?php if (! empty($warnings['respon'])): ?>
                <div class="alert alert-warning rounded-3 small py-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo e($warnings['respon']); ?>
                </div>
            <?php endif; ?>

            <?php if (! $isReadOnly): ?>
            <form action="<?php echo e(route('admin.audit-sesi.laporan.respon-manajemen.save', $sesi->id)); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>
            <fieldset <?php echo $isReadOnly ? 'disabled' : ''; ?>>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tanggal Respon</label>
                        <input type="date" name="tanggal_respon" class="form-control" value="<?php echo e($responManajemen->tanggal_respon ? $responManajemen->tanggal_respon->format('Y-m-d') : ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Nama Penandatangan</label>
                        <input type="text" name="nama_penandatangan" class="form-control" value="<?php echo e($responManajemen->nama_penandatangan ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Jabatan Penandatangan</label>
                        <input type="text" name="jabatan_penandatangan" class="form-control" value="<?php echo e($responManajemen->jabatan_penandatangan ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Isi Respon Manajemen</label>
                        <textarea name="isi_respon" class="form-control" rows="5"><?php echo e($responManajemen->isi_respon ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Unggah Dokumen Respon Bertanda Tangan (PDF/JPG/PNG, maks 5 MB)</label>
                        <input type="file" name="dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <?php if ($responManajemen->dokumen_url): ?>
                            <div class="mt-2 d-flex flex-wrap align-items-center gap-3 p-2 bg-light rounded border small">
                                <a href="<?php echo e($responManajemen->dokumen_url); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Lihat Dokumen Respon
                                </a>
                                <?php if (! $isReadOnly): ?>
                                    <div class="form-check mb-0 ms-auto">
                                        <input class="form-check-input" type="checkbox" name="hapus_dokumen" value="1" id="hapusDokRespon">
                                        <label class="form-check-label text-danger" for="hapusDokRespon">Hapus dokumen saat simpan</label>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </fieldset>
            <?php if (! $isReadOnly): ?>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Respon Manajemen
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- TAB F: HASIL AUDIT IUJP (KHUSUS TAMBANG / PENGOLAHAN PEMURNIAN) -->
    <?php if ($iujpEligible): ?>
    <div class="tab-pane fade" id="pane-iujp" role="tabpanel">
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 border-bottom pb-2">
                <h5 class="fw-bold text-slate-800 mb-0">Lampiran F — Rekapitulasi Hasil Audit Internal IUJP / Kontraktor</h5>
                <?php if (! $isReadOnly): ?>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAddIujp">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Hasil Audit IUJP
                    </button>
                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Perusahaan IUJP</th>
                            <th>Nomor IUJP &amp; Bidang</th>
                            <th>Tanggal Audit</th>
                            <th class="text-end">Skor (%)</th>
                            <th class="text-center">Temuan (K/My/Mn)</th>
                            <th>Dokumen</th>
                            <?php if (! $isReadOnly): ?><th class="text-end">Aksi</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($iujps->isNotEmpty()): ?>
                            <?php foreach ($iujps as $iu): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo e($iu->nama_perusahaan_iujp); ?></td>
                                    <td>
                                        <div><?php echo e($iu->nomor_iujp ?? '-'); ?></div>
                                        <div class="text-muted"><?php echo e($iu->bidang_usaha ?? ''); ?></div>
                                    </td>
                                    <td><?php echo e($iu->tanggal_audit ? $iu->tanggal_audit->format('d/m/Y') : '-'); ?></td>
                                    <td class="text-end fw-bold"><?php echo $iu->skor_pencapaian !== null ? e(number_format((float) $iu->skor_pencapaian, 2, ',', '.')) . '%' : '-'; ?></td>
                                    <td class="text-center"><?php echo e($iu->jumlah_temuan_kritikal . ' / ' . $iu->jumlah_temuan_mayor . ' / ' . $iu->jumlah_temuan_minor); ?></td>
                                    <td>
                                        <?php if ($iu->dokumen_url): ?>
                                            <a href="<?php echo e($iu->dokumen_url); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0">Lihat</a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (! $isReadOnly): ?>
                                        <td class="text-end">
                                            <form action="<?php echo e(route('admin.audit-sesi.laporan.iujp.destroy', [$sesi->id, $iu->id])); ?>" method="POST" class="d-inline" data-confirm="Hapus data hasil audit IUJP ini?">
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
                                <td colspan="<?php echo $isReadOnly ? 6 : 7; ?>" class="text-center py-4 text-muted">Belum ada hasil audit IUJP yang ditambahkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if ($iujpEligible && ! $isReadOnly): ?>
<div class="modal fade" id="modalAddIujp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800"><i class="bi bi-buildings-fill text-primary me-2"></i>Tambah Hasil Audit IUJP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.audit-sesi.laporan.iujp.store', $sesi->id)); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Tarik Otomatis dari Sesi Audit IUJP Selesai (Opsional)</label>
                            <select name="sumber_audit_sesi_id" class="form-select">
                                <option value="">-- Input Manual --</option>
                                <?php foreach ($availableIujpSesis as $av): ?>
                                    <option value="<?php echo e($av->id); ?>">
                                        <?php echo e(($av->perusahaan->nama_perusahaan ?? $av->area_audit) . ' — Periode ' . $av->tahun_periode . ' (Skor: ' . number_format((float) $av->skor_akhir, 2) . '%)'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Jika dipilih, skor pencapaian dan jumlah temuan akan ditarik otomatis dari sesi tersebut.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Perusahaan IUJP</label>
                            <input type="text" name="nama_perusahaan_iujp" class="form-control" placeholder="Wajib diisi jika input manual">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nomor IUJP</label>
                            <input type="text" name="nomor_iujp" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Bidang Usaha Jasa</label>
                            <input type="text" name="bidang_usaha" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Audit</label>
                            <input type="date" name="tanggal_audit" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Skor (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="skor_pencapaian" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Temuan Kritikal</label>
                            <input type="number" min="0" name="jumlah_temuan_kritikal" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Temuan Mayor</label>
                            <input type="number" min="0" name="jumlah_temuan_mayor" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Temuan Minor</label>
                            <input type="number" min="0" name="jumlah_temuan_minor" class="form-control" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Ringkasan Hasil Audit</label>
                            <textarea name="ringkasan" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Unggah Ringkasan / Laporan IUJP (PDF/JPG/PNG, maks 5 MB)</label>
                            <input type="file" name="dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Hasil IUJP</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
document.addEventListener('DOMContentLoaded', function () {
    const peranOptions = <?php echo json_encode(\App\Models\AuditDaftarHadirPeserta::PERAN_OPTIONS, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
    let pCounter = 200;

    function renumber(tbody) {
        tbody.querySelectorAll('tr').forEach((tr, idx) => {
            const num = tr.querySelector('.row-num');
            if (num) num.textContent = idx + 1;
        });
    }

    document.querySelectorAll('.btn-add-peserta').forEach(btn => {
        btn.addEventListener('click', function () {
            const tableId = btn.getAttribute('data-target');
            const tbody = document.getElementById(tableId)?.querySelector('tbody');
            if (!tbody) return;
            const idx = pCounter++;
            const tr = document.createElement('tr');
            const sel = document.createElement('select');
            sel.name = `pesertas[${idx}][peran]`;
            sel.className = 'form-select form-select-sm';
            Object.entries(peranOptions).forEach(([k, lbl]) => {
                const opt = document.createElement('option');
                opt.value = k;
                opt.textContent = lbl;
                if (k === 'auditi') opt.selected = true;
                sel.appendChild(opt);
            });
            tr.innerHTML = `<td class="text-center fw-bold row-num"></td><td><input type="text" name="pesertas[${idx}][nama]" class="form-control form-control-sm"></td><td><input type="text" name="pesertas[${idx}][jabatan]" class="form-control form-control-sm"></td><td><input type="text" name="pesertas[${idx}][unit_instansi]" class="form-control form-control-sm"></td><td></td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-peserta"><i class="bi bi-trash"></i></button></td>`;
            tr.children[4].appendChild(sel);
            tbody.appendChild(tr);
            renumber(tbody);
        });
    });

    document.body.addEventListener('click', function (e) {
        const rm = e.target.closest('.btn-remove-peserta');
        if (rm) {
            const tr = rm.closest('tr');
            const tbody = tr?.closest('tbody');
            if (tr && tbody && tbody.querySelectorAll('tr').length > 1) {
                tr.remove();
                renumber(tbody);
            }
        }
    });
});
</script>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
