<?php
$title = 'Kelola Master Data Tahap Awal: ' . $perusahaan->nama_perusahaan . ' — SMKP Minerba';
ob_start();

$kinerja = $tahapAwal->kinerja_kp ?? [];
$faktorPenyesuaian = $tahapAwal->faktor_penyesuaian ?? [];
$tim1 = $tahapAwal->tim_audit_tahap_1 ?? [];
$kontak = $tahapAwal->kontak_awal ?? [];
$kelayakan = $tahapAwal->kelayakan_audit ?? [];
$kecukupan = $tahapAwal->kecukupan_dokumen ?? [];
$tim2 = $tahapAwal->tim_audit_tahap_2 ?? [];
$risksPresent = $tahapAwal->top_risks_present ?? [];
$risksFuture = $tahapAwal->top_risks_future ?? [];
$trends = $tahapAwal->trend_data ?? [];
$peralatan = $tahapAwal->unjuk_kerja_peralatan ?? [];
$klis = $tahapAwal->capaian_kli ?? [];
$auditPrev = $tahapAwal->data_audit_sebelumnya ?? [];
$harapanList = $tahapAwal->harapan_pemangku ?? [];
$thnPeriode = (int) date('Y');
$thnDepan = $thnPeriode + 1;
$thnSebelumnya = $thnPeriode - 1;
?>

<!-- Top Page Header -->
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
        <a href="<?php echo e(route('admin.master-tahap-awal.index')); ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Master Tahap Awal Perusahaan
        </a>
        <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
            <h3 class="fw-bold text-slate-800 mb-0">
                <i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Master Data Tahap Awal: <?php echo e($perusahaan->nama_perusahaan); ?>
            </h3>
            <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo e($tahapAwal->no_dokumen_header ?? 'TT-MGT-FRS-026L'); ?></span>
            <?php if ($tahapAwal->is_customized): ?>
                <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i>Kustom Tersimpan</span>
            <?php else: ?>
                <span class="badge bg-secondary rounded-pill px-3 py-2">Preset Standar</span>
            <?php endif; ?>
        </div>
        <p class="text-muted small mb-0 mt-1">
            Kategori: <strong><?php echo e($perusahaan->kategori); ?></strong> &bull;
            Kode: <strong><?php echo e($perusahaan->kode_perusahaan ?: '-'); ?></strong> &bull;
            Digunakan otomatis saat membuat Sesi Audit baru untuk perusahaan ini.
        </p>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- Quick Switcher Perusahaan -->
        <div style="min-width: 280px;">
            <select id="quickSwitchPerusahaan" class="form-select select-searchable" placeholder="-- Pindah Perusahaan Cepat --">
                <?php foreach ($allPerusahaans as $p): ?>
                    <option value="<?php echo e(route('admin.master-tahap-awal.edit', $p->id)); ?>" <?php echo $p->id === $perusahaan->id ? 'selected' : ''; ?>>
                        <?php echo e($p->nama_perusahaan); ?> <?php echo $p->kode_perusahaan ? '(' . e($p->kode_perusahaan) . ')' : ''; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <form action="<?php echo e(route('admin.master-tahap-awal.reset', $perusahaan->id)); ?>" method="POST" class="d-inline" data-confirm="Reset seluruh Master Data Tahap Awal perusahaan ini ke preset standar?">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <button type="submit" class="btn btn-outline-warning text-dark rounded-3 px-3" title="Reset ke Data Preset Standar">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Default
            </button>
        </form>
    </div>
</div>

<!-- Navigation Tabs (4 Steps) -->
<div class="card card-custom p-2 mb-4 bg-white">
    <ul class="nav nav-pills nav-fill gap-2" id="tahapAwalTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-3 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-bab1-btn" data-bs-toggle="pill" data-bs-target="#pane-bab1" type="button" role="tab">
                <i class="bi bi-building-check fs-5"></i>
                <span>I. Informasi Auditi & Mandays</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-bab2-btn" data-bs-toggle="pill" data-bs-target="#pane-bab2" type="button" role="tab">
                <i class="bi bi-file-earmark-check fs-5"></i>
                <span>II. Permulaan & Tinjauan Dokumen</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-bab3-btn" data-bs-toggle="pill" data-bs-target="#pane-bab3" type="button" role="tab">
                <i class="bi bi-shield-exclamation fs-5"></i>
                <span>III. Persiapan Lapangan & Data Risiko</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2"
                    id="tab-penutup-btn" data-bs-toggle="pill" data-bs-target="#pane-penutup" type="button" role="tab">
                <i class="bi bi-pen-fill fs-5"></i>
                <span>IV. Dokumen Kerja & Pengesahan</span>
            </button>
        </li>
    </ul>
</div>

<form action="<?php echo e(route('admin.master-tahap-awal.update', $perusahaan->id)); ?>" method="POST" id="formMasterTahapAwal">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="_method" value="PUT">

    <div class="tab-content" id="tahapAwalTabContent">

        <!-- =========================================================
             TAB 1: BAB I. INFORMASI PELAKSANAAN AUDIT SMKP & MANDAYS
             ========================================================= -->
        <div class="tab-pane fade show active" id="pane-bab1" role="tabpanel">
            <div class="row g-4">
                <!-- A.1 Data Perusahaan Auditi -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <h5 class="fw-bold text-slate-800 mb-3 border-bottom pb-2">
                            <i class="bi bi-building text-primary me-2"></i>I.A. Data Perusahaan Auditi
                        </h5>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Perusahaan Auditi</label>
                            <input type="text" name="nama_perusahaan_auditi" class="form-control" value="<?php echo e($tahapAwal->nama_perusahaan_auditi ?? $perusahaan->nama_perusahaan); ?>">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label class="form-label small fw-bold">Jenis Perizinan (Pisahkan baris jika > 1)</label>
                                <textarea name="jenis_perizinan" class="form-control" rows="2"><?php echo e($tahapAwal->jenis_perizinan); ?></textarea>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-bold">Jenis Komoditas</label>
                                <input type="text" name="jenis_komoditas" class="form-control" value="<?php echo e($tahapAwal->jenis_komoditas ?? 'Emas DMP'); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Kantor Site</label>
                            <textarea name="alamat_kantor_site" class="form-control" rows="2"><?php echo e($tahapAwal->alamat_kantor_site); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Lokasi Tambang</label>
                            <textarea name="alamat_lokasi_tambang" class="form-control" rows="2"><?php echo e($tahapAwal->alamat_lokasi_tambang); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Kantor Pusat</label>
                            <textarea name="alamat_kantor_pusat" class="form-control" rows="3"><?php echo e($tahapAwal->alamat_kantor_pusat); ?></textarea>
                        </div>
                        <div>
                            <label class="form-label small fw-bold">Format Default Periode Audit</label>
                            <input type="text" name="periode_audit_label" class="form-control" value="<?php echo e($tahapAwal->periode_audit_label); ?>">
                        </div>
                    </div>
                </div>

                <!-- A.2 Data Kinerja Keselamatan Pertambangan -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <h5 class="fw-bold text-slate-800 mb-3 border-bottom pb-2">
                            <i class="bi bi-activity text-danger me-2"></i>Data Kinerja Keselamatan Pertambangan
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Kecelakaan Cidera Ringan</label>
                                <input type="text" name="kinerja_kp[cidera_ringan]" class="form-control" value="<?php echo e($kinerja['cidera_ringan'] ?? '0'); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Kecelakaan Cidera Berat</label>
                                <input type="text" name="kinerja_kp[cidera_berat]" class="form-control" value="<?php echo e($kinerja['cidera_berat'] ?? '0'); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Kecelakaan Berakibat Mati</label>
                                <input type="text" name="kinerja_kp[mati]" class="form-control" value="<?php echo e($kinerja['mati'] ?? '2'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold"><em>Frequency Rate</em> (FR) Kecelakaan Tambang</label>
                                <input type="text" name="kinerja_kp[fr]" class="form-control" value="<?php echo e($kinerja['fr'] ?? '0.33'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold"><em>Severity Rate</em> (SR) Kecelakaan Tambang</label>
                                <input type="text" name="kinerja_kp[sr]" class="form-control" value="<?php echo e($kinerja['sr'] ?? '1,973.19'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Jumlah Kejadian Berbahaya</label>
                                <input type="text" name="kinerja_kp[kejadian_berbahaya]" class="form-control" value="<?php echo e($kinerja['kejadian_berbahaya'] ?? '1'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold"><em>Absence Severity Rate</em> (ASR)</label>
                                <input type="text" name="kinerja_kp[asr]" class="form-control" value="<?php echo e($kinerja['asr'] ?? '16.97'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold"><em>Morbidity Frequency Rate</em> (MFR)</label>
                                <input type="text" name="kinerja_kp[mfr]" class="form-control" value="<?php echo e($kinerja['mfr'] ?? '16.97'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Kejadian Akibat Penyakit Tenaga Kerja</label>
                                <input type="text" name="kinerja_kp[penyakit_tk]" class="form-control" value="<?php echo e($kinerja['penyakit_tk'] ?? '0'); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold">Frekuensi Penyakit Akibat Kerja (PAK)</label>
                                <input type="text" name="kinerja_kp[frekuensi_pak]" class="form-control" value="<?php echo e($kinerja['frekuensi_pak'] ?? '0'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- A.3 Dasar Faktor Penyesuaian -->
                <div class="col-12">
                    <div class="card card-custom p-4">
                        <h5 class="fw-bold text-slate-800 mb-3">
                            <i class="bi bi-sliders text-info me-2"></i>Dasar Faktor Penyesuaian (7 Kondisi Kepdirjen 185)
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th class="text-start">Kondisi</th>
                                        <th style="width: 110px;">Ya</th>
                                        <th style="width: 110px;">Tidak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kondisiPenyesuaian as $no => $kondisiText): ?>
                                        <?php $val = strtolower($faktorPenyesuaian[$no] ?? 'tidak'); ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?php echo e($no); ?></td>
                                            <td><?php echo e($kondisiText); ?></td>
                                            <td class="text-center">
                                                <input class="form-check-input kondisi-radio" type="radio"
                                                       name="faktor_penyesuaian[<?php echo e($no); ?>]" value="ya"
                                                       <?php echo e($val === 'ya' ? 'checked' : ''); ?>>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input kondisi-radio" type="radio"
                                                       name="faktor_penyesuaian[<?php echo e($no); ?>]" value="tidak"
                                                       <?php echo e($val !== 'ya' ? 'checked' : ''); ?>>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- B. Perhitungan Hari Kerja Audit dan Jumlah Auditor -->
                <div class="col-12">
                    <div class="card card-custom p-4 border-start border-4 border-primary">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-calculator text-primary me-2"></i>I.B. Perhitungan Hari Kerja Audit (Mandays) dan Jumlah Auditor
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnHitungMandaysOtomatis">
                                <i class="bi bi-magic me-1"></i> Hitung Otomatis Total & Alokasi
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Jumlah Pekerja Auditi</label>
                                <input type="number" name="jumlah_pekerja" id="inpJumlahPekerja" class="form-control" value="<?php echo e($tahapAwal->jumlah_pekerja ?? 3086); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Kelas Risiko</label>
                                <select name="kelas_risiko" id="inpKelasRisiko" class="form-select">
                                    <?php foreach (['Tinggi', 'Sedang', 'Rendah'] as $kr): ?>
                                        <option value="<?php echo e($kr); ?>" <?php echo e(($tahapAwal->kelas_risiko ?? 'Tinggi') === $kr ? 'selected' : ''); ?>><?php echo e($kr); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Mandays (Hari)</label>
                                <input type="number" step="0.1" name="mandays_awal" id="inpMandaysAwal" class="form-control" value="<?php echo e($tahapAwal->mandays_awal ?? 25); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Faktor Penyesuaian (Hari)</label>
                                <input type="number" step="0.1" name="faktor_penyesuaian_hari" id="inpFaktorHari" class="form-control" value="<?php echo e($tahapAwal->faktor_penyesuaian_hari ?? 2.5); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Total Mandays (Hari)</label>
                                <input type="number" step="0.1" name="total_mandays" id="inpTotalMandays" class="form-control bg-light fw-bold" value="<?php echo e($tahapAwal->total_mandays ?? 27.5); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Alokasi Mandays Tahap I (Maks. 10% Total)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" name="alokasi_tahap_1" id="inpAlokasiTahap1" class="form-control" value="<?php echo e($tahapAwal->alokasi_tahap_1 ?? 3); ?>">
                                    <span class="input-group-text">Hari</span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Alokasi Mandays Tahap II (Min. 90% Total & Rincian Auditor)</label>
                                <input type="text" name="alokasi_tahap_2" id="inpAlokasiTahap2" class="form-control" value="<?php echo e($tahapAwal->alokasi_tahap_2 ?? '24,5 Hari ( 4.9 Hari, 5 Auditor)'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================
             TAB 2: BAB II. HASIL PERMULAAN AUDIT & PENINJAUAN DOKUMEN
             ========================================================= -->
        <div class="tab-pane fade" id="pane-bab2" role="tabpanel">
            <div class="row g-4">
                <!-- II.A Penetapan Tim Audit Tahap I -->
                <div class="col-12">
                    <div class="card card-custom p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-people-fill text-primary me-2"></i>II.A. Penetapan Tim Audit Tahap I & Surat Pengangkatan KTT
                            </h5>
                            <button type="button" class="btn btn-sm btn-primary rounded-3" id="btnAddTim1">
                                <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota Tim Tahap I
                            </button>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Nomor Surat Pengangkatan Tim Audit (SK KTT)</label>
                                <input type="text" name="no_surat_tugas" class="form-control" value="<?php echo e($tahapAwal->no_surat_tugas); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Tanggal Surat Pengangkatan</label>
                                <input type="date" name="tanggal_surat_tugas" class="form-control" value="<?php echo e($tahapAwal->tanggal_surat_tugas ? $tahapAwal->tanggal_surat_tugas->format('Y-m-d') : '2024-09-25'); ?>">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tableTim1">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 55px;">No</th>
                                        <th>Nama Auditor</th>
                                        <th style="width: 220px;">Jabatan</th>
                                        <th style="width: 320px;">Nomor Registrasi Auditor</th>
                                        <th style="width: 60px;">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tim1 as $idx => $anggota): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><input type="text" name="tim_audit_tahap_1[<?php echo e($idx); ?>][nama]" class="form-control form-control-sm" value="<?php echo e($anggota['nama'] ?? ''); ?>"></td>
                                            <td>
                                                <select name="tim_audit_tahap_1[<?php echo e($idx); ?>][jabatan]" class="form-select form-select-sm">
                                                    <option value="Ketua Tim Audit" <?php echo e(($anggota['jabatan'] ?? '') === 'Ketua Tim Audit' ? 'selected' : ''); ?>>Ketua Tim Audit</option>
                                                    <option value="Auditor" <?php echo e(($anggota['jabatan'] ?? 'Auditor') === 'Auditor' ? 'selected' : ''); ?>>Auditor</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="tim_audit_tahap_1[<?php echo e($idx); ?>][no_registrasi]" class="form-control form-control-sm font-monospace" value="<?php echo e($anggota['no_registrasi'] ?? ''); ?>"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- II.B Pelaksanaan Kontak Awal dengan Auditi -->
                <div class="col-12">
                    <div class="card card-custom p-4">
                        <h5 class="fw-bold text-slate-800 mb-3">
                            <i class="bi bi-calendar2-event text-info me-2"></i>II.B. Pelaksanaan Kontak Awal dengan Auditi
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Hari / Tanggal Kontak Awal & Evaluasi Kelayakan</label>
                                <input type="text" name="kontak_awal[hari_tanggal]" class="form-control" value="<?php echo e($kontak['hari_tanggal'] ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Media</label>
                                <input type="text" name="kontak_awal[media]" class="form-control" value="<?php echo e($kontak['media'] ?? 'Meeting'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Perwakilan Auditi</label>
                                <input type="text" name="kontak_awal[perwakilan_auditi]" class="form-control" value="<?php echo e($kontak['perwakilan_auditi'] ?? 'Tejo Prihantoro'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Jabatan Perwakilan Auditi</label>
                                <input type="text" name="kontak_awal[jabatan_perwakilan]" class="form-control" value="<?php echo e($kontak['jabatan_perwakilan'] ?? 'Kepala Teknik Tambang'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- II.C Penentuan Kelayakan Audit -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <h5 class="fw-bold text-slate-800 mb-3">
                            <i class="bi bi-clipboard2-check text-success me-2"></i>II.C. Penentuan Kelayakan Audit (7 Indikator)
                        </h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th class="text-start">Indikator Kelayakan Audit</th>
                                        <th style="width: 190px;">Hasil Evaluasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($indikatorKelayakan as $no => $indText): ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?php echo e($no); ?></td>
                                            <td class="small"><?php echo e($indText); ?></td>
                                            <td>
                                                <input type="text" name="kelayakan_audit[<?php echo e($no); ?>]" class="form-control form-control-sm text-center fw-semibold"
                                                       value="<?php echo e($kelayakan[$no] ?? 'Tersedia dan Layak'); ?>" list="listOpsiKelayakan">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <datalist id="listOpsiKelayakan">
                                <option value="Tersedia dan Layak">
                                <option value="Baik">
                                <option value="Tersedia">
                                <option value="Cukup">
                                <option value="Belum Tersedia">
                            </datalist>
                        </div>
                        <div class="p-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                            <span class="small fw-bold">Kesimpulan Kelayakan Audit:</span>
                            <select name="kesimpulan_kelayakan" class="form-select form-select-sm w-auto fw-bold">
                                <option value="layak" <?php echo e(($tahapAwal->kesimpulan_kelayakan ?? 'layak') === 'layak' ? 'selected' : ''); ?>>Layak untuk dilaksanakan</option>
                                <option value="tidak_layak" <?php echo e(($tahapAwal->kesimpulan_kelayakan ?? '') === 'tidak_layak' ? 'selected' : ''); ?>>Tidak Layak untuk dilaksanakan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- II.D Penentuan Kecukupan Dokumentasi -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <h5 class="fw-bold text-slate-800 mb-3">
                            <i class="bi bi-folder-check text-primary me-2"></i>II.D. Penentuan Kecukupan Dokumentasi (7 Elemen SMKP)
                        </h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th class="text-start">Peninjauan Dokumentasi</th>
                                        <th style="width: 170px;">Hasil Evaluasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($elemenDokumentasi as $no => $elText): ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?php echo e($no); ?></td>
                                            <td class="small fw-semibold"><?php echo e($elText); ?></td>
                                            <td>
                                                <select name="kecukupan_dokumen[<?php echo e($no); ?>]" class="form-select form-select-sm fw-semibold">
                                                    <?php foreach (['Baik', 'Cukup', 'Kurang'] as $opt): ?>
                                                        <option value="<?php echo e($opt); ?>" <?php echo e(($kecukupan[$no] ?? 'Baik') === $opt ? 'selected' : ''); ?>><?php echo e($opt); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                            <span class="small fw-bold">Kesimpulan Evaluasi Dokumentasi:</span>
                            <select name="kesimpulan_kecukupan" class="form-select form-select-sm w-auto fw-bold">
                                <option value="dapat_dilanjutkan" <?php echo e(($tahapAwal->kesimpulan_kecukupan ?? 'dapat_dilanjutkan') === 'dapat_dilanjutkan' ? 'selected' : ''); ?>>Dapat dilanjutkan*) ke tahap berikutnya</option>
                                <option value="tidak_dapat_dilanjutkan" <?php echo e(($tahapAwal->kesimpulan_kecukupan ?? '') === 'tidak_dapat_dilanjutkan' ? 'selected' : ''); ?>>Belum dapat dilanjutkan</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================
             TAB 3: BAB III. PERSIAPAN AUDIT LAPANGAN & DATA RISIKO
             ========================================================= -->
        <div class="tab-pane fade" id="pane-bab3" role="tabpanel">
            <div class="row g-4">
                <!-- III.A Penetapan Tim Audit Tahap II -->
                <div class="col-12">
                    <div class="card card-custom p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-people text-primary me-2"></i>III.A. Penetapan Tim Audit Tahap II (Pelaksanaan Lapangan)
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-info text-dark rounded-3 fw-semibold" id="btnCopyFromTim1">
                                    <i class="bi bi-files me-1"></i> Salin dari Tim Audit Tahap I
                                </button>
                                <button type="button" class="btn btn-sm btn-primary rounded-3" id="btnAddTim2">
                                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota Tim Tahap II
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tableTim2">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 55px;">No</th>
                                        <th>Nama Auditor</th>
                                        <th style="width: 220px;">Jabatan</th>
                                        <th style="width: 320px;">Nomor Registrasi Auditor</th>
                                        <th style="width: 60px;">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tim2 as $idx => $anggota): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><input type="text" name="tim_audit_tahap_2[<?php echo e($idx); ?>][nama]" class="form-control form-control-sm" value="<?php echo e($anggota['nama'] ?? ''); ?>"></td>
                                            <td>
                                                <select name="tim_audit_tahap_2[<?php echo e($idx); ?>][jabatan]" class="form-select form-select-sm">
                                                    <option value="Ketua Tim Audit" <?php echo e(($anggota['jabatan'] ?? '') === 'Ketua Tim Audit' ? 'selected' : ''); ?>>Ketua Tim Audit</option>
                                                    <option value="Auditor" <?php echo e(($anggota['jabatan'] ?? 'Auditor') === 'Auditor' ? 'selected' : ''); ?>>Auditor</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="tim_audit_tahap_2[<?php echo e($idx); ?>][no_registrasi]" class="form-control form-control-sm font-monospace" value="<?php echo e($anggota['no_registrasi'] ?? ''); ?>"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- III.B.1 Top Risks Periode Audit Berjalan (Risk of Present) -->
                <div class="col-12">
                    <div class="card card-custom p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <h5 class="fw-bold text-slate-800 mb-0">
                                    <i class="bi bi-fire text-danger me-2"></i>Data Top Risks Aspek KP: Periode Berjalan (<em>Risk of Present</em>)
                                </h5>
                                <small class="text-muted">Critical point untuk penetapan metode dan sampel pembuktian primer pada periode berjalan.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-3" id="btnAddRiskPresent">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris Risiko
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tableRiskPresent">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Kegiatan</th>
                                        <th style="width: 260px;">Risiko</th>
                                        <th style="width: 140px;">Nilai Risiko Awal</th>
                                        <th style="width: 140px;">Nilai Risiko Sisa</th>
                                        <th style="width: 55px;">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($risksPresent as $idx => $rp): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><textarea name="top_risks_present[<?php echo e($idx); ?>][kegiatan]" class="form-control form-control-sm" rows="2"><?php echo e($rp['kegiatan'] ?? ''); ?></textarea></td>
                                            <td><input type="text" name="top_risks_present[<?php echo e($idx); ?>][risiko]" class="form-control form-control-sm" value="<?php echo e($rp['risiko'] ?? ''); ?>"></td>
                                            <td><input type="text" name="top_risks_present[<?php echo e($idx); ?>][nilai_awal]" class="form-control form-control-sm text-center" value="<?php echo e($rp['nilai_awal'] ?? ''); ?>"></td>
                                            <td><input type="text" name="top_risks_present[<?php echo e($idx); ?>][nilai_sisa]" class="form-control form-control-sm text-center" value="<?php echo e($rp['nilai_sisa'] ?? ''); ?>"></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- III.B.2 Top Risks Rencana Kegiatan Tahun Depan (Risk of Future) -->
                <div class="col-12">
                    <div class="card card-custom p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <h5 class="fw-bold text-slate-800 mb-0">
                                    <i class="bi bi-compass text-warning me-2"></i>Data Top Risks Aspek KP: Rencana Kegiatan Mendatang (<em>Risk of Future</em>)
                                </h5>
                                <small class="text-muted">Penilaian risiko untuk rencana kegiatan operasional tahun berikutnya.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-3" id="btnAddRiskFuture">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris Risiko
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tableRiskFuture">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Kegiatan</th>
                                        <th style="width: 260px;">Risiko</th>
                                        <th style="width: 140px;">Nilai Risiko Awal</th>
                                        <th style="width: 140px;">Nilai Risiko Sisa</th>
                                        <th style="width: 55px;">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($risksFuture as $idx => $rf): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><textarea name="top_risks_future[<?php echo e($idx); ?>][kegiatan]" class="form-control form-control-sm" rows="2"><?php echo e($rf['kegiatan'] ?? ''); ?></textarea></td>
                                            <td><input type="text" name="top_risks_future[<?php echo e($idx); ?>][risiko]" class="form-control form-control-sm" value="<?php echo e($rf['risiko'] ?? ''); ?>"></td>
                                            <td><input type="text" name="top_risks_future[<?php echo e($idx); ?>][nilai_awal]" class="form-control form-control-sm text-center" value="<?php echo e($rf['nilai_awal'] ?? ''); ?>"></td>
                                            <td><input type="text" name="top_risks_future[<?php echo e($idx); ?>][nilai_sisa]" class="form-control form-control-sm text-center" value="<?php echo e($rf['nilai_sisa'] ?? ''); ?>"></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- III.B.3 Enam Tabel Data Trend -->
                <?php
                    $trendSections = [
                        'faktor_penyebab'  => ['title' => 'Trend Faktor Penyebab Near Miss / Property Damage / Kecelakaan', 'col' => 'Faktor Penyebab Dominan'],
                        'lokasi'           => ['title' => 'Trend Lokasi Terjadinya Insiden / Kecelakaan', 'col' => 'Lokasi'],
                        'jenis_kegiatan'   => ['title' => 'Trend Jenis Kegiatan terkait Insiden / Kecelakaan', 'col' => 'Jenis Kegiatan'],
                        'jenis_jabatan'    => ['title' => 'Trend Jenis Jabatan terkait Insiden / Kecelakaan', 'col' => 'Jenis Jabatan'],
                        'deviasi_inspeksi' => ['title' => 'Trend Deviasi KP Berdasarkan Hasil Temuan Inspeksi', 'col' => 'Deviasi Penerapan KP Hasil Inspeksi'],
                        'faktor_pak'       => ['title' => 'Trend Faktor Penyebab Kejadian Penyakit Tenaga Kerja / PAK', 'col' => 'Faktor Penyebab Dominan'],
                    ];
                ?>
                <?php foreach ($trendSections as $tKey => $tMeta): ?>
                    <?php $tItems = !empty($trends[$tKey]) ? $trends[$tKey] : ['']; ?>
                    <div class="col-md-6">
                        <div class="card card-custom p-4 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold text-slate-800 mb-0"><?php echo e($tMeta['title']); ?></h6>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-add-trend" data-trend-key="<?php echo e($tKey); ?>">
                                    <i class="bi bi-plus"></i> Tambah
                                </button>
                            </div>
                            <table class="table table-sm table-bordered align-middle mb-0" id="tableTrend_<?php echo e($tKey); ?>">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th class="text-start"><?php echo e($tMeta['col']); ?></th>
                                        <th style="width: 45px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tItems as $idx => $itemVal): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><input type="text" name="trend_data[<?php echo e($tKey); ?>][]" class="form-control form-control-sm" value="<?php echo e($itemVal); ?>"></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm text-danger btn-remove-row"><i class="bi bi-x-lg"></i></button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- III.B.4 Data Unjuk Kerja Peralatan Pertambangan (PA & MA) -->
                <div class="col-lg-7">
                    <div class="card card-custom p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-truck-flatbed text-primary me-2"></i>Data Unjuk Kerja Peralatan Pertambangan (PA & MA)
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-3" id="btnAddPeralatan">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Peralatan
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tablePeralatan">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th class="text-start">Jenis Peralatan</th>
                                        <th style="width: 120px;"><em>Physical Availability</em></th>
                                        <th style="width: 120px;"><em>Mechanical Availability</em></th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($peralatan as $idx => $alat): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><input type="text" name="unjuk_kerja_peralatan[<?php echo e($idx); ?>][jenis_peralatan]" class="form-control form-control-sm" value="<?php echo e($alat['jenis_peralatan'] ?? ''); ?>"></td>
                                            <td><input type="text" name="unjuk_kerja_peralatan[<?php echo e($idx); ?>][pa]" class="form-control form-control-sm text-center" value="<?php echo e($alat['pa'] ?? ''); ?>"></td>
                                            <td><input type="text" name="unjuk_kerja_peralatan[<?php echo e($idx); ?>][ma]" class="form-control form-control-sm text-center" value="<?php echo e($alat['ma'] ?? ''); ?>"></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- III.B.5 Data Capaian Key Leading Indicator (KLI) -->
                <div class="col-lg-5">
                    <div class="card card-custom p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-speedometer2 text-success me-2"></i>Capaian <em>Key Leading Indicator</em>
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-3" id="btnAddKli">
                                <i class="bi bi-plus-lg me-1"></i> Tambah KLI
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tableKli">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th class="text-start"><em>Key Leading Indicator</em></th>
                                        <th style="width: 140px;">Status Pencapaian</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($klis as $idx => $kli): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><input type="text" name="capaian_kli[<?php echo e($idx); ?>][indikator]" class="form-control form-control-sm" value="<?php echo e($kli['indikator'] ?? ''); ?>"></td>
                                            <td><input type="text" name="capaian_kli[<?php echo e($idx); ?>][capaian]" class="form-control form-control-sm text-center" value="<?php echo e($kli['capaian'] ?? ''); ?>"></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- III.B.6 Data Audit Internal SMKP Tahun Sebelumnya -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <h5 class="fw-bold text-slate-800 mb-3">
                            <i class="bi bi-clock-history text-primary me-2"></i>Data Audit Internal SMKP Tahun Sebelumnya
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Tahun Audit Sebelumnya</label>
                                <input type="text" name="data_audit_sebelumnya[tahun]" class="form-control" value="<?php echo e($auditPrev['tahun'] ?? $thnSebelumnya); ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">1. Capaian Nilai Audit</label>
                                <input type="text" name="data_audit_sebelumnya[nilai_audit]" class="form-control fw-bold" value="<?php echo e($auditPrev['nilai_audit'] ?? '79.50 %'); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">2. Status Penyelesaian Tindak Lanjut Ketidaksesuaian Kategori Kritikal</label>
                                <input type="text" name="data_audit_sebelumnya[status_kritikal]" class="form-control" value="<?php echo e($auditPrev['status_kritikal'] ?? 'Tidak ada temuan kritikal'); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">3. Status Penyelesaian Tindak Lanjut Ketidaksesuaian Kategori Mayor</label>
                                <input type="text" name="data_audit_sebelumnya[status_mayor]" class="form-control" value="<?php echo e($auditPrev['status_mayor'] ?? 'Tidak ada temuan Mayor'); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">4. Status Penyelesaian Tindak Lanjut Ketidaksesuaian Kategori Minor</label>
                                <input type="text" name="data_audit_sebelumnya[status_minor]" class="form-control" value="<?php echo e($auditPrev['status_minor'] ?? '98% temuan sudah ditindaklanjuti'); ?>">
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small fw-bold mb-0">5. Catatan Hasil Evaluasi dari Instansi Pembina Sektor</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" id="btnAddCatatanInstansi">
                                        <i class="bi bi-plus"></i> Tambah Poin
                                    </button>
                                </div>
                                <div id="wrapCatatanInstansi" class="d-flex flex-column gap-2">
                                    <?php foreach (($auditPrev['catatan_instansi'] ?? ['']) as $cInst): ?>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="data_audit_sebelumnya[catatan_instansi][]" class="form-control" value="<?php echo e($cInst); ?>">
                                            <button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-x-lg"></i></button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- III.B.7 Daftar Harapan dan Kebutuhan Pemangku Kepentingan -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-chat-quote text-info me-2"></i>Daftar Harapan & Kebutuhan Pemangku Kepentingan
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-info text-dark rounded-3" id="btnAddHarapan">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Masukan
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tableHarapan">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th class="text-start">Masukan dari Pemangku Kepentingan</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($harapanList as $idx => $hrp): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><textarea name="harapan_pemangku[]" class="form-control form-control-sm" rows="2"><?php echo e($hrp); ?></textarea></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================
             TAB 4: BAB III.C DOKUMEN KERJA, PENUTUP & METADATA DOKUMEN
             ========================================================= -->
        <div class="tab-pane fade" id="pane-penutup" role="tabpanel">
            <div class="row g-4">
                <!-- Pengesahan Berita Acara -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <h5 class="fw-bold text-slate-800 mb-3 border-bottom pb-2">
                            <i class="bi bi-award text-primary me-2"></i>Pengesahan Berita Acara (Penutup)
                        </h5>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tanggal Default Berita Acara Tahap Awal</label>
                            <input type="date" name="tanggal_berita_acara" class="form-control" value="<?php echo e($tahapAwal->tanggal_berita_acara ? $tahapAwal->tanggal_berita_acara->format('Y-m-d') : $thnDepan . '-01-04'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Ketua Tim Audit (Penandatangan 1)</label>
                            <input type="text" name="ketua_tim_audit" class="form-control" value="<?php echo e($tahapAwal->ketua_tim_audit ?? 'Fiska Polii'); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Kepala Teknik Tambang / KTT (Penandatangan 2)</label>
                            <input type="text" name="nama_ktt" class="form-control" value="<?php echo e($tahapAwal->nama_ktt ?? 'Tejo Prihantoro'); ?>">
                        </div>
                    </div>
                </div>

                <!-- Metadata Kop & Footer Dokumen Terkendali -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <h5 class="fw-bold text-slate-800 mb-3 border-bottom pb-2">
                            <i class="bi bi-file-earmark-medical text-secondary me-2"></i>Metadata Kop & Footer Dokumen Terkendali
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">No. Dokumen (Header)</label>
                                <input type="text" name="no_dokumen_header" class="form-control font-monospace" value="<?php echo e($tahapAwal->no_dokumen_header ?? 'TT-MGT-FRS-026L'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">No. Dokumen (Footer)</label>
                                <input type="text" name="no_dokumen_footer" class="form-control font-monospace" value="<?php echo e($tahapAwal->no_dokumen_footer ?? 'TT-MGT-FRS-026K'); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">No. Revisi</label>
                                <input type="text" name="no_revisi" class="form-control" value="<?php echo e($tahapAwal->no_revisi ?? '01'); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Tanggal Terbit</label>
                                <input type="date" name="tanggal_terbit" class="form-control" value="<?php echo e($tahapAwal->tanggal_terbit ? $tahapAwal->tanggal_terbit->format('Y-m-d') : '2025-05-07'); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Tanggal Tinjau Ulang</label>
                                <input type="date" name="tanggal_tinjau_ulang" class="form-control" value="<?php echo e($tahapAwal->tanggal_tinjau_ulang ? $tahapAwal->tanggal_tinjau_ulang->format('Y-m-d') : '2028-05-07'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Sticky Action Bar -->
    <div class="card card-custom p-3 mt-4 sticky-bottom bg-white border-top shadow-lg d-flex flex-row align-items-center justify-content-between flex-wrap gap-2" style="z-index: 1010;">
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" name="sync_active_sesi" id="chkSyncActiveSesi" value="1" checked>
            <label class="form-check-label small fw-semibold text-slate-800" for="chkSyncActiveSesi">
                Sinkronisasikan juga perubahan ini ke Sesi Audit milik <strong><?php echo e($perusahaan->nama_perusahaan); ?></strong> yang sudah berjalan (<?php echo e($perusahaan->auditSesis->count()); ?> sesi)
            </label>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('admin.master-tahap-awal.index')); ?>" class="btn btn-light border rounded-3 px-4 fw-semibold">
                Kembali ke Daftar
            </a>
            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i> Simpan Master Data Perusahaan
            </button>
        </div>
    </div>
</form>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
document.addEventListener('DOMContentLoaded', function () {
    // Quick Switch Perusahaan
    const quickSwitch = document.getElementById('quickSwitchPerusahaan');
    if (quickSwitch) {
        quickSwitch.addEventListener('change', function () {
            if (this.value) {
                window.location.href = this.value;
            }
        });
    }

    function renumberTable(tableEl) {
        if (!tableEl) return;
        tableEl.querySelectorAll('tbody tr').forEach((tr, idx) => {
            const numCell = tr.querySelector('.row-num');
            if (numCell) numCell.textContent = idx + 1;
        });
    }

    document.body.addEventListener('click', function (e) {
        const removeRowBtn = e.target.closest('.btn-remove-row');
        if (removeRowBtn) {
            const tr = removeRowBtn.closest('tr');
            const table = tr ? tr.closest('table') : null;
            if (tr && table && table.querySelectorAll('tbody tr').length > 1) {
                tr.remove();
                renumberTable(table);
            }
        }

        const removeItemBtn = e.target.closest('.btn-remove-item');
        if (removeItemBtn) {
            const group = removeItemBtn.closest('.input-group');
            if (group) group.remove();
        }
    });

    // Calculator for Mandays
    const btnCalc = document.getElementById('btnHitungMandaysOtomatis');
    if (btnCalc) {
        btnCalc.addEventListener('click', function () {
            const mandays = parseFloat(document.getElementById('inpMandaysAwal').value) || 0;
            const faktor = parseFloat(document.getElementById('inpFaktorHari').value) || 0;
            const total = mandays + faktor;
            const tahap1 = Math.round(total * 0.1);
            const tahap2 = (total - tahap1).toFixed(1).replace('.', ',');
            const jmlAuditor = document.querySelectorAll('#tableTim2 tbody tr').length || 1;
            const hariPerAuditor = ((total - tahap1) / jmlAuditor).toFixed(1);

            document.getElementById('inpTotalMandays').value = total.toFixed(1);
            document.getElementById('inpAlokasiTahap1').value = tahap1;
            document.getElementById('inpAlokasiTahap2').value = `${tahap2} Hari ( ${hariPerAuditor} Hari, ${jmlAuditor} Auditor)`;
        });
    }

    // Add Tim 1
    let tim1Counter = <?php echo count($tim1) + 10; ?>;
    document.getElementById('btnAddTim1')?.addEventListener('click', function () {
        const tbody = document.querySelector('#tableTim1 tbody');
        const idx = tim1Counter++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center fw-bold row-num"></td>
            <td><input type="text" name="tim_audit_tahap_1[${idx}][nama]" class="form-control form-control-sm"></td>
            <td>
                <select name="tim_audit_tahap_1[${idx}][jabatan]" class="form-select form-select-sm">
                    <option value="Ketua Tim Audit">Ketua Tim Audit</option>
                    <option value="Auditor" selected>Auditor</option>
                </select>
            </td>
            <td><input type="text" name="tim_audit_tahap_1[${idx}][no_registrasi]" class="form-control form-control-sm font-monospace"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        renumberTable(document.getElementById('tableTim1'));
    });

    // Add Tim 2
    let tim2Counter = <?php echo count($tim2) + 10; ?>;
    function appendTim2Row(nama = '', jabatan = 'Auditor', reg = '') {
        const tbody = document.querySelector('#tableTim2 tbody');
        const idx = tim2Counter++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center fw-bold row-num"></td>
            <td><input type="text" name="tim_audit_tahap_2[${idx}][nama]" class="form-control form-control-sm" value="${nama}"></td>
            <td>
                <select name="tim_audit_tahap_2[${idx}][jabatan]" class="form-select form-select-sm">
                    <option value="Ketua Tim Audit" ${jabatan === 'Ketua Tim Audit' ? 'selected' : ''}>Ketua Tim Audit</option>
                    <option value="Auditor" ${jabatan !== 'Ketua Tim Audit' ? 'selected' : ''}>Auditor</option>
                </select>
            </td>
            <td><input type="text" name="tim_audit_tahap_2[${idx}][no_registrasi]" class="form-control form-control-sm font-monospace" value="${reg}"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        renumberTable(document.getElementById('tableTim2'));
    }

    document.getElementById('btnAddTim2')?.addEventListener('click', function () {
        appendTim2Row();
    });

    // Copy Tim 1 to Tim 2
    document.getElementById('btnCopyFromTim1')?.addEventListener('click', function () {
        const tbody2 = document.querySelector('#tableTim2 tbody');
        tbody2.innerHTML = '';
        document.querySelectorAll('#tableTim1 tbody tr').forEach(tr1 => {
            const inputs = tr1.querySelectorAll('input');
            const select = tr1.querySelector('select');
            const nama = inputs[0] ? inputs[0].value : '';
            const reg = inputs[1] ? inputs[1].value : '';
            const jab = select ? select.value : 'Auditor';
            appendTim2Row(nama, jab, reg);
        });
    });

    // Add Risk Present / Future
    let riskCounter = 100;
    function addRiskRow(tableId, fieldPrefix) {
        const table = document.getElementById(tableId);
        const tbody = table.querySelector('tbody');
        const idx = riskCounter++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center fw-bold row-num"></td>
            <td><textarea name="${fieldPrefix}[${idx}][kegiatan]" class="form-control form-control-sm" rows="2"></textarea></td>
            <td><input type="text" name="${fieldPrefix}[${idx}][risiko]" class="form-control form-control-sm" value="Kematian/Fatality/ Permanen disbility"></td>
            <td><input type="text" name="${fieldPrefix}[${idx}][nilai_awal]" class="form-control form-control-sm text-center" value="Tinggi"></td>
            <td><input type="text" name="${fieldPrefix}[${idx}][nilai_sisa]" class="form-control form-control-sm text-center" value="Rendah"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        renumberTable(table);
    }

    document.getElementById('btnAddRiskPresent')?.addEventListener('click', () => addRiskRow('tableRiskPresent', 'top_risks_present'));
    document.getElementById('btnAddRiskFuture')?.addEventListener('click', () => addRiskRow('tableRiskFuture', 'top_risks_future'));

    // Add Trend Rows
    document.querySelectorAll('.btn-add-trend').forEach(btn => {
        btn.addEventListener('click', function () {
            const key = this.getAttribute('data-trend-key');
            const table = document.getElementById('tableTrend_' + key);
            const tbody = table.querySelector('tbody');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center fw-bold row-num"></td>
                <td><input type="text" name="trend_data[${key}][]" class="form-control form-control-sm"></td>
                <td class="text-center"><button type="button" class="btn btn-sm text-danger btn-remove-row"><i class="bi bi-x-lg"></i></button></td>
            `;
            tbody.appendChild(tr);
            renumberTable(table);
        });
    });

    // Add Peralatan
    let alatCounter = 100;
    document.getElementById('btnAddPeralatan')?.addEventListener('click', function () {
        const table = document.getElementById('tablePeralatan');
        const idx = alatCounter++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center fw-bold row-num"></td>
            <td><input type="text" name="unjuk_kerja_peralatan[${idx}][jenis_peralatan]" class="form-control form-control-sm"></td>
            <td><input type="text" name="unjuk_kerja_peralatan[${idx}][pa]" class="form-control form-control-sm text-center" value="90"></td>
            <td><input type="text" name="unjuk_kerja_peralatan[${idx}][ma]" class="form-control form-control-sm text-center" value="87"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        table.querySelector('tbody').appendChild(tr);
        renumberTable(table);
    });

    // Add KLI
    let kliCounter = 100;
    document.getElementById('btnAddKli')?.addEventListener('click', function () {
        const table = document.getElementById('tableKli');
        const idx = kliCounter++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center fw-bold row-num"></td>
            <td><input type="text" name="capaian_kli[${idx}][indikator]" class="form-control form-control-sm"></td>
            <td><input type="text" name="capaian_kli[${idx}][capaian]" class="form-control form-control-sm text-center" value="100%"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        table.querySelector('tbody').appendChild(tr);
        renumberTable(table);
    });

    // Add Catatan Instansi
    document.getElementById('btnAddCatatanInstansi')?.addEventListener('click', function () {
        const wrap = document.getElementById('wrapCatatanInstansi');
        const div = document.createElement('div');
        div.className = 'input-group input-group-sm';
        div.innerHTML = `
            <input type="text" name="data_audit_sebelumnya[catatan_instansi][]" class="form-control">
            <button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-x-lg"></i></button>
        `;
        wrap.appendChild(div);
    });

    // Add Harapan
    document.getElementById('btnAddHarapan')?.addEventListener('click', function () {
        const table = document.getElementById('tableHarapan');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center fw-bold row-num"></td>
            <td><textarea name="harapan_pemangku[]" class="form-control form-control-sm" rows="2"></textarea></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        table.querySelector('tbody').appendChild(tr);
        renumberTable(table);
    });
});
</script>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();

