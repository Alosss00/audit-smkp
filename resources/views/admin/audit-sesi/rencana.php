<?php
$title = 'Rencana Audit (' . ($rencana->no_dokumen ?: 'TT-MGT-FRS-026A') . ') — SMKP Minerba';
ob_start();

$isAuditorRole = auth()->check() && auth()->user()->role === 'auditor';
$cetakRoute = $isAuditorRole
    ? route('auditor.audit-sesi.rencana.cetak', $sesi->id)
    : route('admin.audit-sesi.rencana.cetak', $sesi->id);
$backRoute = $isAuditorRole
    ? route('auditor.audit-sesi.index')
    : route('admin.audit-sesi.index');

$dokumenRelevan = !empty($rencana->dokumen_relevan) ? $rencana->dokumen_relevan : [''];
$areaLingkupList = $rencana->areas->isNotEmpty()
    ? $rencana->areas->pluck('nama_area')->all()
    : [''];
$timList = $rencana->tims->isNotEmpty()
    ? $rencana->tims
    : collect([(object) ['nama' => '', 'jabatan' => '', 'peran' => 'auditor']]);
$jadwalList = $rencana->jadwals->isNotEmpty()
    ? $rencana->jadwals
    : collect([(object) [
        'hari_ke' => 1,
        'tanggal' => $sesi->tanggal_mulai,
        'jam_mulai' => '08:00',
        'jam_selesai' => '09:00',
        'jenis_kegiatan' => 'rapat_pembukaan',
        'kegiatan' => 'Rapat Pembukaan Audit Internal SMKP',
        'elemen_id' => null,
        'auditor' => '',
        'auditi_pic' => '',
        'lokasi' => '',
    ]]);
$pengesahanList = $rencana->pengesahans->isNotEmpty()
    ? $rencana->pengesahans
    : collect([(object) ['jabatan' => 'Ketua Tim', 'nama' => '', 'tanggal_ttd' => null]]);
?>

<!-- Top Header -->
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
        <a href="<?php echo e($backRoute); ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Sesi Audit
        </a>
        <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
            <h2 class="fw-bold text-slate-800 mb-0">
                <i class="bi bi-calendar2-check-fill text-primary me-2"></i>Rencana Audit Internal SMKP
            </h2>
            <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo e($rencana->no_dokumen ?: 'TT-MGT-FRS-026A'); ?></span>
            <?php if ($rencana->isFinal()): ?>
                <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-lock-fill me-1"></i>Final</span>
            <?php else: ?>
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-pencil-square me-1"></i>Draft</span>
            <?php endif; ?>
        </div>
        <p class="text-muted small mb-0 mt-1">
            Auditi: <strong><?php echo e($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit); ?></strong> &bull;
            Periode Sesi: <strong><?php echo e($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('d/m/Y') : '-'); ?> s/d <?php echo e($sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('d/m/Y') : '-'); ?></strong>
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo e($cetakRoute); ?>" target="_blank" class="btn btn-dark rounded-3 px-3 fw-semibold shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> Cetak Landscape (026A)
        </a>
        <?php if (! $isAuditorRole): ?>
            <a href="<?php echo e(route('admin.audit-sesi.tahap-awal', $sesi->id)); ?>" class="btn btn-outline-secondary rounded-3 px-3">
                <i class="bi bi-journal-bookmark me-1"></i> Tahap Awal
            </a>
            <?php if (! $rencana->isFinal()): ?>
                <form action="<?php echo e(route('admin.audit-sesi.rencana.finalize', $sesi->id)); ?>" method="POST" class="d-inline"
                      data-confirm="Finalisasi Rencana Audit? Setelah final, hanya Administrator yang dapat mengubah dokumen ini.">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="btn btn-success rounded-3 px-3 fw-semibold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Finalkan Rencana
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($mandaysWarning)): ?>
    <div class="alert alert-warning rounded-4 shadow-sm d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
        <div class="small fw-semibold"><?php echo e($mandaysWarning); ?></div>
    </div>
<?php endif; ?>

<?php if ($isReadOnly): ?>
    <div class="alert alert-info rounded-4 shadow-sm d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-lock-fill fs-5 text-info"></i>
        <div class="small">
            <?php if ($isAuditorRole): ?>
                Mode <strong>Hanya Baca</strong> untuk Auditor Perusahaan. Anda dapat meninjau dan mencetak Rencana Audit ini.
            <?php else: ?>
                Rencana Audit telah berstatus <strong>Final</strong> dan terkunci. Perubahan hanya dapat dilakukan oleh Administrator.
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- 5 Navigation Tabs -->
<?php echo view('partials.perangkat_audit_nav', ['sesi' => $sesi, 'activeStep' => 'rencana'])->render(); ?>
<div class="card card-custom p-2 mb-4 bg-white">
    <ul class="nav nav-pills nav-fill gap-2" id="rencanaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-3 py-2 fw-bold" id="tab-info-btn" data-bs-toggle="pill" data-bs-target="#pane-info" type="button" role="tab">
                <i class="bi bi-info-circle me-1"></i> 1. Informasi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2 fw-bold" id="tab-tim-btn" data-bs-toggle="pill" data-bs-target="#pane-tim" type="button" role="tab">
                <i class="bi bi-people me-1"></i> 2. Tim Audit
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2 fw-bold" id="tab-jadwal-btn" data-bs-toggle="pill" data-bs-target="#pane-jadwal" type="button" role="tab">
                <i class="bi bi-calendar3 me-1"></i> 3. Jadwal
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2 fw-bold" id="tab-sampel-btn" data-bs-toggle="pill" data-bs-target="#pane-sampel" type="button" role="tab">
                <i class="bi bi-grid-3x3 me-1"></i> 4. Metode &amp; Sampel
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2 fw-bold" id="tab-pengesahan-btn" data-bs-toggle="pill" data-bs-target="#pane-pengesahan" type="button" role="tab">
                <i class="bi bi-pen me-1"></i> 5. Pengesahan
            </button>
        </li>
    </ul>
</div>

<?php if (! $isReadOnly): ?>
<form action="<?php echo e(route('admin.audit-sesi.rencana.update', $sesi->id)); ?>" method="POST" id="formRencanaAudit">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="_method" value="PUT">
<?php endif; ?>

<fieldset <?php echo $isReadOnly ? 'disabled' : ''; ?>>
    <div class="tab-content" id="rencanaTabContent">
        <!-- TAB 1: INFORMASI UMUM -->
        <div class="tab-pane fade show active" id="pane-info" role="tabpanel">
            <div class="card card-custom p-4">
                <h5 class="fw-bold text-slate-800 mb-3 border-bottom pb-2">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>Informasi Umum Rencana Audit
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Nomor Dokumen <span class="text-danger">*</span></label>
                        <input type="text" name="no_dokumen" class="form-control font-monospace" value="<?php echo e($rencana->no_dokumen ?: 'TT-MGT-FRS-026A'); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Jenis Audit</label>
                        <input type="text" name="jenis_audit" class="form-control" value="<?php echo e($rencana->jenis_audit ?? ''); ?>" placeholder="Contoh: Audit Internal SMKP Minerba">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Kelompok Penandatangan</label>
                        <select name="kelompok_penandatangan" class="form-select">
                            <option value="">-- Pilih Kelompok Usaha --</option>
                            <?php foreach (\App\Models\AuditRencana::KELOMPOK_PENANDATANGAN_OPTIONS as $kKey => $kLabel): ?>
                                <option value="<?php echo e($kKey); ?>" <?php echo ($rencana->kelompok_penandatangan ?? $perusahaanKelompok) === $kKey ? 'selected' : ''; ?>>
                                    <?php echo e($kLabel); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($perusahaanKelompok)): ?>
                            <small class="text-warning-emphasis">Jenis usaha perusahaan belum diisi; pilih kelompok penandatangan di sini.</small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kriteria Audit</label>
                        <textarea name="kriteria_audit" class="form-control" rows="3"><?php echo e($rencana->kriteria_audit ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tujuan Audit</label>
                        <textarea name="tujuan" class="form-control" rows="3"><?php echo e($rencana->tujuan ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Ruang Lingkup Audit</label>
                        <textarea name="ruang_lingkup" class="form-control" rows="3"><?php echo e($rencana->ruang_lingkup ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Pengecualian (Opsional)</label>
                        <textarea name="pengecualian" class="form-control" rows="3"><?php echo e($rencana->pengecualian ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Fasilitas &amp; Sumber Daya Pendukung</label>
                        <textarea name="fasilitas" class="form-control" rows="2"><?php echo e($rencana->fasilitas ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Distribusi Laporan Audit</label>
                        <textarea name="distribusi_laporan" class="form-control" rows="2"><?php echo e($rencana->distribusi_laporan ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <label class="form-label small fw-bold mb-0">Daftar Area Lingkup Audit (Terstruktur)</label>
                                <small class="text-muted d-block">Digunakan pada Bab 3 Ruang Lingkup Laporan Audit Internal SMKP.</small>
                            </div>
                            <?php if (! $isReadOnly): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAddAreaLingkup">
                                    <i class="bi bi-plus"></i> Tambah Area
                                </button>
                            <?php endif; ?>
                        </div>
                        <div id="wrapAreaLingkup" class="d-flex flex-column gap-2">
                            <?php foreach ($areaLingkupList as $areaNama): ?>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="areas[]" class="form-control" value="<?php echo e($areaNama); ?>" placeholder="Contoh: Area Pit Tambang / Workshop / Gudang Handak">
                                    <?php if (! $isReadOnly): ?>
                                        <button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-x-lg"></i></button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold mb-0">Daftar Dokumen Relevan</label>
                            <?php if (! $isReadOnly): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAddDokRelevan">
                                    <i class="bi bi-plus"></i> Tambah Dokumen
                                </button>
                            <?php endif; ?>
                        </div>
                        <div id="wrapDokRelevan" class="d-flex flex-column gap-2">
                            <?php foreach ($dokumenRelevan as $dok): ?>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="dokumen_relevan[]" class="form-control" value="<?php echo e($dok); ?>" placeholder="Nama / nomor dokumen relevan">
                                    <?php if (! $isReadOnly): ?>
                                        <button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-x-lg"></i></button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: TIM AUDIT -->
        <div class="tab-pane fade" id="pane-tim" role="tabpanel">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold text-slate-800 mb-0">
                            <i class="bi bi-people-fill text-primary me-2"></i>Tim Audit Internal
                        </h5>
                        <small class="text-muted">Saat finalisasi, tepat <strong>1</strong> anggota wajib berperan sebagai <strong>Ketua Tim (Lead Auditor)</strong>.</small>
                    </div>
                    <?php if (! $isReadOnly): ?>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-info text-dark rounded-3 fw-semibold" id="btnTriggerCopyTim2">
                                <i class="bi bi-files me-1"></i> Salin dari Tahap II
                            </button>
                            <button type="button" class="btn btn-sm btn-primary rounded-3" id="btnAddTimRencana">
                                <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="tableTimRencana">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama</th>
                                <th>Jabatan / No. Registrasi</th>
                                <th style="width: 230px;">Peran dalam Tim</th>
                                <?php if (! $isReadOnly): ?><th style="width: 55px;">Hapus</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($timList as $idx => $t): ?>
                                <tr>
                                    <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                    <td><input type="text" name="tims[<?php echo e($idx); ?>][nama]" class="form-control form-control-sm" value="<?php echo e($t->nama ?? ''); ?>"></td>
                                    <td><input type="text" name="tims[<?php echo e($idx); ?>][jabatan]" class="form-control form-control-sm" value="<?php echo e($t->jabatan ?? ''); ?>"></td>
                                    <td>
                                        <select name="tims[<?php echo e($idx); ?>][peran]" class="form-select form-select-sm">
                                            <?php foreach (\App\Models\AuditRencana::PERAN_OPTIONS as $pKey => $pLabel): ?>
                                                <option value="<?php echo e($pKey); ?>" <?php echo ($t->peran ?? 'auditor') === $pKey ? 'selected' : ''; ?>><?php echo e($pLabel); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <?php if (! $isReadOnly): ?>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: JADWAL PELAKSANAAN -->
        <div class="tab-pane fade" id="pane-jadwal" role="tabpanel">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold text-slate-800 mb-0">
                            <i class="bi bi-calendar3-week text-primary me-2"></i>Jadwal Pelaksanaan Audit
                        </h5>
                        <small class="text-muted">Hari pertama wajib memuat <strong>Rapat Pembukaan</strong> dan hari terakhir wajib memuat <strong>Rapat Penutupan</strong>. Tanggal harus dalam rentang sesi audit.</small>
                    </div>
                    <?php if (! $isReadOnly): ?>
                        <button type="button" class="btn btn-sm btn-primary rounded-3" id="btnAddJadwal">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Baris Jadwal
                        </button>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="tableJadwal">
                        <thead class="table-light text-center small">
                            <tr>
                                <th style="width: 65px;">Hari Ke</th>
                                <th style="width: 135px;">Tanggal</th>
                                <th style="width: 105px;">Mulai</th>
                                <th style="width: 105px;">Selesai</th>
                                <th style="width: 175px;">Jenis Kegiatan</th>
                                <th>Uraian Kegiatan</th>
                                <th style="width: 150px;">Elemen</th>
                                <th style="width: 120px;">Auditor</th>
                                <th style="width: 120px;">PIC Auditi</th>
                                <th style="width: 110px;">Lokasi</th>
                                <?php if (! $isReadOnly): ?><th style="width: 48px;"></th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwalList as $idx => $j): ?>
                                <?php $tglVal = ($j->tanggal instanceof \Carbon\Carbon) ? $j->tanggal->format('Y-m-d') : (string) ($j->tanggal ?? ''); ?>
                                <tr>
                                    <td><input type="number" min="1" name="jadwals[<?php echo e($idx); ?>][hari_ke]" class="form-control form-control-sm text-center" value="<?php echo e($j->hari_ke ?? 1); ?>"></td>
                                    <td><input type="date" name="jadwals[<?php echo e($idx); ?>][tanggal]" class="form-control form-control-sm" value="<?php echo e($tglVal); ?>"></td>
                                    <td><input type="time" name="jadwals[<?php echo e($idx); ?>][jam_mulai]" class="form-control form-control-sm" value="<?php echo e(substr((string) ($j->jam_mulai ?? '08:00'), 0, 5)); ?>"></td>
                                    <td><input type="time" name="jadwals[<?php echo e($idx); ?>][jam_selesai]" class="form-control form-control-sm" value="<?php echo e(substr((string) ($j->jam_selesai ?? '17:00'), 0, 5)); ?>"></td>
                                    <td>
                                        <select name="jadwals[<?php echo e($idx); ?>][jenis_kegiatan]" class="form-select form-select-sm">
                                            <?php foreach (\App\Models\AuditRencana::JENIS_KEGIATAN_OPTIONS as $jkKey => $jkLabel): ?>
                                                <option value="<?php echo e($jkKey); ?>" <?php echo ($j->jenis_kegiatan ?? 'audit_elemen') === $jkKey ? 'selected' : ''; ?>><?php echo e($jkLabel); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="text" name="jadwals[<?php echo e($idx); ?>][kegiatan]" class="form-control form-control-sm" value="<?php echo e($j->kegiatan ?? ''); ?>"></td>
                                    <td>
                                        <select name="jadwals[<?php echo e($idx); ?>][elemen_id]" class="form-select form-select-sm">
                                            <option value="">-- Opsional --</option>
                                            <?php foreach ($elemens as $el): ?>
                                                <option value="<?php echo e($el->id); ?>" <?php echo (int) ($j->elemen_id ?? 0) === (int) $el->id ? 'selected' : ''; ?>>
                                                    <?php echo e($el->kode_elemen); ?> - <?php echo e($el->nama_elemen); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="text" name="jadwals[<?php echo e($idx); ?>][auditor]" class="form-control form-control-sm" value="<?php echo e($j->auditor ?? ''); ?>"></td>
                                    <td><input type="text" name="jadwals[<?php echo e($idx); ?>][auditi_pic]" class="form-control form-control-sm" value="<?php echo e($j->auditi_pic ?? ''); ?>"></td>
                                    <td><input type="text" name="jadwals[<?php echo e($idx); ?>][lokasi]" class="form-control form-control-sm" value="<?php echo e($j->lokasi ?? ''); ?>"></td>
                                    <?php if (! $isReadOnly): ?>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 4: MATRIKS METODE & SAMPEL -->
        <div class="tab-pane fade" id="pane-sampel" role="tabpanel">
            <div class="card card-custom p-4">
                <div class="mb-3">
                    <h5 class="fw-bold text-slate-800 mb-1">
                        <i class="bi bi-grid-3x3-gap text-primary me-2"></i>Matriks Metode &amp; Sampel Audit (Per Sub-Elemen)
                    </h5>
                    <small class="text-muted">
                        Jika metode atau teknik sampling diisi, maka <strong>Deskripsi Sampel</strong> dan <strong>Jumlah Sampel (&ge; 1)</strong> wajib diisi.
                        Untuk metode <strong>Slovin</strong>, masukkan populasi (N) dan margin error (e, 0 &lt; e &lt; 1); pratinjau otomatis menghitung <code>ceil(N / (1 + N &times; e&sup2;))</code> dan diverifikasi ulang di server.
                    </small>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle mb-0" id="tableSampel">
                        <thead class="table-light text-center small">
                            <tr>
                                <th style="width: 190px;">Sub-Elemen</th>
                                <th style="width: 105px;">Jenis Data</th>
                                <th style="width: 185px;">Metode Pengambilan Data</th>
                                <th style="width: 155px;">Teknik Sampling</th>
                                <th style="width: 150px;">Penentuan Jumlah</th>
                                <th style="width: 85px;">Populasi (N)</th>
                                <th style="width: 90px;">Error (e)</th>
                                <th style="width: 90px;">Jml Sampel</th>
                                <th style="min-width: 210px;">Deskripsi Sampel (Spesifik &amp; Terukur)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rencana->sampels as $s): ?>
                                <?php $selectedMetode = is_array($s->metode) ? $s->metode : []; ?>
                                <tr class="sampel-row" data-sampel-id="<?php echo e($s->id); ?>">
                                    <td class="small">
                                        <strong class="text-primary"><?php echo e($s->subElemen->kode_sub ?? '-'); ?></strong>
                                        <div class="text-slate-700"><?php echo e($s->subElemen->nama_sub ?? '-'); ?></div>
                                    </td>
                                    <td>
                                        <select name="sampels[<?php echo e($s->id); ?>][jenis_data]" class="form-select form-select-sm">
                                            <option value="">--</option>
                                            <option value="primer" <?php echo $s->jenis_data === 'primer' ? 'selected' : ''; ?>>Primer</option>
                                            <option value="sekunder" <?php echo $s->jenis_data === 'sekunder' ? 'selected' : ''; ?>>Sekunder</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1 small">
                                            <?php foreach (\App\Models\AuditRencana::METODE_OPTIONS as $mKey => $mLabel): ?>
                                                <label class="form-check-label d-flex align-items-center gap-1">
                                                    <input type="checkbox" class="form-check-input mt-0"
                                                           name="sampels[<?php echo e($s->id); ?>][metode][]"
                                                           value="<?php echo e($mKey); ?>"
                                                           <?php echo in_array($mKey, $selectedMetode, true) ? 'checked' : ''; ?>>
                                                    <span><?php echo e($mLabel); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="sampels[<?php echo e($s->id); ?>][teknik_sampling]" class="form-select form-select-sm">
                                            <option value="">-- Pilih --</option>
                                            <?php foreach (\App\Models\AuditRencana::TEKNIK_SAMPLING_OPTIONS as $tKey => $tLabel): ?>
                                                <option value="<?php echo e($tKey); ?>" <?php echo $s->teknik_sampling === $tKey ? 'selected' : ''; ?>><?php echo e($tLabel); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="sampels[<?php echo e($s->id); ?>][metode_penentuan]" class="form-select form-select-sm sel-penentuan">
                                            <option value="">-- Pilih --</option>
                                            <?php foreach (\App\Models\AuditRencana::METODE_PENENTUAN_OPTIONS as $mpKey => $mpLabel): ?>
                                                <option value="<?php echo e($mpKey); ?>" <?php echo $s->metode_penentuan === $mpKey ? 'selected' : ''; ?>><?php echo e($mpLabel); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" min="1" name="sampels[<?php echo e($s->id); ?>][populasi_n]"
                                               class="form-control form-control-sm text-center inp-slovin-n"
                                               value="<?php echo e($s->populasi_n ?? ''); ?>">
                                    </td>
                                    <td>
                                        <input type="number" step="0.001" min="0.001" max="0.999" name="sampels[<?php echo e($s->id); ?>][margin_error_e]"
                                               class="form-control form-control-sm text-center inp-slovin-e"
                                               placeholder="0.05"
                                               value="<?php echo e($s->margin_error_e ?? ''); ?>">
                                    </td>
                                    <td>
                                        <input type="number" min="1" name="sampels[<?php echo e($s->id); ?>][jumlah_sampel]"
                                               class="form-control form-control-sm text-center fw-bold inp-jumlah-sampel"
                                               value="<?php echo e($s->jumlah_sampel ?? ''); ?>">
                                        <small class="text-primary d-block text-center lbl-slovin-preview" style="font-size: 0.7rem;"></small>
                                    </td>
                                    <td>
                                        <textarea name="sampels[<?php echo e($s->id); ?>][deskripsi_sampel]" class="form-control form-control-sm" rows="2" placeholder="Objek, lokasi, jumlah dokumen/personel..."><?php echo e($s->deskripsi_sampel ?? ''); ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 5: PENGESAHAN -->
        <div class="tab-pane fade" id="pane-pengesahan" role="tabpanel">
            <div class="card card-custom p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold text-slate-800 mb-0">
                            <i class="bi bi-pen-fill text-primary me-2"></i>Pengesahan Rencana Audit
                        </h5>
                        <small class="text-muted">Daftar penandatangan disalin otomatis dari Master Penandatangan sesuai klasifikasi usaha.</small>
                    </div>
                    <?php if (! $isReadOnly): ?>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-3" id="btnAddPengesahan">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Penandatangan
                        </button>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="tablePengesahan">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 55px;">No</th>
                                <th style="width: 240px;">Jabatan</th>
                                <th>Nama Penandatangan</th>
                                <th style="width: 180px;">Tanggal Tanda Tangan</th>
                                <?php if (! $isReadOnly): ?><th style="width: 55px;">Hapus</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pengesahanList as $idx => $p): ?>
                                <?php $ttdVal = ($p->tanggal_ttd instanceof \Carbon\Carbon) ? $p->tanggal_ttd->format('Y-m-d') : (string) ($p->tanggal_ttd ?? ''); ?>
                                <tr>
                                    <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                    <td><input type="text" name="pengesahans[<?php echo e($idx); ?>][jabatan]" class="form-control form-control-sm fw-semibold" value="<?php echo e($p->jabatan ?? ''); ?>"></td>
                                    <td><input type="text" name="pengesahans[<?php echo e($idx); ?>][nama]" class="form-control form-control-sm" value="<?php echo e($p->nama ?? ''); ?>"></td>
                                    <td><input type="date" name="pengesahans[<?php echo e($idx); ?>][tanggal_ttd]" class="form-control form-control-sm" value="<?php echo e($ttdVal); ?>"></td>
                                    <?php if (! $isReadOnly): ?>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</fieldset>

<?php if (! $isReadOnly): ?>
    <div class="card card-custom p-3 mt-4 sticky-bottom bg-white border-top shadow-lg d-flex flex-row align-items-center justify-content-between flex-wrap gap-2" style="z-index: 1010;">
        <div class="small text-muted">
            <i class="bi bi-info-circle text-primary me-1"></i> Simpan perubahan terlebih dahulu sebelum memfinalkan atau mencetak Rencana Audit.
        </div>
        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm">
            <i class="bi bi-save me-1"></i> Simpan Rencana Audit
        </button>
    </div>
</form>

<form action="<?php echo e(route('admin.audit-sesi.rencana.copy-tim', $sesi->id)); ?>" method="POST" id="formCopyTimTahap2" class="d-none">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
</form>
<?php endif; ?>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
document.addEventListener('DOMContentLoaded', function () {
    function renumberTable(tableEl) {
        if (!tableEl) return;
        tableEl.querySelectorAll('tbody tr').forEach((tr, idx) => {
            const numCell = tr.querySelector('.row-num');
            if (numCell) numCell.textContent = idx + 1;
        });
    }

    document.body.addEventListener('click', function (e) {
        const removeRow = e.target.closest('.btn-remove-row');
        if (removeRow) {
            const tr = removeRow.closest('tr');
            const table = tr ? tr.closest('table') : null;
            if (tr && table && table.querySelectorAll('tbody tr').length > 1) {
                tr.remove();
                renumberTable(table);
            }
        }
        const removeItem = e.target.closest('.btn-remove-item');
        if (removeItem) {
            const grp = removeItem.closest('.input-group');
            if (grp) grp.remove();
        }
    });

    // Add Area Lingkup Audit
    document.getElementById('btnAddAreaLingkup')?.addEventListener('click', function () {
        const wrap = document.getElementById('wrapAreaLingkup');
        if (!wrap) return;
        const div = document.createElement('div');
        div.className = 'input-group input-group-sm';
        div.innerHTML = '<input type="text" name="areas[]" class="form-control" placeholder="Contoh: Area Pit Tambang / Workshop / Gudang Handak"><button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-x-lg"></i></button>';
        wrap.appendChild(div);
    });

    // Add Dokumen Relevan
    document.getElementById('btnAddDokRelevan')?.addEventListener('click', function () {
        const wrap = document.getElementById('wrapDokRelevan');
        if (!wrap) return;
        const div = document.createElement('div');
        div.className = 'input-group input-group-sm';
        div.innerHTML = '<input type="text" name="dokumen_relevan[]" class="form-control" placeholder="Nama / nomor dokumen relevan"><button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-x-lg"></i></button>';
        wrap.appendChild(div);
    });

    // Copy Tim Tahap II submit trigger
    document.getElementById('btnTriggerCopyTim2')?.addEventListener('click', function () {
        if (confirm('Salin daftar anggota tim dari Tim Audit Tahap II (menggantikan daftar tim saat ini)?')) {
            document.getElementById('formCopyTimTahap2')?.submit();
        }
    });

    // Add Tim
    let timCounter = 100;
    const peranOptions = <?php echo json_encode(\App\Models\AuditRencana::PERAN_OPTIONS, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
    document.getElementById('btnAddTimRencana')?.addEventListener('click', function () {
        const table = document.getElementById('tableTimRencana');
        const tbody = table?.querySelector('tbody');
        if (!tbody) return;
        const idx = timCounter++;
        const tr = document.createElement('tr');
        const sel = document.createElement('select');
        sel.name = `tims[${idx}][peran]`;
        sel.className = 'form-select form-select-sm';
        Object.entries(peranOptions).forEach(([k, label]) => {
            const opt = document.createElement('option');
            opt.value = k;
            opt.textContent = label;
            if (k === 'auditor') opt.selected = true;
            sel.appendChild(opt);
        });
        tr.innerHTML = `<td class="text-center fw-bold row-num"></td><td><input type="text" name="tims[${idx}][nama]" class="form-control form-control-sm"></td><td><input type="text" name="tims[${idx}][jabatan]" class="form-control form-control-sm"></td><td></td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>`;
        tr.children[3].appendChild(sel);
        tbody.appendChild(tr);
        renumberTable(table);
    });

    // Add Jadwal
    let jadwalCounter = 100;
    const jenisKegiatanOptions = <?php echo json_encode(\App\Models\AuditRencana::JENIS_KEGIATAN_OPTIONS, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
    const elemenList = <?php echo json_encode($elemens->map(fn ($e) => ['id' => $e->id, 'label' => $e->kode_elemen . ' - ' . $e->nama_elemen])->values()->all(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
    const defaultTglMulai = <?php echo json_encode($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y-m-d') : '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    document.getElementById('btnAddJadwal')?.addEventListener('click', function () {
        const table = document.getElementById('tableJadwal');
        const tbody = table?.querySelector('tbody');
        if (!tbody) return;
        const idx = jadwalCounter++;
        const tr = document.createElement('tr');

        const selJenis = document.createElement('select');
        selJenis.name = `jadwals[${idx}][jenis_kegiatan]`;
        selJenis.className = 'form-select form-select-sm';
        Object.entries(jenisKegiatanOptions).forEach(([k, label]) => {
            const opt = document.createElement('option');
            opt.value = k;
            opt.textContent = label;
            if (k === 'audit_elemen') opt.selected = true;
            selJenis.appendChild(opt);
        });

        const selElemen = document.createElement('select');
        selElemen.name = `jadwals[${idx}][elemen_id]`;
        selElemen.className = 'form-select form-select-sm';
        const emptyOpt = document.createElement('option');
        emptyOpt.value = '';
        emptyOpt.textContent = '-- Opsional --';
        selElemen.appendChild(emptyOpt);
        elemenList.forEach(el => {
            const opt = document.createElement('option');
            opt.value = el.id;
            opt.textContent = el.label;
            selElemen.appendChild(opt);
        });

        tr.innerHTML = `<td><input type="number" min="1" name="jadwals[${idx}][hari_ke]" class="form-control form-control-sm text-center" value="1"></td><td><input type="date" name="jadwals[${idx}][tanggal]" class="form-control form-control-sm"></td><td><input type="time" name="jadwals[${idx}][jam_mulai]" class="form-control form-control-sm" value="08:00"></td><td><input type="time" name="jadwals[${idx}][jam_selesai]" class="form-control form-control-sm" value="17:00"></td><td></td><td><input type="text" name="jadwals[${idx}][kegiatan]" class="form-control form-control-sm"></td><td></td><td><input type="text" name="jadwals[${idx}][auditor]" class="form-control form-control-sm"></td><td><input type="text" name="jadwals[${idx}][auditi_pic]" class="form-control form-control-sm"></td><td><input type="text" name="jadwals[${idx}][lokasi]" class="form-control form-control-sm"></td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>`;
        tr.children[1].querySelector('input').value = defaultTglMulai;
        tr.children[4].appendChild(selJenis);
        tr.children[6].appendChild(selElemen);
        tbody.appendChild(tr);
    });

    // Add Pengesahan
    let pengesahanCounter = 100;
    document.getElementById('btnAddPengesahan')?.addEventListener('click', function () {
        const table = document.getElementById('tablePengesahan');
        const tbody = table?.querySelector('tbody');
        if (!tbody) return;
        const idx = pengesahanCounter++;
        const tr = document.createElement('tr');
        tr.innerHTML = `<td class="text-center fw-bold row-num"></td><td><input type="text" name="pengesahans[${idx}][jabatan]" class="form-control form-control-sm fw-semibold"></td><td><input type="text" name="pengesahans[${idx}][nama]" class="form-control form-control-sm"></td><td><input type="date" name="pengesahans[${idx}][tanggal_ttd]" class="form-control form-control-sm"></td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>`;
        tbody.appendChild(tr);
        renumberTable(table);
    });

    // Live Slovin preview (client-side hint only; server recalculates & validates)
    function updateSlovinRow(tr) {
        const sel = tr.querySelector('.sel-penentuan');
        const inpN = tr.querySelector('.inp-slovin-n');
        const inpE = tr.querySelector('.inp-slovin-e');
        const inpJml = tr.querySelector('.inp-jumlah-sampel');
        const lbl = tr.querySelector('.lbl-slovin-preview');
        if (!sel || !inpN || !inpE || !lbl) return;

        if (sel.value !== 'slovin') {
            lbl.textContent = '';
            return;
        }
        const nVal = parseInt(inpN.value, 10);
        const eVal = parseFloat(inpE.value);
        if (!isNaN(nVal) && nVal >= 1 && !isNaN(eVal) && eVal > 0 && eVal < 1) {
            const calc = Math.ceil(nVal / (1 + nVal * eVal * eVal));
            lbl.textContent = `Slovin: ${calc}`;
            if (inpJml && !inpJml.value) {
                inpJml.value = calc;
            }
        } else {
            lbl.textContent = 'Isi N & e';
        }
    }

    document.querySelectorAll('#tableSampel tbody tr.sampel-row').forEach(tr => {
        ['change', 'input'].forEach(evt => {
            tr.addEventListener(evt, function (e) {
                if (e.target.matches('.sel-penentuan, .inp-slovin-n, .inp-slovin-e')) {
                    updateSlovinRow(tr);
                }
            });
        });
        updateSlovinRow(tr);
    });
});
</script>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
