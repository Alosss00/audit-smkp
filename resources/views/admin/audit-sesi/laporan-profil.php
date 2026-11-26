<?php
$title = 'Profil & Informasi Umum Laporan Audit — SMKP Minerba';
ob_start();

$isAuditorRole = auth()->check() && auth()->user()->role === 'auditor';
$isAdmin = auth()->check() && auth()->user()->isAdmin();
$backRoute = $isAuditorRole
    ? route('auditor.audit-sesi.index')
    : route('admin.audit-sesi.index');

$perusahaan = $sesi->perusahaan;
$legalitas = $perusahaan?->legalitas;
$profil = $laporan->profil;
$peralatanList = $laporan->peralatans->isNotEmpty()
    ? $laporan->peralatans
    : collect([(object) ['kelompok' => '', 'jenis' => '', 'tipe' => '', 'kapasitas' => '', 'jumlah' => 0]]);
$areas = $rencana ? $rencana->areas : collect();
?>

<!-- Top Header -->
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
        <a href="<?php echo e($backRoute); ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Sesi Audit
        </a>
        <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
            <h2 class="fw-bold text-slate-800 mb-0">
                <i class="bi bi-building-gear text-primary me-2"></i>Profil &amp; Informasi Umum Laporan Audit
            </h2>
            <span class="badge bg-primary rounded-pill px-3 py-2">Bab 2 &amp; 3 Laporan</span>
            <span class="badge bg-light text-dark border rounded-pill px-3 py-2">Periode <?php echo e($sesi->tahun_periode); ?></span>
        </div>
        <p class="text-muted small mb-0 mt-1">
            Auditi: <strong><?php echo e($perusahaan->nama_perusahaan ?? $sesi->area_audit); ?></strong> &bull;
            Pelaksanaan: <strong><?php echo e($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('d/m/Y') : '-'); ?> s/d <?php echo e($sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('d/m/Y') : '-'); ?></strong>
        </p>
    </div>

    <?php if (! $isReadOnly): ?>
        <div class="d-flex flex-wrap gap-2">
            <?php if ($tahapAwal && $tahapAwal->jumlah_pekerja !== null): ?>
                <form action="<?php echo e(route('admin.audit-sesi.laporan.profil.copy-tahap-awal', $sesi->id)); ?>" method="POST" class="d-inline"
                      data-confirm="Salin jumlah pekerja (<?php echo e($tahapAwal->jumlah_pekerja); ?> orang) dari Dokumen Tahap Awal?">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="btn btn-outline-info text-dark rounded-3 px-3 fw-semibold shadow-sm">
                        <i class="bi bi-people me-1"></i> Salin Pekerja dari Tahap Awal
                    </button>
                </form>
            <?php endif; ?>
            <?php if ($prevSesi): ?>
                <form action="<?php echo e(route('admin.audit-sesi.laporan.profil.copy-prev-period', $sesi->id)); ?>" method="POST" class="d-inline"
                      data-confirm="Salin daftar peralatan dan narasi dari periode <?php echo e($prevSesi->tahun_periode); ?> (menggantikan daftar peralatan saat ini)?">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="btn btn-outline-primary rounded-3 px-3 fw-semibold shadow-sm">
                        <i class="bi bi-clock-history me-1"></i> Salin dari Periode <?php echo e($prevSesi->tahun_periode); ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($isReadOnly): ?>
    <div class="alert alert-info rounded-4 shadow-sm d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-eye-fill fs-5 text-info"></i>
        <div class="small">Mode <strong>Hanya Baca</strong> untuk Auditor Perusahaan. Anda dapat meninjau informasi profil laporan audit sesi ini.</div>
    </div>
<?php endif; ?>

<!-- Section 1: Ringkasan Domisili & Legalitas Perusahaan (Read-Only) -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 border-bottom pb-2">
        <h5 class="fw-bold text-slate-800 mb-0">
            <i class="bi bi-file-earmark-check text-primary me-2"></i>2.1 Domisili &amp; Legalitas Perusahaan
        </h5>
        <?php if ($isAdmin): ?>
            <a href="<?php echo e(route('admin.perusahaans.index')); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-pencil me-1"></i> Ubah di Master Perusahaan
            </a>
        <?php endif; ?>
    </div>
    <div class="row g-3 small">
        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100">
                <div class="fw-bold text-slate-800 mb-2">Identitas &amp; Alamat</div>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Nama Perusahaan</dt>
                    <dd class="col-7 fw-semibold"><?php echo e($perusahaan->nama_perusahaan ?? '-'); ?></dd>
                    <dt class="col-5 text-muted">Jenis Komoditas</dt>
                    <dd class="col-7"><?php echo e($perusahaan->jenis_komoditas ?? '-'); ?></dd>
                    <dt class="col-5 text-muted">KTT / PJO</dt>
                    <dd class="col-7"><?php echo e($perusahaan->nama_ktt_default ?? '-'); ?></dd>
                    <dt class="col-5 text-muted">Kantor Pusat</dt>
                    <dd class="col-7"><?php echo e($perusahaan->alamat_kantor_pusat ?? '-'); ?></dd>
                    <dt class="col-5 text-muted">Kantor Site</dt>
                    <dd class="col-7"><?php echo e($perusahaan->alamat_kantor_site ?? '-'); ?></dd>
                    <dt class="col-5 text-muted">Lokasi Tambang</dt>
                    <dd class="col-7 mb-0"><?php echo e($perusahaan->alamat_lokasi_tambang ?? '-'); ?></dd>
                </dl>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 h-100">
                <div class="fw-bold text-slate-800 mb-2">Legalitas &amp; Wilayah Izin</div>
                <?php if ($legalitas): ?>
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Jenis Izin</dt>
                        <dd class="col-7 fw-semibold"><?php echo e($legalitas->jenis_izin_label); ?></dd>
                        <dt class="col-5 text-muted">Nomor &amp; Tgl SK</dt>
                        <dd class="col-7"><?php echo e($legalitas->nomor_sk_izin ?? '-'); ?><?php echo $legalitas->tanggal_sk ? ' (' . e($legalitas->tanggal_sk->format('d/m/Y')) . ')' : ''; ?></dd>
                        <dt class="col-5 text-muted">Status &amp; Masa Berlaku</dt>
                        <dd class="col-7">
                            <?php echo e($legalitas->status_izin ?? '-'); ?>
                            <?php if ($legalitas->tanggal_mulai_berlaku || $legalitas->tanggal_berakhir): ?>
                                (<?php echo e($legalitas->tanggal_mulai_berlaku ? $legalitas->tanggal_mulai_berlaku->format('d/m/Y') : '-'); ?> s/d <?php echo e($legalitas->tanggal_berakhir ? $legalitas->tanggal_berakhir->format('d/m/Y') : '-'); ?>)
                            <?php endif; ?>
                        </dd>
                        <dt class="col-5 text-muted">Kode WIUP / Luas</dt>
                        <dd class="col-7">
                            <?php echo e($legalitas->kode_wiup ?? '-'); ?>
                            <?php if ($legalitas->luas_ha !== null): ?>
                                / <?php echo e(number_format((float) $legalitas->luas_ha, 2, ',', '.')); ?> Ha
                            <?php endif; ?>
                        </dd>
                        <dt class="col-5 text-muted">Wilayah Administratif</dt>
                        <dd class="col-7">
                            <?php
                            $wil = array_filter([$legalitas->lokasi_desa, $legalitas->lokasi_kecamatan, $legalitas->lokasi_kabupaten, $legalitas->lokasi_provinsi]);
                            echo e(!empty($wil) ? implode(', ', $wil) : '-');
                            ?>
                        </dd>
                        <dt class="col-5 text-muted">Studi Kelayakan</dt>
                        <dd class="col-7"><?php echo e($legalitas->nomor_persetujuan_studi_kelayakan ?? '-'); ?><?php echo $legalitas->tanggal_persetujuan_studi_kelayakan ? ' (' . e($legalitas->tanggal_persetujuan_studi_kelayakan->format('d/m/Y')) . ')' : ''; ?></dd>
                        <dt class="col-5 text-muted">Dok. Lingkungan</dt>
                        <dd class="col-7 mb-0"><?php echo e($legalitas->nomor_persetujuan_dokumen_lingkungan ?? '-'); ?><?php echo $legalitas->tanggal_persetujuan_dokumen_lingkungan ? ' (' . e($legalitas->tanggal_persetujuan_dokumen_lingkungan->format('d/m/Y')) . ')' : ''; ?></dd>
                    </dl>
                <?php else: ?>
                    <p class="text-muted mb-0">Data legalitas perusahaan belum dilengkapi di Master Perusahaan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Section 2: Daftar Area Lingkup Audit (Read-Only dari Rencana Audit) -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 border-bottom pb-2">
        <h5 class="fw-bold text-slate-800 mb-0">
            <i class="bi bi-geo-alt text-primary me-2"></i>3. Daftar Area Lingkup Audit (dari Rencana Audit)
        </h5>
        <?php if (! $isAuditorRole): ?>
            <a href="<?php echo e(route('admin.audit-sesi.rencana.edit', $sesi->id)); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-pencil me-1"></i> Ubah di Rencana Audit
            </a>
        <?php endif; ?>
    </div>
    <?php if ($areas->isNotEmpty()): ?>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($areas as $idx => $a): ?>
                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-semibold">
                    <?php echo e(($idx + 1) . '. ' . $a->nama_area); ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted small mb-0">Belum ada area lingkup audit terstruktur yang dicatat pada Rencana Audit sesi ini.</p>
    <?php endif; ?>
</div>

<!-- Editable Form: Bab 2.2 & 2.3 -->
<?php if (! $isReadOnly): ?>
<form action="<?php echo e(route('admin.audit-sesi.laporan.profil.update', $sesi->id)); ?>" method="POST" enctype="multipart/form-data" id="formProfilLaporan">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="_method" value="PUT">
<?php endif; ?>

<fieldset <?php echo $isReadOnly ? 'disabled' : ''; ?>>
    <!-- 2.2 Kegiatan, Produksi, Pekerja, Struktur Organisasi -->
    <div class="card card-custom p-4 mb-4">
        <h5 class="fw-bold text-slate-800 mb-3 border-bottom pb-2">
            <i class="bi bi-speedometer2 text-primary me-2"></i>2.2 Kegiatan, Produksi, Tenaga Kerja &amp; Struktur Organisasi
        </h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Kapasitas Produksi</label>
                <input type="number" step="0.01" min="0" name="kapasitas_produksi" class="form-control" value="<?php echo e(old('kapasitas_produksi', $profil?->kapasitas_produksi)); ?>" placeholder="Contoh: 500000">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Realisasi Produksi</label>
                <input type="number" step="0.01" min="0" name="realisasi_produksi" class="form-control" value="<?php echo e(old('realisasi_produksi', $profil?->realisasi_produksi)); ?>" placeholder="Contoh: 425000">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Satuan Produksi</label>
                <input type="text" name="satuan_produksi" class="form-control" value="<?php echo e(old('satuan_produksi', $profil?->satuan_produksi)); ?>" placeholder="Contoh: Ton / BCM / Oz">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Jumlah Pekerja (Total)</label>
                <input type="number" min="0" name="jumlah_pekerja" class="form-control" value="<?php echo e(old('jumlah_pekerja', $profil?->jumlah_pekerja)); ?>" placeholder="Jumlah total pekerja tambang">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Jumlah Pekerja Asing (TKA)</label>
                <input type="number" min="0" name="jumlah_pekerja_asing" class="form-control" value="<?php echo e(old('jumlah_pekerja_asing', $profil?->jumlah_pekerja_asing)); ?>" placeholder="Jumlah tenaga kerja asing (jika ada)">
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold">Narasi Kegiatan Pertambangan / Jasa Pertambangan</label>
                <textarea name="narasi_kegiatan" class="form-control" rows="4" placeholder="Uraikan jenis kegiatan operasional, metode penambangan/pengolahan, dan ruang lingkup aktivitas perusahaan..."><?php echo e(old('narasi_kegiatan', $profil?->narasi_kegiatan)); ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label small fw-bold">Bagan Struktur Organisasi (PDF / JPG / PNG, maks. 5 MB)</label>
                <?php if (! $isReadOnly): ?>
                    <input type="file" name="struktur_organisasi" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <?php endif; ?>
                <?php if ($profil && $profil->struktur_organisasi_path): ?>
                    <div class="mt-2 d-flex flex-wrap align-items-center gap-3 p-2 bg-light rounded-3 border">
                        <a href="<?php echo e($profil->struktur_organisasi_url); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-file-earmark-image me-1"></i> Lihat Berkas Struktur Organisasi
                        </a>
                        <span class="small text-muted font-monospace"><?php echo e(basename($profil->struktur_organisasi_path)); ?></span>
                        <?php if (! $isReadOnly): ?>
                            <div class="form-check ms-auto mb-0">
                                <input class="form-check-input" type="checkbox" name="hapus_struktur" id="chkHapusStruktur" value="1">
                                <label class="form-check-label small text-danger fw-semibold" for="chkHapusStruktur">Hapus berkas saat simpan</label>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <small class="text-muted">Belum ada berkas struktur organisasi yang diunggah.</small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Daftar Peralatan -->
    <div class="card card-custom p-4 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 border-bottom pb-2">
            <div>
                <h5 class="fw-bold text-slate-800 mb-0">
                    <i class="bi bi-truck text-primary me-2"></i>Daftar Peralatan Pertambangan
                </h5>
                <small class="text-muted">Total per kelompok dan grand total dihitung otomatis dari daftar peralatan.</small>
            </div>
            <?php if (! $isReadOnly): ?>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" id="btnAddPeralatan">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Peralatan
                </button>
            <?php endif; ?>
        </div>

        <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle mb-0" id="tablePeralatan">
                <thead class="table-light text-center small">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kelompok</th>
                        <th>Jenis Peralatan</th>
                        <th>Tipe / Model</th>
                        <th style="width: 150px;">Kapasitas</th>
                        <th style="width: 110px;">Jumlah</th>
                        <?php if (! $isReadOnly): ?><th style="width: 55px;">Hapus</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($peralatanList as $idx => $alat): ?>
                        <tr>
                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                            <td><input type="text" name="peralatans[<?php echo e($idx); ?>][kelompok]" class="form-control form-control-sm inp-alat-kelompok" value="<?php echo e($alat->kelompok ?? ''); ?>" placeholder="Contoh: Alat Berat / Angkut"></td>
                            <td><input type="text" name="peralatans[<?php echo e($idx); ?>][jenis]" class="form-control form-control-sm" value="<?php echo e($alat->jenis ?? ''); ?>" placeholder="Contoh: Excavator"></td>
                            <td><input type="text" name="peralatans[<?php echo e($idx); ?>][tipe]" class="form-control form-control-sm" value="<?php echo e($alat->tipe ?? ''); ?>" placeholder="Contoh: PC-200"></td>
                            <td><input type="text" name="peralatans[<?php echo e($idx); ?>][kapasitas]" class="form-control form-control-sm" value="<?php echo e($alat->kapasitas ?? ''); ?>" placeholder="Contoh: 20 Ton"></td>
                            <td><input type="number" min="0" name="peralatans[<?php echo e($idx); ?>][jumlah]" class="form-control form-control-sm text-end inp-alat-jumlah" value="<?php echo e($alat->jumlah ?? 0); ?>"></td>
                            <?php if (! $isReadOnly): ?>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-alat"><i class="bi bi-trash"></i></button></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="p-3 bg-light rounded-3 d-flex flex-wrap align-items-center justify-content-between gap-2" id="ringkasanPeralatan">
            <div class="d-flex flex-wrap gap-2" id="wrapSubtotalKelompok">
                <?php if (!empty($totalPerKelompok)): ?>
                    <?php foreach ($totalPerKelompok as $kelNama => $kelTotal): ?>
                        <span class="badge bg-white text-dark border rounded-pill px-3 py-2">
                            <?php echo e($kelNama); ?>: <strong><?php echo e($kelTotal); ?></strong> unit
                        </span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="small text-muted">Belum ada ringkasan peralatan.</span>
                <?php endif; ?>
            </div>
            <div class="fw-bold text-slate-800">
                Grand Total: <span class="badge bg-primary rounded-pill px-3 py-2 fs-6" id="lblGrandTotalPeralatan"><?php echo e($grandTotalPeralatan); ?></span> unit
            </div>
        </div>
    </div>

    <!-- 2.3 Narasi Penerapan SMKP Umum & per Elemen -->
    <div class="card card-custom p-4 mb-4">
        <h5 class="fw-bold text-slate-800 mb-3 border-bottom pb-2">
            <i class="bi bi-shield-check text-primary me-2"></i>2.3 Gambaran Penerapan SMKP Minerba
        </h5>
        <div class="mb-4">
            <label class="form-label small fw-bold">Gambaran Umum Penerapan SMKP</label>
            <textarea name="narasi_penerapan_smkp" class="form-control" rows="3" placeholder="Ringkasan umum penerapan SMKP Minerba di perusahaan..."><?php echo e(old('narasi_penerapan_smkp', $profil?->narasi_penerapan_smkp)); ?></textarea>
        </div>
        <h6 class="fw-bold text-slate-800 mb-3">Narasi Penerapan per Elemen SMKP</h6>
        <div class="row g-3">
            <?php foreach ($elemens as $el): ?>
                <?php $existingNarasi = $narasiMap->get($el->id)?->narasi ?? ''; ?>
                <div class="col-12">
                    <label class="form-label small fw-bold text-slate-800">
                        Elemen <?php echo e($el->kode_elemen); ?> — <?php echo e($el->nama_elemen); ?>
                    </label>
                    <textarea name="narasi_elemens[<?php echo e($el->id); ?>]" class="form-control" rows="2" placeholder="Uraikan penerapan Elemen <?php echo e($el->kode_elemen); ?>..."><?php echo e(old('narasi_elemens.' . $el->id, $existingNarasi)); ?></textarea>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</fieldset>

<?php if (! $isReadOnly): ?>
    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="<?php echo e($backRoute); ?>" class="btn btn-light rounded-pill px-4">Kembali</a>
        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
            <i class="bi bi-save me-1"></i> Simpan Profil Laporan
        </button>
    </div>
</form>
<?php endif; ?>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('tablePeralatan');
    const tbody = table?.querySelector('tbody');
    const wrapSubtotal = document.getElementById('wrapSubtotalKelompok');
    const lblGrand = document.getElementById('lblGrandTotalPeralatan');
    let alatCounter = <?php echo (int) $peralatanList->count() + 10; ?>;

    function renumberAndRecalc() {
        if (!tbody) return;
        const totals = {};
        let grand = 0;

        tbody.querySelectorAll('tr').forEach((tr, idx) => {
            const num = tr.querySelector('.row-num');
            if (num) num.textContent = idx + 1;

            const kelInput = tr.querySelector('.inp-alat-kelompok');
            const jmlInput = tr.querySelector('.inp-alat-jumlah');
            const kel = (kelInput?.value || '').trim();
            const jml = parseInt(jmlInput?.value || '0', 10) || 0;

            if (kel !== '' || jml > 0) {
                const key = kel !== '' ? kel : 'Lainnya';
                totals[key] = (totals[key] || 0) + Math.max(0, jml);
                grand += Math.max(0, jml);
            }
        });

        if (lblGrand) lblGrand.textContent = grand;
        if (wrapSubtotal) {
            wrapSubtotal.innerHTML = '';
            const keys = Object.keys(totals);
            if (keys.length === 0) {
                const sp = document.createElement('span');
                sp.className = 'small text-muted';
                sp.textContent = 'Belum ada ringkasan peralatan.';
                wrapSubtotal.appendChild(sp);
            } else {
                keys.forEach(k => {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-white text-dark border rounded-pill px-3 py-2';
                    badge.textContent = k + ': ' + totals[k] + ' unit';
                    wrapSubtotal.appendChild(badge);
                });
            }
        }
    }

    document.getElementById('btnAddPeralatan')?.addEventListener('click', function () {
        if (!tbody) return;
        const idx = alatCounter++;
        const tr = document.createElement('tr');
        tr.innerHTML = `<td class="text-center fw-bold row-num"></td><td><input type="text" name="peralatans[${idx}][kelompok]" class="form-control form-control-sm inp-alat-kelompok" placeholder="Contoh: Alat Berat / Angkut"></td><td><input type="text" name="peralatans[${idx}][jenis]" class="form-control form-control-sm" placeholder="Contoh: Excavator"></td><td><input type="text" name="peralatans[${idx}][tipe]" class="form-control form-control-sm" placeholder="Contoh: PC-200"></td><td><input type="text" name="peralatans[${idx}][kapasitas]" class="form-control form-control-sm" placeholder="Contoh: 20 Ton"></td><td><input type="number" min="0" name="peralatans[${idx}][jumlah]" class="form-control form-control-sm text-end inp-alat-jumlah" value="0"></td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-alat"><i class="bi bi-trash"></i></button></td>`;
        tbody.appendChild(tr);
        renumberAndRecalc();
    });

    tbody?.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-remove-alat');
        if (btn) {
            const tr = btn.closest('tr');
            if (tr && tbody.querySelectorAll('tr').length > 1) {
                tr.remove();
                renumberAndRecalc();
            }
        }
    });

    tbody?.addEventListener('input', function (e) {
        if (e.target.matches('.inp-alat-kelompok, .inp-alat-jumlah')) {
            renumberAndRecalc();
        }
    });
});
</script>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
