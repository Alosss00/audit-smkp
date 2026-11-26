<?php
$title = 'Penyusunan Dokumen Tahap Awal Audit (TT-MGT-FRS-026L) — SMKP Minerba';
ob_start();

$kinerja = $tahapAwal->kinerja_kp ?? [];
$faktorPenyesuaian = $tahapAwal->faktor_penyesuaian ?? [];
$tim1 = !empty($tahapAwal->tim_audit_tahap_1) ? $tahapAwal->tim_audit_tahap_1 : [['nama' => '', 'jabatan' => '', 'no_registrasi' => '']];
$ketuaTimTahap1 = null;
foreach ($tim1 as $anggota) {
    if ((($anggota['jabatan'] ?? '') === 'Ketua Tim Audit') && !empty($anggota['nama'])) {
        $ketuaTimTahap1 = trim($anggota['nama']);
        break;
    }
}
$valKetuaTim = $ketuaTimTahap1 ?: ($tahapAwal->ketua_tim_audit ?? '');
$kontak = $tahapAwal->kontak_awal ?? [];
$kelayakan = $tahapAwal->kelayakan_audit ?? [];
$kecukupan = $tahapAwal->kecukupan_dokumen ?? [];
$tim2 = !empty($tahapAwal->tim_audit_tahap_2) ? $tahapAwal->tim_audit_tahap_2 : [['nama' => '', 'jabatan' => '', 'no_registrasi' => '']];
$risksPresent = !empty($tahapAwal->top_risks_present) ? $tahapAwal->top_risks_present : [['kegiatan' => '', 'risiko' => '', 'nilai_awal' => '', 'nilai_sisa' => '']];
$risksFuture = !empty($tahapAwal->top_risks_future) ? $tahapAwal->top_risks_future : [['kegiatan' => '', 'risiko' => '', 'nilai_awal' => '', 'nilai_sisa' => '']];
$trends = $tahapAwal->trend_data ?? [];
$peralatan = !empty($tahapAwal->unjuk_kerja_peralatan) ? $tahapAwal->unjuk_kerja_peralatan : [['jenis_peralatan' => '', 'pa' => '', 'ma' => '']];
$klis = !empty($tahapAwal->capaian_kli) ? $tahapAwal->capaian_kli : [['indikator' => '', 'capaian' => '']];
$auditPrev = $tahapAwal->data_audit_sebelumnya ?? [];
$harapanList = !empty($tahapAwal->harapan_pemangku) ? $tahapAwal->harapan_pemangku : [''];
$thnPeriode = $sesi->tahun_periode ?? ($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y') : date('Y'));
$thnDepan = ((int) $thnPeriode) + 1;
$thnSebelumnya = ((int) $thnPeriode) - 1;

$isKelayakanLayak = ($tahapAwal->kesimpulan_kelayakan ?? '') === 'layak';
$isKecukupanLanjut = ($tahapAwal->kesimpulan_kecukupan ?? '') === 'dapat_dilanjutkan';
$isJadwalDapatDitentukan = $isKelayakanLayak && $isKecukupanLanjut;

$isKontraktor = $isKontraktor ?? ($sesi->perusahaan ? $sesi->perusahaan->isKontraktor() : false);
$pemegangIupList = $pemegangIupList ?? collect();

$hasPrevData = !empty($prevTahapAwalData) && !empty($prevTahunLabel);
$renderCopyPrevBtn = function (string $section, string $extraClass = '') use ($hasPrevData, $prevTahunLabel): string {
    if (!$hasPrevData) {
        return '';
    }
    $cls = trim('btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 btn-copy-prev ' . $extraClass);
    return '<button type="button" class="' . e($cls) . '" data-copy-section="' . e($section) . '" title="Salin data bagian ini dari periode Tahun ' . e($prevTahunLabel) . '"><i class="bi bi-copy me-1"></i> Salin ' . e($prevTahunLabel) . '</button>';
};
?>

<!-- Top Page Header -->
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
        <a href="<?php echo e(route('admin.audit-sesi.index')); ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Sesi Audit
        </a>
        <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
            <h2 class="fw-bold text-slate-800 mb-0">
                <i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Penyusunan Dokumen Tahap Awal Audit (Tahap I)
            </h2>
            <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo e($tahapAwal->no_dokumen_header ?: 'TT-MGT-FRS-026L'); ?></span>
            <?php if ($isKontraktor): ?>
                <span class="badge bg-info text-dark rounded-pill px-3 py-2">
                    <i class="bi bi-truck-front-fill me-1"></i> Form Khusus Kontraktor (IUJP)
                </span>
            <?php endif; ?>
        </div>
        <p class="text-muted small mb-0 mt-1">
            Perusahaan Auditi: <strong><?php echo e($tahapAwal->nama_perusahaan_auditi ?: $sesi->area_audit); ?></strong> &bull;
            Periode Audit: <strong class="text-primary">Tahun <?php echo e($thnPeriode); ?></strong>
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo e(route('admin.audit-sesi.tahap-awal.cetak', $sesi->id)); ?>" target="_blank" class="btn btn-dark rounded-3 px-3 fw-semibold shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> Lihat / Cetak PDF (TT-MGT-FRS-026L)
        </a>
        <a href="<?php echo e(route('admin.audit-sesi.matrix', $sesi->id)); ?>" class="btn btn-outline-primary rounded-3 px-3 fw-semibold">
            <i class="bi bi-grid-3x3-gap me-1"></i> Matriks Tahap II
        </a>
        <a href="<?php echo e(route('admin.audit-sesi.rekap', $sesi->id)); ?>" class="btn btn-outline-secondary rounded-3 px-3">
            <i class="bi bi-bar-chart-line me-1"></i> Rekap Sesi
        </a>
        <form action="<?php echo e(route('admin.audit-sesi.tahap-awal.reset', $sesi->id)); ?>" method="POST" class="d-inline" data-confirm="Reset seluruh data Tahap Awal Audit ke preset standar?">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <button type="submit" class="btn btn-outline-warning text-dark rounded-3 px-3" title="Reset ke Data Preset Standar">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Preset Standar
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

<form action="<?php echo e(route('admin.audit-sesi.tahap-awal.update', $sesi->id)); ?>" method="POST" id="formTahapAwal">
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
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <div>
                                <h5 class="fw-bold text-slate-800 mb-0">
                                    <i class="bi bi-building text-primary me-2"></i>I.A. Data Perusahaan Auditi
                                </h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary small mt-1">
                                    <i class="bi bi-link-45deg me-1"></i>Otomatis dari Master Perusahaan & Sesi Audit
                                </span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <?php echo $renderCopyPrevBtn('data_perusahaan'); ?>
                                <?php if ($sesi->perusahaan): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-xs" id="btnSyncPerusahaan" title="Sinkronkan ulang data dari Master Perusahaan & Sesi Audit">
                                        <i class="bi bi-arrow-repeat me-1"></i> Sinkron Data
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                            $comp = $sesi->perusahaan;
                            $thnSesi = $sesi->tahun_periode ?? ($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y') : date('Y'));
                            $periodeDefault = $thnSesi ? "1 Januari {$thnSesi} s/d 31 Desember {$thnSesi}" : '';

                            $valNamaPerusahaan = $tahapAwal->nama_perusahaan_auditi ?: ($comp->nama_perusahaan ?? $sesi->area_audit ?? '');
                            $valJenisPerizinan = trim(str_replace(['Jenis Perizinan', "\t"], '', (string) ($tahapAwal->jenis_perizinan ?: ($comp->jenis_perizinan ?? ''))));
                            $valJenisKomoditas = $tahapAwal->jenis_komoditas ?: ($comp->jenis_komoditas ?? '');
                            $valAlamatSite     = $tahapAwal->alamat_kantor_site ?: ($comp->alamat_kantor_site ?? '');
                            $valAlamatTambang  = $tahapAwal->alamat_lokasi_tambang ?: ($comp->alamat_lokasi_tambang ?? '');
                            $valAlamatPusat    = $tahapAwal->alamat_kantor_pusat ?: ($comp->alamat_kantor_pusat ?? '');
                            $valPeriodeAudit   = $tahapAwal->periode_audit_label ?: $periodeDefault;
                        ?>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Perusahaan Auditi</label>
                            <input type="text" name="nama_perusahaan_auditi" id="input_nama_perusahaan_auditi" class="form-control" value="<?php echo e($valNamaPerusahaan); ?>">
                        </div>
                        <?php if ($isKontraktor): ?>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Area Pemegang IUP / Perusahaan Induk (Klien Audit)</label>
                                <input type="text" name="perusahaan_induk" id="input_perusahaan_induk" class="form-control" list="listPemegangIup"
                                       placeholder="Contoh: PT Meares Soputan Mining"
                                       value="<?php echo e($tahapAwal->perusahaan_induk ?? ''); ?>">
                                <datalist id="listPemegangIup">
                                    <?php foreach ($pemegangIupList as $iup): ?>
                                        <option value="<?php echo e($iup->nama_perusahaan); ?>">
                                    <?php endforeach; ?>
                                </datalist>
                                <small class="text-muted">Digunakan pada narasi Surat Pengangkatan Tim Audit KTT &amp; Rencana Audit.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Jenis Perizinan (NIB / IUJP)</label>
                                <textarea name="jenis_perizinan" id="input_jenis_perizinan" class="form-control" rows="2" placeholder="Contoh: NIB 9120204321416"><?php echo e($valJenisPerizinan); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Jenis Komoditas / Bidang Usaha Jasa</label>
                                <textarea name="jenis_komoditas" id="input_jenis_komoditas" class="form-control" rows="2" placeholder="Contoh: Aktivitas Penunjang Pertambangan..., Jasa Pengujian Laboratorium"><?php echo e($valJenisKomoditas); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Alamat Perusahaan</label>
                                <textarea name="alamat_kantor_site" id="input_alamat_kantor_site" class="form-control" rows="4" placeholder="Contoh: C/O PT Meares Soputan Mining Toka Tindung Project Via Desa Winuri..."><?php echo e($valAlamatSite); ?></textarea>
                            </div>
                            <div>
                                <label class="form-label small fw-bold">Periode Audit</label>
                                <input type="text" name="periode_audit_label" id="input_periode_audit_label" class="form-control" placeholder="Contoh: 10 Oktober 2024 – 9 Oktober 2025" value="<?php echo e($valPeriodeAudit); ?>">
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-info-circle me-1"></i>Periode audit kontraktor dapat disesuaikan dengan kontrak / siklus audit (Tahun <?php echo e($thnSesi); ?>).
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label small fw-bold">Jenis Perizinan (Pisahkan baris jika > 1)</label>
                                    <textarea name="jenis_perizinan" id="input_jenis_perizinan" class="form-control" rows="2" placeholder="Contoh: IUP Operasi Produksi No. ... (Tekan Enter untuk baris baru)"><?php echo e($valJenisPerizinan); ?></textarea>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold">Jenis Komoditas</label>
                                    <input type="text" name="jenis_komoditas" id="input_jenis_komoditas" class="form-control" value="<?php echo e($valJenisKomoditas); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Alamat Kantor Site</label>
                                <textarea name="alamat_kantor_site" id="input_alamat_kantor_site" class="form-control" rows="2"><?php echo e($valAlamatSite); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Alamat Lokasi Tambang</label>
                                <textarea name="alamat_lokasi_tambang" id="input_alamat_lokasi_tambang" class="form-control" rows="2"><?php echo e($valAlamatTambang); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Alamat Kantor Pusat</label>
                                <textarea name="alamat_kantor_pusat" id="input_alamat_kantor_pusat" class="form-control" rows="3"><?php echo e($valAlamatPusat); ?></textarea>
                            </div>
                            <div>
                                <label class="form-label small fw-bold">Periode Audit</label>
                                <input type="text" name="periode_audit_label" id="input_periode_audit_label" class="form-control" value="<?php echo e($valPeriodeAudit); ?>">
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-info-circle me-1"></i>Diambil dari tahun periode audit yang dipilih saat sesi dibuat (<?php echo e($thnSesi); ?>).
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- A.2 Data Kinerja Keselamatan Pertambangan -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 border-bottom pb-2">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-activity text-danger me-2"></i>Data Kinerja Keselamatan Pertambangan
                            </h5>
                            <?php echo $renderCopyPrevBtn('kinerja_kp'); ?>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Kecelakaan Cidera Ringan</label>
                                <input type="text" name="kinerja_kp[cidera_ringan]" class="form-control" value="<?php echo e($kinerja['cidera_ringan'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Kecelakaan Cidera Berat</label>
                                <input type="text" name="kinerja_kp[cidera_berat]" class="form-control" value="<?php echo e($kinerja['cidera_berat'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Kecelakaan Berakibat Mati</label>
                                <input type="text" name="kinerja_kp[mati]" class="form-control" value="<?php echo e($kinerja['mati'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold"><em>Frequency Rate</em> (FR) Kecelakaan Tambang</label>
                                <input type="text" name="kinerja_kp[fr]" class="form-control" value="<?php echo e($kinerja['fr'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold"><em>Severity Rate</em> (SR) Kecelakaan Tambang</label>
                                <input type="text" name="kinerja_kp[sr]" class="form-control" value="<?php echo e($kinerja['sr'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Jumlah Kejadian Berbahaya</label>
                                <input type="text" name="kinerja_kp[kejadian_berbahaya]" class="form-control" value="<?php echo e($kinerja['kejadian_berbahaya'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold"><em>Absence Severity Rate</em> (ASR)</label>
                                <input type="text" name="kinerja_kp[asr]" class="form-control" value="<?php echo e($kinerja['asr'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold"><em>Morbidity Frequency Rate</em> (MFR)</label>
                                <input type="text" name="kinerja_kp[mfr]" class="form-control" value="<?php echo e($kinerja['mfr'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Kejadian Akibat Penyakit Tenaga Kerja</label>
                                <input type="text" name="kinerja_kp[penyakit_tk]" class="form-control" value="<?php echo e($kinerja['penyakit_tk'] ?? ''); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold">Frekuensi Penyakit Akibat Kerja (PAK)</label>
                                <input type="text" name="kinerja_kp[frekuensi_pak]" class="form-control" value="<?php echo e($kinerja['frekuensi_pak'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- A.3 Dasar Faktor Penyesuaian -->
                <div class="col-12">
                    <div class="card card-custom p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-sliders text-info me-2"></i>Dasar Faktor Penyesuaian (7 Kondisi Kepdirjen 185)
                            </h5>
                            <?php echo $renderCopyPrevBtn('faktor_penyesuaian'); ?>
                        </div>
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
                                    <?php foreach($kondisiPenyesuaian as $no => $kondisiText): ?>
                                        <?php $val = isset($faktorPenyesuaian[$no]) ? strtolower($faktorPenyesuaian[$no]) : ''; ?>
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
                                                       <?php echo e($val === 'tidak' ? 'checked' : ''); ?>>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- B. Perhitungan Hari Kerja Audit dan Jumlah Auditor -->
                <?php
                    $masterFaktorPenyesuaian = $masterFaktorPenyesuaian ?? collect();
                    $saranKelasRisiko = $saranKelasRisiko ?? ($sesi->perusahaan ? $sesi->perusahaan->kelasRisikoDefault() : null);
                    $savedMasterFaktorIds = [];
                    if (!empty($faktorPenyesuaian['master_faktor_terpilih']) && is_array($faktorPenyesuaian['master_faktor_terpilih'])) {
                        $savedMasterFaktorIds = array_map(fn ($f) => (int) ($f['id'] ?? 0), $faktorPenyesuaian['master_faktor_terpilih']);
                    }
                    $currentSumberPerhitungan = $tahapAwal->sumber_perhitungan ?? '';
                ?>
                <div class="col-12">
                    <div class="card card-custom p-4 border-start border-4 border-primary">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <h5 class="fw-bold text-slate-800 mb-0">
                                    <i class="bi bi-calculator text-primary me-2"></i>I.B. Perhitungan Hari Kerja Audit (Mandays) dan Jumlah Auditor
                                </h5>
                                <div class="mt-1 d-flex align-items-center gap-2 flex-wrap">
                                    <span id="badgeSumberPerhitungan" class="badge rounded-pill px-2.5 py-1 <?php echo $currentSumberPerhitungan === 'tabel' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary border'; ?>">
                                        <i class="bi <?php echo $currentSumberPerhitungan === 'tabel' ? 'bi-table' : 'bi-pencil'; ?> me-1"></i>
                                        Sumber: <strong id="lblSumberPerhitungan"><?php echo $currentSumberPerhitungan === 'tabel' ? 'Tabel Acuan' : ($currentSumberPerhitungan === 'manual' ? 'Manual' : 'Belum dihitung'); ?></strong>
                                    </span>
                                    <?php if (!empty($saranKelasRisiko)): ?>
                                        <span class="badge bg-info bg-opacity-10 text-dark border border-info border-opacity-25 rounded-pill px-2.5 py-1">
                                            <i class="bi bi-lightbulb text-info me-1"></i>Saran Kelas Risiko (Jenis Usaha): <strong><?php echo e($saranKelasRisiko); ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <?php echo $renderCopyPrevBtn('mandays'); ?>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" id="btnHitungDariTabelAcuan"
                                        data-endpoint="<?php echo e(route('admin.audit-sesi.tahap-awal.hitung-hari', $sesi->id)); ?>">
                                    <i class="bi bi-table me-1"></i> Hitung dari Tabel Acuan
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnHitungMandaysOtomatis">
                                    <i class="bi bi-magic me-1"></i> Hitung Otomatis Total & Alokasi
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="sumber_perhitungan" id="inpSumberPerhitungan" value="<?php echo e($currentSumberPerhitungan); ?>">
                        <div id="alertHitungAcuan" class="alert d-none py-2 px-3 small mb-3 rounded-3" role="alert"></div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Jumlah Pekerja Auditi</label>
                                <input type="number" name="jumlah_pekerja" id="inpJumlahPekerja" class="form-control" value="<?php echo e($tahapAwal->jumlah_pekerja ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Kelas Risiko</label>
                                <?php $selectedKelasRisiko = $tahapAwal->kelas_risiko ?: ($saranKelasRisiko ?? ''); ?>
                                <select name="kelas_risiko" id="inpKelasRisiko" class="form-select">
                                    <option value="" <?php echo e(empty($selectedKelasRisiko) ? 'selected' : ''); ?>>-- Pilih Kelas Risiko --</option>
                                    <?php foreach(['Tinggi', 'Menengah', 'Sedang', 'Rendah'] as $kr): ?>
                                        <option value="<?php echo e($kr); ?>" <?php echo e($selectedKelasRisiko === $kr ? 'selected' : ''); ?>><?php echo e($kr); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Mandays (Hari)</label>
                                <input type="number" step="0.1" name="mandays_awal" id="inpMandaysAwal" class="form-control mandays-manual-watch" value="<?php echo e($tahapAwal->mandays_awal ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Faktor Penyesuaian (Hari)</label>
                                <input type="number" step="0.1" name="faktor_penyesuaian_hari" id="inpFaktorHari" class="form-control mandays-manual-watch" value="<?php echo e($tahapAwal->faktor_penyesuaian_hari ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Total Mandays (Hari)</label>
                                <input type="number" step="0.1" name="total_mandays" id="inpTotalMandays" class="form-control bg-light fw-bold mandays-manual-watch" value="<?php echo e($tahapAwal->total_mandays ?? ''); ?>">
                            </div>

                            <?php if ($masterFaktorPenyesuaian->isNotEmpty()): ?>
                                <div class="col-12">
                                    <div class="p-3 rounded-3 bg-light border">
                                        <div class="small fw-bold text-slate-800 mb-2">
                                            <i class="bi bi-check2-square text-primary me-1"></i>Faktor Penyesuaian (Master Acuan) — Pilih faktor yang berlaku sebelum klik "Hitung dari Tabel Acuan":
                                        </div>
                                        <div class="row g-2">
                                            <?php foreach ($masterFaktorPenyesuaian as $mf): ?>
                                                <?php $isChecked = in_array((int) $mf->id, $savedMasterFaktorIds, true); ?>
                                                <div class="col-md-6">
                                                    <div class="form-check small">
                                                        <input class="form-check-input chk-master-faktor" type="checkbox"
                                                               name="master_faktor_ids[]"
                                                               value="<?php echo e($mf->id); ?>"
                                                               id="mf_<?php echo e($mf->id); ?>"
                                                               <?php echo $isChecked ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="mf_<?php echo e($mf->id); ?>">
                                                            <span class="badge <?php echo $mf->tipe === 'tambah' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success'; ?> me-1">
                                                                <?php echo $mf->tipe === 'tambah' ? '+' : '−'; ?><?php echo $mf->nilai_hari !== null ? e(number_format((float) $mf->nilai_hari, 1)) . ' hr' : 'belum diisi'; ?>
                                                            </span>
                                                            <?php echo e($mf->nama); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Alokasi Mandays Tahap I (Maks. 10% Total)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" name="alokasi_tahap_1" id="inpAlokasiTahap1" class="form-control" value="<?php echo e($tahapAwal->alokasi_tahap_1 ?? ''); ?>">
                                    <span class="input-group-text">Hari</span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Alokasi Mandays Tahap II (Min. 90% Total & Rincian Auditor)</label>
                                <input type="text" name="alokasi_tahap_2" id="inpAlokasiTahap2" class="form-control" value="<?php echo e($tahapAwal->alokasi_tahap_2 ?? ''); ?>">
                                <div class="mt-2 text-muted small d-flex align-items-center gap-1" id="hintAlokasiDurasi">
                                    <i class="bi bi-calendar-event text-primary"></i>
                                    <span>Total Durasi Audit: <strong id="lblDurasiHari" class="text-primary">-</strong> (dibulatkan ke atas &bull; otomatis menyinkronkan Jadwal Pelaksanaan di bawah).</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- C. Jadwal Pelaksanaan Audit (Verifikasi Lapangan / Tahap II) -->
                <div class="col-12" id="row_jadwal_pelaksanaan">
                    <div id="containerJadwalAudit" class="card card-custom p-4 border-start border-4 border-primary">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-box bg-primary text-white d-flex align-items-center justify-content-center rounded-3" id="jadwalIconBox" style="width: 44px; height: 44px;">
                                    <i class="bi bi-calendar-check-fill fs-5" id="jadwalIcon"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-slate-800">
                                        <i class="bi bi-calendar-range text-primary me-2"></i>I.C. Jadwal Pelaksanaan Audit (Verifikasi Lapangan / Tahap II)
                                    </h5>
                                    <small class="text-muted" id="jadwalSubtitle">
                                        Disinkronkan otomatis berdasarkan Alokasi Mandays Tahap II (Form I.B di atas).
                                    </small>
                                </div>
                            </div>
                            <div id="jadwalStatusBadge" class="d-flex flex-wrap gap-2 align-items-center">
                                <?php echo $renderCopyPrevBtn('jadwal_pelaksanaan'); ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold" id="badgeDurasiAudit">
                                    <i class="bi bi-clock-history me-1"></i> <span id="textBadgeDurasi">Tersinkronisasi Mandays</span>
                                </span>
                            </div>
                        </div>

                        <!-- Alert Penjelasan Durasi & Sinkronisasi -->
                        <div id="jadwalAlertNotice" class="alert alert-info bg-white border border-info border-opacity-25 py-2 px-3 mb-3 d-flex align-items-center justify-content-between gap-2 small">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill text-info fs-6" id="jadwalAlertIcon"></i>
                                <span id="jadwalAlertText">
                                    Total durasi audit lapangan dihitung otomatis berdasarkan pembulatan ke atas (<em>round up / ceil</em>) dari rata-rata hari per auditor pada Alokasi Mandays Tahap II. Tanggal selesai akan otomatis terkalkulasi saat tanggal mulai ditentukan.
                                </span>
                            </div>
                        </div>

                        <!-- Input Tanggal Mulai & Tanggal Selesai -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="input_tanggal_mulai" class="form-label small fw-bold text-slate-800">
                                    Tanggal Mulai Pelaksanaan Audit (Tahap II) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-slate-300"><i class="bi bi-calendar3 text-primary"></i></span>
                                    <input type="date" name="tanggal_mulai" id="input_tanggal_mulai" 
                                           class="form-control" 
                                           value="<?php echo e($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y-m-d') : ''); ?>" required>
                                </div>
                                <small class="text-muted">Awal pelaksanaan verifikasi lapangan / opening meeting.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="input_tanggal_selesai" class="form-label small fw-bold text-slate-800">
                                    Tanggal Selesai Pelaksanaan Audit (Tahap II) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-slate-300"><i class="bi bi-calendar3-check text-primary"></i></span>
                                    <input type="date" name="tanggal_selesai" id="input_tanggal_selesai" 
                                           class="form-control bg-light" 
                                           value="<?php echo e($sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('Y-m-d') : ''); ?>" required>
                                </div>
                                <small class="text-muted" id="infoDurasiAuditText">Akhir pelaksanaan audit lapangan / closing meeting (otomatis terhitung dari durasi).</small>
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
                            <div class="<?php echo $isKontraktor ? 'col-md-4' : 'col-md-6'; ?>">
                                <label class="form-label small fw-bold">Nomor Surat Pengangkatan Tim Audit (SK KTT)</label>
                                <input type="text" name="no_surat_tugas" class="form-control" value="<?php echo e($tahapAwal->no_surat_tugas ?? ''); ?>">
                            </div>
                            <div class="<?php echo $isKontraktor ? 'col-md-4' : 'col-md-6'; ?>">
                                <label class="form-label small fw-bold">Tanggal Surat Pengangkatan<?php echo $isKontraktor ? ' (Tahap I)' : ''; ?></label>
                                <input type="date" name="tanggal_surat_tugas" class="form-control" value="<?php echo e($tahapAwal->tanggal_surat_tugas ? $tahapAwal->tanggal_surat_tugas->format('Y-m-d') : ''); ?>">
                            </div>
                            <?php if ($isKontraktor): ?>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Tanggal Surat Pengangkatan (Tahap II)</label>
                                    <input type="date" name="tanggal_surat_tugas_tahap_2" class="form-control" value="<?php echo e($tahapAwal->tanggal_surat_tugas_tahap_2 ? $tahapAwal->tanggal_surat_tugas_tahap_2->format('Y-m-d') : ''); ?>">
                                    <small class="text-muted">Opsional jika berbeda dengan Tahap I.</small>
                                </div>
                            <?php endif; ?>
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
                                    <?php foreach($tim1 as $idx => $anggota): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><input type="text" name="tim_audit_tahap_1[<?php echo e($idx); ?>][nama]" class="form-control form-control-sm tim1-nama" value="<?php echo e($anggota['nama'] ?? ''); ?>"></td>
                                            <td>
                                                <select name="tim_audit_tahap_1[<?php echo e($idx); ?>][jabatan]" class="form-select form-select-sm tim1-jabatan">
                                                    <option value="" <?php echo e(empty($anggota['jabatan']) ? 'selected' : ''); ?>>-- Pilih Jabatan --</option>
                                                    <option value="Ketua Tim Audit" <?php echo e(($anggota['jabatan'] ?? '') === 'Ketua Tim Audit' ? 'selected' : ''); ?>>Ketua Tim Audit</option>
                                                    <option value="Auditor" <?php echo e(($anggota['jabatan'] ?? '') === 'Auditor' ? 'selected' : ''); ?>>Auditor</option>
                                                    <option value="Observer" <?php echo e(($anggota['jabatan'] ?? '') === 'Observer' ? 'selected' : ''); ?>>Observer</option>
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
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-calendar2-event text-info me-2"></i>II.B. Pelaksanaan Kontak Awal dengan Auditi
                            </h5>
                            <?php echo $renderCopyPrevBtn('kontak_awal'); ?>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Hari / Tanggal Kontak Awal & Evaluasi Kelayakan</label>
                                <input type="text" name="kontak_awal[hari_tanggal]" class="form-control" value="<?php echo e($kontak['hari_tanggal'] ?? ''); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Media</label>
                                <input type="text" name="kontak_awal[media]" class="form-control" value="<?php echo e($kontak['media'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Perwakilan Auditi<?php echo $isKontraktor ? ' (PJO)' : ''; ?></label>
                                <input type="text" name="kontak_awal[perwakilan_auditi]" class="form-control"
                                       placeholder="<?php echo $isKontraktor ? 'Contoh: Mus Hermawan' : ''; ?>"
                                       value="<?php echo e($kontak['perwakilan_auditi'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Jabatan Perwakilan Auditi</label>
                                <input type="text" name="kontak_awal[jabatan_perwakilan]" class="form-control"
                                       list="listJabatanPerwakilan"
                                       placeholder="<?php echo $isKontraktor ? 'Contoh: PJO' : ''; ?>"
                                       value="<?php echo e($kontak['jabatan_perwakilan'] ?? ''); ?>">
                                <datalist id="listJabatanPerwakilan">
                                    <option value="PJO">
                                    <option value="Penanggung Jawab Operasional">
                                    <option value="Kepala Teknik Tambang">
                                </datalist>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- II.C Penentuan Kelayakan Audit -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-clipboard2-check text-success me-2"></i>II.C. Penentuan Kelayakan Audit (7 Indikator)
                            </h5>
                            <?php echo $renderCopyPrevBtn('kelayakan_audit'); ?>
                        </div>
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
                                    <?php foreach($indikatorKelayakan as $no => $indText): ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?php echo e($no); ?></td>
                                            <td class="small"><?php echo e($indText); ?></td>
                                            <td>
                                                <input type="text" name="kelayakan_audit[<?php echo e($no); ?>]" class="form-control form-control-sm text-center fw-semibold"
                                                       value="<?php echo e($kelayakan[$no] ?? ''); ?>" list="listOpsiKelayakan">
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
                            <select name="kesimpulan_kelayakan" id="selectKesimpulanKelayakan" class="form-select form-select-sm w-auto fw-bold">
                                <option value="" <?php echo e(empty($tahapAwal->kesimpulan_kelayakan) ? 'selected' : ''); ?>>-- Pilih Kesimpulan --</option>
                                <option value="layak" <?php echo e(($tahapAwal->kesimpulan_kelayakan ?? '') === 'layak' ? 'selected' : ''); ?>>Layak untuk dilaksanakan</option>
                                <option value="tidak_layak" <?php echo e(($tahapAwal->kesimpulan_kelayakan ?? '') === 'tidak_layak' ? 'selected' : ''); ?>>Tidak Layak untuk dilaksanakan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- II.D Penentuan Kecukupan Dokumentasi -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-folder-check text-primary me-2"></i>II.D. Penentuan Kecukupan Dokumentasi (7 Elemen SMKP)
                            </h5>
                            <?php echo $renderCopyPrevBtn('kecukupan_dokumen'); ?>
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th class="text-start" style="width: 32%;">Peninjauan Dokumentasi</th>
                                        <th class="text-start">Hasil Evaluasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($elemenDokumentasi as $no => $elText): ?>
                                        <?php
                                            $val = $kecukupan[$no] ?? '';
                                            $isPredefined = in_array($val, ['Baik', 'Cukup', 'Kurang'], true);
                                            $isCustom = !empty($val) && !$isPredefined;
                                        ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?php echo e($no); ?></td>
                                            <td class="small fw-semibold"><?php echo e($elText); ?></td>
                                            <td>
                                                <div class="kecukupan-row-container" data-no="<?php echo e($no); ?>">
                                                    <select class="form-select form-select-sm fw-semibold sel-kecukupan" id="sel_kecukupan_<?php echo e($no); ?>">
                                                        <option value="" <?php echo e(empty($val) ? 'selected' : ''); ?>>-- Pilih Evaluasi --</option>
                                                        <option value="Baik" <?php echo e($val === 'Baik' ? 'selected' : ''); ?>>Baik</option>
                                                        <option value="Cukup" <?php echo e($val === 'Cukup' ? 'selected' : ''); ?>>Cukup</option>
                                                        <option value="Kurang" <?php echo e($val === 'Kurang' ? 'selected' : ''); ?>>Kurang</option>
                                                        <option value="custom" <?php echo e($isCustom ? 'selected' : ''); ?>>Input Deskripsi...</option>
                                                    </select>
                                                    <div class="desc-kecukupan-wrapper mt-2 <?php echo e($isCustom ? '' : 'd-none'); ?>" id="wrapper_desc_<?php echo e($no); ?>">
                                                        <textarea class="form-control form-control-sm txt-kecukupan-desc" id="txt_desc_<?php echo e($no); ?>" rows="3" placeholder="Ketik narasi atau deskripsi hasil evaluasi..."><?php echo e($isCustom ? $val : ''); ?></textarea>
                                                    </div>
                                                    <input type="hidden" name="kecukupan_dokumen[<?php echo e($no); ?>]" id="inp_kecukupan_<?php echo e($no); ?>" value="<?php echo e($val); ?>">
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                            <span class="small fw-bold">Kesimpulan Evaluasi Dokumentasi:</span>
                            <select name="kesimpulan_kecukupan" id="selectKesimpulanKecukupan" class="form-select form-select-sm w-auto fw-bold">
                                <option value="" <?php echo e(empty($tahapAwal->kesimpulan_kecukupan) ? 'selected' : ''); ?>>-- Pilih Kesimpulan --</option>
                                <option value="dapat_dilanjutkan" <?php echo e(($tahapAwal->kesimpulan_kecukupan ?? '') === 'dapat_dilanjutkan' ? 'selected' : ''); ?>>Dapat dilanjutkan*) ke tahap berikutnya</option>
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
                                    <?php foreach($tim2 as $idx => $anggota): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><input type="text" name="tim_audit_tahap_2[<?php echo e($idx); ?>][nama]" class="form-control form-control-sm" value="<?php echo e($anggota['nama'] ?? ''); ?>"></td>
                                            <td>
                                                <select name="tim_audit_tahap_2[<?php echo e($idx); ?>][jabatan]" class="form-select form-select-sm">
                                                    <option value="" <?php echo e(empty($anggota['jabatan']) ? 'selected' : ''); ?>>-- Pilih Jabatan --</option>
                                                    <option value="Ketua Tim Audit" <?php echo e(($anggota['jabatan'] ?? '') === 'Ketua Tim Audit' ? 'selected' : ''); ?>>Ketua Tim Audit</option>
                                                    <option value="Auditor" <?php echo e(($anggota['jabatan'] ?? '') === 'Auditor' ? 'selected' : ''); ?>>Auditor</option>
                                                    <option value="Observer" <?php echo e(($anggota['jabatan'] ?? '') === 'Observer' ? 'selected' : ''); ?>>Observer</option>
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
                                    <i class="bi bi-fire text-danger me-2"></i>Data Top Risks Aspek KP: Periode Audit <?php echo e($thnPeriode); ?> (<em>Risk of Present</em>)
                                </h5>
                                <small class="text-muted">Critical point untuk penetapan metode dan sampel pembuktian primer pada periode berjalan.</small>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <?php echo $renderCopyPrevBtn('top_risks_present'); ?>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-3" id="btnAddRiskPresent">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Baris Risiko
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tableRiskPresent">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Kegiatan</th>
                                        <th style="width: 260px;">Risiko</th>
                                        <?php if ($isKontraktor): ?>
                                            <th style="width: 160px;">Nilai Risiko</th>
                                        <?php else: ?>
                                            <th style="width: 140px;">Nilai Risiko Awal</th>
                                            <th style="width: 140px;">Nilai Risiko Sisa</th>
                                        <?php endif; ?>
                                        <th style="width: 55px;">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($risksPresent as $idx => $rp): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><textarea name="top_risks_present[<?php echo e($idx); ?>][kegiatan]" class="form-control form-control-sm" rows="2"><?php echo e($rp['kegiatan'] ?? ''); ?></textarea></td>
                                            <td><input type="text" name="top_risks_present[<?php echo e($idx); ?>][risiko]" class="form-control form-control-sm" value="<?php echo e($rp['risiko'] ?? ''); ?>"></td>
                                            <td><input type="text" name="top_risks_present[<?php echo e($idx); ?>][nilai_awal]" class="form-control form-control-sm text-center" value="<?php echo e($rp['nilai_awal'] ?? ''); ?>"></td>
                                            <?php if (!$isKontraktor): ?>
                                                <td><input type="text" name="top_risks_present[<?php echo e($idx); ?>][nilai_sisa]" class="form-control form-control-sm text-center" value="<?php echo e($rp['nilai_sisa'] ?? ''); ?>"></td>
                                            <?php endif; ?>
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
                                    <i class="bi bi-compass text-warning me-2"></i>Data Top Risks Aspek KP: Rencana Kegiatan <?php echo e($thnDepan); ?> (<em>Risk of Future</em>)
                                </h5>
                                <small class="text-muted">Penilaian risiko untuk rencana kegiatan operasional tahun berikutnya.</small>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <?php echo $renderCopyPrevBtn('top_risks_future'); ?>
                                <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-3" id="btnAddRiskFuture">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Baris Risiko
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tableRiskFuture">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Kegiatan</th>
                                        <th style="width: 260px;">Risiko</th>
                                        <?php if ($isKontraktor): ?>
                                            <th style="width: 160px;">Nilai Risiko</th>
                                        <?php else: ?>
                                            <th style="width: 140px;">Nilai Risiko Awal</th>
                                            <th style="width: 140px;">Nilai Risiko Sisa</th>
                                        <?php endif; ?>
                                        <th style="width: 55px;">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($risksFuture as $idx => $rf): ?>
                                        <tr>
                                            <td class="text-center fw-bold row-num"><?php echo e($idx + 1); ?></td>
                                            <td><textarea name="top_risks_future[<?php echo e($idx); ?>][kegiatan]" class="form-control form-control-sm" rows="2"><?php echo e($rf['kegiatan'] ?? ''); ?></textarea></td>
                                            <td><input type="text" name="top_risks_future[<?php echo e($idx); ?>][risiko]" class="form-control form-control-sm" value="<?php echo e($rf['risiko'] ?? ''); ?>"></td>
                                            <td><input type="text" name="top_risks_future[<?php echo e($idx); ?>][nilai_awal]" class="form-control form-control-sm text-center" value="<?php echo e($rf['nilai_awal'] ?? ''); ?>"></td>
                                            <?php if (!$isKontraktor): ?>
                                                <td><input type="text" name="top_risks_future[<?php echo e($idx); ?>][nilai_sisa]" class="form-control form-control-sm text-center" value="<?php echo e($rf['nilai_sisa'] ?? ''); ?>"></td>
                                            <?php endif; ?>
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
                <?php foreach($trendSections as $tKey => $tMeta): ?>
                    <?php $tItems = !empty($trends[$tKey]) ? $trends[$tKey] : ['']; ?>
                    <div class="col-md-6">
                        <div class="card card-custom p-4 h-100">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                <h6 class="fw-bold text-slate-800 mb-0"><?php echo e($tMeta['title']); ?></h6>
                                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                    <?php echo $renderCopyPrevBtn('trend_' . $tKey, 'py-0 px-2'); ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-add-trend" data-trend-key="<?php echo e($tKey); ?>">
                                        <i class="bi bi-plus"></i> Tambah
                                    </button>
                                </div>
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
                                    <?php foreach($tItems as $idx => $itemVal): ?>
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
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-truck-flatbed text-primary me-2"></i>Data Unjuk Kerja Peralatan Pertambangan (PA & MA)
                            </h5>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <?php echo $renderCopyPrevBtn('unjuk_kerja_peralatan'); ?>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-3" id="btnAddPeralatan">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Peralatan
                                </button>
                            </div>
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
                                    <?php foreach($peralatan as $idx => $alat): ?>
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
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-speedometer2 text-success me-2"></i>Capaian <em>Key Leading Indicator</em>
                            </h5>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <?php echo $renderCopyPrevBtn('capaian_kli'); ?>
                                <button type="button" class="btn btn-sm btn-outline-success rounded-3" id="btnAddKli">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah KLI
                                </button>
                            </div>
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
                                    <?php foreach($klis as $idx => $kli): ?>
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
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-clock-history text-primary me-2"></i>Data Audit Internal SMKP Tahun Sebelumnya
                            </h5>
                            <?php echo $renderCopyPrevBtn('data_audit_sebelumnya'); ?>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Tahun Audit Sebelumnya</label>
                                <input type="text" name="data_audit_sebelumnya[tahun]" class="form-control" value="<?php echo e($auditPrev['tahun'] ?? ''); ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">1. Capaian Nilai Audit</label>
                                <input type="text" name="data_audit_sebelumnya[nilai_audit]" class="form-control fw-bold" value="<?php echo e($auditPrev['nilai_audit'] ?? ''); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">2. Status Penyelesaian Tindak Lanjut Ketidaksesuaian Kategori Kritikal</label>
                                <input type="text" name="data_audit_sebelumnya[status_kritikal]" class="form-control" value="<?php echo e($auditPrev['status_kritikal'] ?? ''); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">3. Status Penyelesaian Tindak Lanjut Ketidaksesuaian Kategori Mayor</label>
                                <input type="text" name="data_audit_sebelumnya[status_mayor]" class="form-control" value="<?php echo e($auditPrev['status_mayor'] ?? ''); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">4. Status Penyelesaian Tindak Lanjut Ketidaksesuaian Kategori Minor</label>
                                <input type="text" name="data_audit_sebelumnya[status_minor]" class="form-control" value="<?php echo e($auditPrev['status_minor'] ?? ''); ?>">
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small fw-bold mb-0">5. Catatan Hasil Evaluasi dari Instansi Pembina Sektor</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" id="btnAddCatatanInstansi">
                                        <i class="bi bi-plus"></i> Tambah Poin
                                    </button>
                                </div>
                                <div id="wrapCatatanInstansi" class="d-flex flex-column gap-2">
                                    <?php foreach((!empty($auditPrev['catatan_instansi']) ? $auditPrev['catatan_instansi'] : ['']) as $cInst): ?>
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
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-chat-quote text-info me-2"></i>Daftar Harapan & Kebutuhan Pemangku Kepentingan
                            </h5>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <?php echo $renderCopyPrevBtn('harapan_pemangku'); ?>
                                <button type="button" class="btn btn-sm btn-outline-info text-dark rounded-3" id="btnAddHarapan">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Masukan
                                </button>
                            </div>
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
                                    <?php foreach($harapanList as $idx => $hrp): ?>
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
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 border-bottom pb-2">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-award text-primary me-2"></i>Pengesahan Berita Acara (Penutup)
                            </h5>
                            <?php echo $renderCopyPrevBtn('pengesahan'); ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tanggal Pembuatan Berita Acara Tahap Awal</label>
                            <input type="date" name="tanggal_berita_acara" class="form-control" value="<?php echo e($tahapAwal->tanggal_berita_acara ? $tahapAwal->tanggal_berita_acara->format('Y-m-d') : ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Ketua Tim Audit (Penandatangan 1)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person-badge text-primary"></i></span>
                                <input type="text" name="ketua_tim_audit" id="inpKetuaTimAudit" class="form-control" value="<?php echo e($valKetuaTim); ?>">
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-link-45deg me-1"></i>Otomatis terhubung dari Auditor dengan jabatan <strong>Ketua Tim Audit</strong> di bagian II.A.
                            </small>
                        </div>
                        <?php if ($isKontraktor): ?>
                            <?php
                                $valNamaPjo = $tahapAwal->nama_pjo ?: ($sesi->perusahaan->nama_ktt_default ?? '');
                                $valNamaKtt = $tahapAwal->nama_ktt ?? '';
                                $kttOptions = [];
                                foreach ($pemegangIupList as $iup) {
                                    $kttName = trim((string) ($iup->nama_ktt_default ?? ''));
                                    if ($kttName !== '') {
                                        $kttOptions[] = [
                                            'nama_ktt' => $kttName,
                                            'nama_perusahaan' => $iup->nama_perusahaan,
                                        ];
                                    }
                                }
                                $kttValues = array_column($kttOptions, 'nama_ktt');
                                $hasCustomKtt = $valNamaKtt !== '' && !in_array($valNamaKtt, $kttValues, true);
                            ?>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Penanggung Jawab Operasional / PJO dari Auditi (Penandatangan 2)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person-vcard text-info"></i></span>
                                    <input type="text" name="nama_pjo" id="inpNamaPjo" class="form-control" placeholder="Contoh: Mus Hermawan" value="<?php echo e($valNamaPjo); ?>">
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-link-45deg me-1"></i>Diisi otomatis dari Master Data Perusahaan (Nama KTT/PJO) dan dapat ditulis/diubah manual.
                                </small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Kepala Teknik Tambang / KTT Pemegang IUP (Penandatangan 3)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person-check text-primary"></i></span>
                                    <select name="nama_ktt" id="inpNamaKtt" class="form-select">
                                        <option value="" <?php echo e($valNamaKtt === '' ? 'selected' : ''); ?>>-- Pilih Nama KTT Pemegang IUP --</option>
                                        <?php foreach ($kttOptions as $opt): ?>
                                            <option value="<?php echo e($opt['nama_ktt']); ?>" <?php echo e($valNamaKtt === $opt['nama_ktt'] ? 'selected' : ''); ?>>
                                                <?php echo e($opt['nama_ktt']); ?> — <?php echo e($opt['nama_perusahaan']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <?php if ($hasCustomKtt): ?>
                                            <option value="<?php echo e($valNamaKtt); ?>" selected><?php echo e($valNamaKtt); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <small class="text-muted d-block mt-1">Diambil dari daftar Nama KTT/PJO perusahaan berlabel Pemegang IUP.</small>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Kepala Teknik Tambang / KTT (Penandatangan 2)</label>
                                <input type="text" name="nama_ktt" class="form-control" value="<?php echo e($tahapAwal->nama_ktt ?? ''); ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Metadata Kop & Footer Dokumen Terkendali -->
                <div class="col-lg-6">
                    <div class="card card-custom p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 border-bottom pb-2">
                            <h5 class="fw-bold text-slate-800 mb-0">
                                <i class="bi bi-file-earmark-medical text-secondary me-2"></i>Metadata Kop & Footer Dokumen Terkendali
                            </h5>
                            <?php echo $renderCopyPrevBtn('metadata_dokumen'); ?>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">No. Dokumen (Header)</label>
                                <input type="text" name="no_dokumen_header" class="form-control font-monospace" value="<?php echo e($tahapAwal->no_dokumen_header ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">No. Dokumen (Footer)</label>
                                <input type="text" name="no_dokumen_footer" class="form-control font-monospace" value="<?php echo e($tahapAwal->no_dokumen_footer ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">No. Revisi</label>
                                <input type="text" name="no_revisi" class="form-control" value="<?php echo e($tahapAwal->no_revisi ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Tanggal Terbit</label>
                                <input type="date" name="tanggal_terbit" class="form-control" value="<?php echo e($tahapAwal->tanggal_terbit ? $tahapAwal->tanggal_terbit->format('Y-m-d') : ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Tanggal Tinjau Ulang</label>
                                <input type="date" name="tanggal_tinjau_ulang" class="form-control" value="<?php echo e($tahapAwal->tanggal_tinjau_ulang ? $tahapAwal->tanggal_tinjau_ulang->format('Y-m-d') : ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Daftar Dokumen Kerja (Bab III.C) -->
                <div class="col-12">
                    <div class="card card-custom p-4 bg-light border">
                        <h6 class="fw-bold text-slate-800 mb-2">
                            <i class="bi bi-folder2-open text-primary me-1"></i>Daftar Dokumen Kerja Standar (Bab III.C — Tercetak Otomatis Sesuai Format Archi Indonesia)
                        </h6>
                        <ul class="small text-slate-700 mb-3">
                            <li>
                                Rencana Sampling pada Formulir Rencana Audit nomor <code>TT-MGT-FRS-026A</code>
                                <?php $rencanaStatus = isset($rencanaAudit) && $rencanaAudit ? $rencanaAudit->status : null; ?>
                                <?php if ($rencanaStatus === 'final'): ?>
                                    <span class="badge bg-success rounded-pill ms-1">Final</span>
                                <?php elseif ($rencanaStatus === 'draft'): ?>
                                    <span class="badge bg-warning text-dark rounded-pill ms-1">Draft</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill ms-1">Belum Dibuat</span>
                                <?php endif; ?>
                                <a href="<?php echo e(route('admin.audit-sesi.rencana.edit', $sesi->id)); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-0 ms-2">
                                    <i class="bi bi-calendar2-check me-1"></i>Buka Rencana Audit (026A)
                                </a>
                            </li>
                            <li>Kriteria Audit Lampiran II Kepdirjen Minerba Nomor <code>185.K/37.04/DJB/2019</code></li>
                            <li>Formulir Kesesuaian Audit (<code>TT-MGT-FRS-026E</code>), Formulir Ketidaksesuaian & Tindak Lanjut (<code>TT-MGT-FRS-026M</code>), Rekapitulasi Ketidaksesuaian (<code>TT-MGT-FRS-026F</code>), Rencana Tindak Lanjut Audit (<code>TT-MGT-FRS-026H</code>), Rekomendasi & OFI (<code>TT-MGT-FRS-026G</code>), dan Daftar Hadir (<code>TT-MGT-FRS-011A</code>).</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Sticky Action Bar -->
    <div class="card card-custom p-3 mt-4 sticky-bottom bg-white border-top shadow-lg d-flex flex-row align-items-center justify-content-between flex-wrap gap-2" style="z-index: 1010;">
        <div class="small text-muted">
            <i class="bi bi-info-circle text-primary me-1"></i> Perubahan pada data statis perusahaan (Izin, Komoditas, Alamat, KTT) juga akan tersinkronisasi ke Master Perusahaan.
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i> Simpan Dokumen Tahap Awal
            </button>
            <button type="submit" name="save_and_print" value="1" class="btn btn-dark rounded-3 px-4 fw-bold shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Simpan & Buka Cetak PDF
            </button>
        </div>
    </div>
</form>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
document.addEventListener('DOMContentLoaded', function () {
    function renumberTable(tableEl) {
        if (!tableEl) return;
        tableEl.querySelectorAll('tbody tr').forEach((tr, idx) => {
            const numCell = tr.querySelector('.row-num');
            if (numCell) numCell.textContent = idx + 1;
        });
    }

    function syncKetuaTimAuditFromTim1() {
        const inpKetua = document.getElementById('inpKetuaTimAudit');
        if (!inpKetua) return;

        let ketuaNama = '';
        const rows = document.querySelectorAll('#tableTim1 tbody tr');
        for (const tr of rows) {
            const selJabatan = tr.querySelector('.tim1-jabatan');
            const inpNama = tr.querySelector('.tim1-nama');
            if (selJabatan && inpNama && selJabatan.value === 'Ketua Tim Audit' && inpNama.value.trim() !== '') {
                ketuaNama = inpNama.value.trim();
                break;
            }
        }
        if (ketuaNama) {
            inpKetua.value = ketuaNama;
        }
    }

    const tableTim1 = document.getElementById('tableTim1');
    if (tableTim1) {
        tableTim1.addEventListener('input', function (e) {
            if (e.target && e.target.classList.contains('tim1-nama')) {
                syncKetuaTimAuditFromTim1();
            }
        });
        tableTim1.addEventListener('change', function (e) {
            if (e.target && e.target.classList.contains('tim1-jabatan')) {
                syncKetuaTimAuditFromTim1();
            }
        });
    }

    document.body.addEventListener('click', function (e) {
        const removeRowBtn = e.target.closest('.btn-remove-row');
        if (removeRowBtn) {
            const tr = removeRowBtn.closest('tr');
            const table = tr ? tr.closest('table') : null;
            if (tr && table && table.querySelectorAll('tbody tr').length > 1) {
                const wasTable1 = table.id === 'tableTim1';
                tr.remove();
                renumberTable(table);
                if (wasTable1) {
                    syncKetuaTimAuditFromTim1();
                }
            }
        }

        const removeItemBtn = e.target.closest('.btn-remove-item');
        if (removeItemBtn) {
            const group = removeItemBtn.closest('.input-group');
            if (group) group.remove();
        }
    });

    // Sinkronkan Form I.A dengan data master perusahaan & periode sesi
    const btnSyncPerusahaan = document.getElementById('btnSyncPerusahaan');
    if (btnSyncPerusahaan) {
        btnSyncPerusahaan.addEventListener('click', function () {
            const data = <?php echo json_encode([
                'nama'     => $comp->nama_perusahaan ?? '',
                'izin'     => $comp->jenis_perizinan ?? '',
                'komoditas'=> $comp->jenis_komoditas ?? '',
                'site'     => $comp->alamat_kantor_site ?? '',
                'tambang'  => $comp->alamat_lokasi_tambang ?? '',
                'pusat'    => $comp->alamat_kantor_pusat ?? '',
                'periode'  => $periodeDefault,
                'ktt'      => $comp->nama_ktt_default ?? '',
            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

            if (document.getElementById('input_nama_perusahaan_auditi')) {
                document.getElementById('input_nama_perusahaan_auditi').value = data.nama;
            }
            if (document.getElementById('input_jenis_perizinan')) {
                document.getElementById('input_jenis_perizinan').value = data.izin;
            }
            if (document.getElementById('input_jenis_komoditas')) {
                document.getElementById('input_jenis_komoditas').value = data.komoditas;
            }
            if (document.getElementById('input_alamat_kantor_site')) {
                document.getElementById('input_alamat_kantor_site').value = data.site;
            }
            if (document.getElementById('input_alamat_lokasi_tambang')) {
                document.getElementById('input_alamat_lokasi_tambang').value = data.tambang;
            }
            if (document.getElementById('input_alamat_kantor_pusat')) {
                document.getElementById('input_alamat_kantor_pusat').value = data.pusat;
            }
            if (document.getElementById('input_periode_audit_label')) {
                document.getElementById('input_periode_audit_label').value = data.periode;
            }

            const inpKtt = document.querySelector('input[name="nama_ktt"]');
            if (inpKtt && data.ktt) {
                inpKtt.value = data.ktt;
            }

            btnSyncPerusahaan.classList.remove('btn-outline-primary');
            btnSyncPerusahaan.classList.add('btn-success');
            btnSyncPerusahaan.innerHTML = '<i class="bi bi-check-circle me-1"></i> Data Tersinkron!';
            setTimeout(function() {
                btnSyncPerusahaan.classList.remove('btn-success');
                btnSyncPerusahaan.classList.add('btn-outline-primary');
                btnSyncPerusahaan.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Sinkron Data';
            }, 2500);
        });
    }

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
            if (typeof syncTanggalSelesaiOtomatis === 'function') {
                syncTanggalSelesaiOtomatis();
            }
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
            <td><input type="text" name="tim_audit_tahap_1[${idx}][nama]" class="form-control form-control-sm tim1-nama"></td>
            <td>
                <select name="tim_audit_tahap_1[${idx}][jabatan]" class="form-select form-select-sm tim1-jabatan">
                    <option value="" selected>-- Pilih Jabatan --</option>
                    <option value="Ketua Tim Audit">Ketua Tim Audit</option>
                    <option value="Auditor">Auditor</option>
                    <option value="Observer">Observer</option>
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
    function appendTim2Row(nama = '', jabatan = '', reg = '') {
        const tbody = document.querySelector('#tableTim2 tbody');
        const idx = tim2Counter++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center fw-bold row-num"></td>
            <td><input type="text" name="tim_audit_tahap_2[${idx}][nama]" class="form-control form-control-sm" value="${nama}"></td>
            <td>
                <select name="tim_audit_tahap_2[${idx}][jabatan]" class="form-select form-select-sm">
                    <option value="" ${!jabatan ? 'selected' : ''}>-- Pilih Jabatan --</option>
                    <option value="Ketua Tim Audit" ${jabatan === 'Ketua Tim Audit' ? 'selected' : ''}>Ketua Tim Audit</option>
                    <option value="Auditor" ${jabatan === 'Auditor' ? 'selected' : ''}>Auditor</option>
                    <option value="Observer" ${jabatan === 'Observer' ? 'selected' : ''}>Observer</option>
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
            const jab = select ? select.value : '';
            appendTim2Row(nama, jab, reg);
        });
    });

    const isKontraktor = <?php echo $isKontraktor ? 'true' : 'false'; ?>;

    // Add Risk Present / Future
    let riskCounter = 100;
    function addRiskRow(tableId, fieldPrefix) {
        const table = document.getElementById(tableId);
        const tbody = table.querySelector('tbody');
        const idx = riskCounter++;
        const tr = document.createElement('tr');
        const nilaiSisaCell = isKontraktor
            ? ''
            : `<td><input type="text" name="${fieldPrefix}[${idx}][nilai_sisa]" class="form-control form-control-sm text-center" value=""></td>`;
        tr.innerHTML = `
            <td class="text-center fw-bold row-num"></td>
            <td><textarea name="${fieldPrefix}[${idx}][kegiatan]" class="form-control form-control-sm" rows="2"></textarea></td>
            <td><input type="text" name="${fieldPrefix}[${idx}][risiko]" class="form-control form-control-sm" value=""></td>
            <td><input type="text" name="${fieldPrefix}[${idx}][nilai_awal]" class="form-control form-control-sm text-center" value=""></td>
            ${nilaiSisaCell}
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
            <td><input type="text" name="unjuk_kerja_peralatan[${idx}][pa]" class="form-control form-control-sm text-center" value=""></td>
            <td><input type="text" name="unjuk_kerja_peralatan[${idx}][ma]" class="form-control form-control-sm text-center" value=""></td>
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
            <td><input type="text" name="capaian_kli[${idx}][capaian]" class="form-control form-control-sm text-center" value=""></td>
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

    // ========================================================
    // Sinkronisasi Durasi Audit Mandays Tahap II & Jadwal Pelaksanaan
    // ========================================================
    const inpAlokasiTahap2 = document.getElementById('inpAlokasiTahap2');
    const inpTglMulai = document.getElementById('input_tanggal_mulai');
    const inpTglSelesai = document.getElementById('input_tanggal_selesai');
    const lblDurasiHari = document.getElementById('lblDurasiHari');
    const textBadgeDurasi = document.getElementById('textBadgeDurasi');
    const infoDurasiAuditText = document.getElementById('infoDurasiAuditText');

    function getDurasiHariAudit() {
        const alokasiText = inpAlokasiTahap2 ? inpAlokasiTahap2.value.trim() : '';

        // 1. Ekstrak nilai rincian hari per auditor, misal: "( 4.9 Hari, 5 Auditor)" atau "( 4,9 Hari, 5 Auditor)"
        const matchPerAuditor = alokasiText.match(/\(\s*([\d\.,]+)\s*Hari/i);
        if (matchPerAuditor) {
            const rawVal = parseFloat(matchPerAuditor[1].replace(',', '.'));
            if (!isNaN(rawVal) && rawVal > 0) {
                return Math.ceil(rawVal);
            }
        }

        // 2. Ekstrak angka pertama jika format teks langsung, misal "5 Hari" atau "4.9 Hari"
        const matchFirstNum = alokasiText.match(/^([\d\.,]+)\s*Hari/i);
        if (matchFirstNum) {
            const rawVal = parseFloat(matchFirstNum[1].replace(',', '.'));
            if (!isNaN(rawVal) && rawVal > 0) {
                const jmlAuditor = document.querySelectorAll('#tableTim2 tbody tr').length || 1;
                if (rawVal > 10 && jmlAuditor > 1) {
                    return Math.ceil(rawVal / jmlAuditor);
                }
                return Math.ceil(rawVal);
            }
        }

        // 3. Fallback kalkulasi manual dari input Mandays
        const total = parseFloat(document.getElementById('inpTotalMandays')?.value) || 0;
        const tahap1 = parseFloat(document.getElementById('inpAlokasiTahap1')?.value) || Math.round(total * 0.1);
        const jmlAuditor = document.querySelectorAll('#tableTim2 tbody tr').length || 1;
        if (total > 0 && jmlAuditor > 0) {
            const sisa = total - tahap1;
            const hariPerAuditor = sisa / jmlAuditor;
            if (hariPerAuditor > 0) {
                return Math.ceil(hariPerAuditor);
            }
        }

        return 1; // default minimal 1 hari
    }

    function syncTanggalSelesaiOtomatis() {
        const durasi = getDurasiHariAudit();

        if (lblDurasiHari) {
            lblDurasiHari.textContent = `${durasi} Hari`;
        }
        if (textBadgeDurasi) {
            textBadgeDurasi.textContent = `Durasi: ${durasi} Hari`;
        }

        if (inpTglMulai && inpTglMulai.value) {
            const parts = inpTglMulai.value.split('-');
            if (parts.length === 3) {
                const year = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10) - 1;
                const day = parseInt(parts[2], 10);

                const d = new Date(year, month, day);
                d.setDate(d.getDate() + (durasi - 1));

                const yyyy = d.getFullYear();
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                const computedSelesai = `${yyyy}-${mm}-${dd}`;

                if (inpTglSelesai) {
                    inpTglSelesai.value = computedSelesai;
                }

                if (infoDurasiAuditText) {
                    infoDurasiAuditText.innerHTML = `Durasi: <strong>${durasi} Hari</strong> (Otomatis: ${inpTglMulai.value} s/d ${computedSelesai}).`;
                }
            }
        }
    }

    if (inpTglMulai) {
        inpTglMulai.addEventListener('change', syncTanggalSelesaiOtomatis);
        inpTglMulai.addEventListener('input', syncTanggalSelesaiOtomatis);
    }
    if (inpAlokasiTahap2) {
        inpAlokasiTahap2.addEventListener('input', syncTanggalSelesaiOtomatis);
        inpAlokasiTahap2.addEventListener('change', syncTanggalSelesaiOtomatis);
    }

    // Monitoring perubahan jumlah auditor Tim 2 terhadap durasi
    document.getElementById('btnAddTim2')?.addEventListener('click', function () {
        setTimeout(syncTanggalSelesaiOtomatis, 100);
    });
    document.getElementById('btnCopyFromTim1')?.addEventListener('click', function () {
        setTimeout(syncTanggalSelesaiOtomatis, 100);
    });
    document.getElementById('tableTim2')?.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-row')) {
            setTimeout(syncTanggalSelesaiOtomatis, 100);
        }
    });

    // Interaktivitas Opsi Evaluasi Kecukupan Dokumentasi (Baik, Cukup, Kurang, Input Deskripsi)
    document.querySelectorAll('.kecukupan-row-container').forEach(function (container) {
        const select = container.querySelector('.sel-kecukupan');
        const wrapper = container.querySelector('.desc-kecukupan-wrapper');
        const textarea = container.querySelector('.txt-kecukupan-desc');
        const hiddenInp = container.querySelector('input[type="hidden"]');

        if (!select || !wrapper || !textarea || !hiddenInp) return;

        select.addEventListener('change', function () {
            if (this.value === 'custom') {
                wrapper.classList.remove('d-none');
                textarea.focus();
                hiddenInp.value = textarea.value.trim();
            } else {
                wrapper.classList.add('d-none');
                hiddenInp.value = this.value;
            }
        });

        textarea.addEventListener('input', function () {
            if (select.value === 'custom') {
                hiddenInp.value = this.value;
            }
        });
    });

    const formTahapAwal = document.getElementById('formTahapAwal');
    if (formTahapAwal) {
        formTahapAwal.addEventListener('submit', function () {
            document.querySelectorAll('.kecukupan-row-container').forEach(function (container) {
                const select = container.querySelector('.sel-kecukupan');
                const textarea = container.querySelector('.txt-kecukupan-desc');
                const hiddenInp = container.querySelector('input[type="hidden"]');
                if (select && hiddenInp) {
                    if (select.value === 'custom' && textarea) {
                        hiddenInp.value = textarea.value.trim();
                    } else if (select.value !== 'custom') {
                        hiddenInp.value = select.value;
                    }
                }
            });
        });
    }

    // Salin data per form/section dari Penyusunan Dokumen Tahap Awal periode sebelumnya (kecuali form daftar tim audit)
    const prevStage1Data = <?php echo json_encode($prevTahapAwalData ?? null, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;

    function setInputByName(name, value) {
        const el = document.querySelector(`[name="${CSS.escape(name)}"]`);
        if (el) {
            el.value = value ?? '';
        }
    }

    function flashCopyFeedback(btn) {
        if (!btn) return;
        const origHtml = btn.dataset.origHtml || btn.innerHTML;
        btn.dataset.origHtml = origHtml;
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success', 'text-white');
        btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Tersalin';

        const card = btn.closest('.card');
        if (card) {
            card.classList.add('border-success');
        }

        clearTimeout(btn._copyResetTimer);
        btn._copyResetTimer = setTimeout(() => {
            btn.classList.remove('btn-success', 'text-white');
            btn.classList.add('btn-outline-secondary');
            btn.innerHTML = origHtml;
            if (card) {
                card.classList.remove('border-success');
            }
        }, 1800);
    }

    function fillRiskTable(tableId, fieldPrefix, rows) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const tbody = table.querySelector('tbody');
        if (!tbody) return;
        tbody.innerHTML = '';

        const list = (Array.isArray(rows) && rows.length > 0)
            ? rows
            : [{ kegiatan: '', risiko: '', nilai_awal: '', nilai_sisa: '' }];

        list.forEach(item => {
            const idx = riskCounter++;
            const tr = document.createElement('tr');
            const nilaiSisaCell = isKontraktor
                ? ''
                : `<td><input type="text" name="${fieldPrefix}[${idx}][nilai_sisa]" class="form-control form-control-sm text-center"></td>`;
            tr.innerHTML = `
                <td class="text-center fw-bold row-num"></td>
                <td><textarea name="${fieldPrefix}[${idx}][kegiatan]" class="form-control form-control-sm" rows="2"></textarea></td>
                <td><input type="text" name="${fieldPrefix}[${idx}][risiko]" class="form-control form-control-sm"></td>
                <td><input type="text" name="${fieldPrefix}[${idx}][nilai_awal]" class="form-control form-control-sm text-center"></td>
                ${nilaiSisaCell}
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
            `;
            tr.querySelector('textarea').value = item.kegiatan ?? '';
            const inputs = tr.querySelectorAll('input');
            inputs[0].value = item.risiko ?? '';
            inputs[1].value = item.nilai_awal ?? '';
            if (!isKontraktor && inputs[2]) {
                inputs[2].value = item.nilai_sisa ?? '';
            }
            tbody.appendChild(tr);
        });
        renumberTable(table);
    }

    function fillTrendTable(trendKey, items) {
        const table = document.getElementById('tableTrend_' + trendKey);
        if (!table) return;
        const tbody = table.querySelector('tbody');
        if (!tbody) return;
        tbody.innerHTML = '';

        const list = (Array.isArray(items) && items.length > 0) ? items : [''];
        list.forEach(val => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center fw-bold row-num"></td>
                <td><input type="text" name="trend_data[${trendKey}][]" class="form-control form-control-sm"></td>
                <td class="text-center"><button type="button" class="btn btn-sm text-danger btn-remove-row"><i class="bi bi-x-lg"></i></button></td>
            `;
            tr.querySelector('input').value = val ?? '';
            tbody.appendChild(tr);
        });
        renumberTable(table);
    }

    function copySectionFromPrevious(section, btn) {
        if (!prevStage1Data) return;

        if (section === 'data_perusahaan' && prevStage1Data.data_perusahaan) {
            const d = prevStage1Data.data_perusahaan;
            setInputByName('nama_perusahaan_auditi', d.nama_perusahaan_auditi);
            setInputByName('perusahaan_induk', d.perusahaan_induk);
            setInputByName('jenis_perizinan', d.jenis_perizinan);
            setInputByName('jenis_komoditas', d.jenis_komoditas);
            setInputByName('alamat_kantor_site', d.alamat_kantor_site);
            setInputByName('alamat_lokasi_tambang', d.alamat_lokasi_tambang);
            setInputByName('alamat_kantor_pusat', d.alamat_kantor_pusat);
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'kinerja_kp' && prevStage1Data.kinerja_kp) {
            const k = prevStage1Data.kinerja_kp;
            ['cidera_ringan', 'cidera_berat', 'mati', 'fr', 'sr', 'kejadian_berbahaya', 'asr', 'mfr', 'penyakit_tk', 'frekuensi_pak'].forEach(key => {
                setInputByName(`kinerja_kp[${key}]`, k[key] ?? '');
            });
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'faktor_penyesuaian' && prevStage1Data.faktor_penyesuaian) {
            const fp = prevStage1Data.faktor_penyesuaian;
            for (let no = 1; no <= 7; no++) {
                const rawVal = (fp[no] ?? fp[String(no)] ?? '').toString().toLowerCase();
                document.querySelectorAll(`input[name="faktor_penyesuaian[${no}]"]`).forEach(radio => {
                    radio.checked = (radio.value === rawVal);
                });
            }
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'mandays' && prevStage1Data.mandays) {
            const m = prevStage1Data.mandays;
            setInputByName('jumlah_pekerja', m.jumlah_pekerja);
            setInputByName('kelas_risiko', m.kelas_risiko);
            setInputByName('mandays_awal', m.mandays_awal);
            setInputByName('faktor_penyesuaian_hari', m.faktor_penyesuaian_hari);
            setInputByName('total_mandays', m.total_mandays);
            setInputByName('alokasi_tahap_1', m.alokasi_tahap_1);
            setInputByName('alokasi_tahap_2', m.alokasi_tahap_2);
            syncTanggalSelesaiOtomatis();
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'jadwal_pelaksanaan' && prevStage1Data.jadwal_pelaksanaan) {
            const j = prevStage1Data.jadwal_pelaksanaan;
            setInputByName('tanggal_mulai', j.tanggal_mulai);
            setInputByName('tanggal_selesai', j.tanggal_selesai);
            syncTanggalSelesaiOtomatis();
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'kontak_awal' && prevStage1Data.kontak_awal) {
            const ka = prevStage1Data.kontak_awal;
            setInputByName('kontak_awal[hari_tanggal]', ka.hari_tanggal);
            setInputByName('kontak_awal[media]', ka.media);
            setInputByName('kontak_awal[perwakilan_auditi]', ka.perwakilan_auditi);
            setInputByName('kontak_awal[jabatan_perwakilan]', ka.jabatan_perwakilan);
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'kelayakan_audit' && prevStage1Data.kelayakan_audit) {
            const items = prevStage1Data.kelayakan_audit.items || {};
            for (let no = 1; no <= 7; no++) {
                setInputByName(`kelayakan_audit[${no}]`, items[no] ?? items[String(no)] ?? '');
            }
            setInputByName('kesimpulan_kelayakan', prevStage1Data.kelayakan_audit.kesimpulan ?? '');
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'kecukupan_dokumen' && prevStage1Data.kecukupan_dokumen) {
            const items = prevStage1Data.kecukupan_dokumen.items || {};
            const predefined = ['Baik', 'Cukup', 'Kurang'];
            for (let no = 1; no <= 7; no++) {
                const val = (items[no] ?? items[String(no)] ?? '').toString();
                const select = document.getElementById(`sel_kecukupan_${no}`);
                const wrapper = document.getElementById(`wrapper_desc_${no}`);
                const textarea = document.getElementById(`txt_desc_${no}`);
                const hiddenInp = document.getElementById(`inp_kecukupan_${no}`);
                if (select && wrapper && textarea && hiddenInp) {
                    if (predefined.includes(val)) {
                        select.value = val;
                        wrapper.classList.add('d-none');
                        textarea.value = '';
                        hiddenInp.value = val;
                    } else if (val.trim() !== '') {
                        select.value = 'custom';
                        wrapper.classList.remove('d-none');
                        textarea.value = val;
                        hiddenInp.value = val;
                    } else {
                        select.value = '';
                        wrapper.classList.add('d-none');
                        textarea.value = '';
                        hiddenInp.value = '';
                    }
                }
            }
            setInputByName('kesimpulan_kecukupan', prevStage1Data.kecukupan_dokumen.kesimpulan ?? '');
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'top_risks_present') {
            fillRiskTable('tableRiskPresent', 'top_risks_present', prevStage1Data.top_risks_present);
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'top_risks_future') {
            fillRiskTable('tableRiskFuture', 'top_risks_future', prevStage1Data.top_risks_future);
            flashCopyFeedback(btn);
            return;
        }

        if (section.startsWith('trend_')) {
            const tKey = section.replace('trend_', '');
            const trendObj = prevStage1Data.trend_data || {};
            fillTrendTable(tKey, trendObj[tKey]);
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'unjuk_kerja_peralatan') {
            const table = document.getElementById('tablePeralatan');
            const tbody = table?.querySelector('tbody');
            if (tbody) {
                tbody.innerHTML = '';
                const rows = (Array.isArray(prevStage1Data.unjuk_kerja_peralatan) && prevStage1Data.unjuk_kerja_peralatan.length > 0)
                    ? prevStage1Data.unjuk_kerja_peralatan
                    : [{ jenis_peralatan: '', pa: '', ma: '' }];
                rows.forEach(item => {
                    const idx = alatCounter++;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center fw-bold row-num"></td>
                        <td><input type="text" name="unjuk_kerja_peralatan[${idx}][jenis_peralatan]" class="form-control form-control-sm"></td>
                        <td><input type="text" name="unjuk_kerja_peralatan[${idx}][pa]" class="form-control form-control-sm text-center"></td>
                        <td><input type="text" name="unjuk_kerja_peralatan[${idx}][ma]" class="form-control form-control-sm text-center"></td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                    `;
                    const inputs = tr.querySelectorAll('input');
                    inputs[0].value = item.jenis_peralatan ?? '';
                    inputs[1].value = item.pa ?? '';
                    inputs[2].value = item.ma ?? '';
                    tbody.appendChild(tr);
                });
                renumberTable(table);
            }
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'capaian_kli') {
            const table = document.getElementById('tableKli');
            const tbody = table?.querySelector('tbody');
            if (tbody) {
                tbody.innerHTML = '';
                const rows = (Array.isArray(prevStage1Data.capaian_kli) && prevStage1Data.capaian_kli.length > 0)
                    ? prevStage1Data.capaian_kli
                    : [{ indikator: '', capaian: '' }];
                rows.forEach(item => {
                    const idx = kliCounter++;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center fw-bold row-num"></td>
                        <td><input type="text" name="capaian_kli[${idx}][indikator]" class="form-control form-control-sm"></td>
                        <td><input type="text" name="capaian_kli[${idx}][capaian]" class="form-control form-control-sm text-center"></td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                    `;
                    const inputs = tr.querySelectorAll('input');
                    inputs[0].value = item.indikator ?? '';
                    inputs[1].value = item.capaian ?? '';
                    tbody.appendChild(tr);
                });
                renumberTable(table);
            }
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'data_audit_sebelumnya' && prevStage1Data.data_audit_sebelumnya) {
            const prevAudit = prevStage1Data.data_audit_sebelumnya;
            setInputByName('data_audit_sebelumnya[tahun]', prevAudit.tahun);
            setInputByName('data_audit_sebelumnya[nilai_audit]', prevAudit.nilai_audit);
            setInputByName('data_audit_sebelumnya[status_kritikal]', prevAudit.status_kritikal);
            setInputByName('data_audit_sebelumnya[status_mayor]', prevAudit.status_mayor);
            setInputByName('data_audit_sebelumnya[status_minor]', prevAudit.status_minor);

            const wrap = document.getElementById('wrapCatatanInstansi');
            if (wrap) {
                wrap.innerHTML = '';
                const catatanList = (Array.isArray(prevAudit.catatan_instansi) && prevAudit.catatan_instansi.length > 0)
                    ? prevAudit.catatan_instansi
                    : [''];
                catatanList.forEach(textVal => {
                    const div = document.createElement('div');
                    div.className = 'input-group input-group-sm';
                    div.innerHTML = `
                        <input type="text" name="data_audit_sebelumnya[catatan_instansi][]" class="form-control">
                        <button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-x-lg"></i></button>
                    `;
                    div.querySelector('input').value = textVal ?? '';
                    wrap.appendChild(div);
                });
            }
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'harapan_pemangku') {
            const table = document.getElementById('tableHarapan');
            const tbody = table?.querySelector('tbody');
            if (tbody) {
                tbody.innerHTML = '';
                const rows = (Array.isArray(prevStage1Data.harapan_pemangku) && prevStage1Data.harapan_pemangku.length > 0)
                    ? prevStage1Data.harapan_pemangku
                    : [''];
                rows.forEach(hrp => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center fw-bold row-num"></td>
                        <td><textarea name="harapan_pemangku[]" class="form-control form-control-sm" rows="2"></textarea></td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                    `;
                    tr.querySelector('textarea').value = hrp ?? '';
                    tbody.appendChild(tr);
                });
                renumberTable(table);
            }
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'pengesahan' && prevStage1Data.pengesahan) {
            setInputByName('tanggal_berita_acara', prevStage1Data.pengesahan.tanggal_berita_acara);
            setInputByName('nama_pjo', prevStage1Data.pengesahan.nama_pjo);
            setInputByName('nama_ktt', prevStage1Data.pengesahan.nama_ktt);
            flashCopyFeedback(btn);
            return;
        }

        if (section === 'metadata_dokumen' && prevStage1Data.metadata_dokumen) {
            const md = prevStage1Data.metadata_dokumen;
            setInputByName('no_dokumen_header', md.no_dokumen_header);
            setInputByName('no_dokumen_footer', md.no_dokumen_footer);
            setInputByName('no_revisi', md.no_revisi);
            setInputByName('tanggal_terbit', md.tanggal_terbit);
            setInputByName('tanggal_tinjau_ulang', md.tanggal_tinjau_ulang);
            flashCopyFeedback(btn);
        }
    }

    document.querySelectorAll('.btn-copy-prev').forEach(btn => {
        btn.addEventListener('click', function () {
            const section = this.getAttribute('data-copy-section');
            if (section) {
                copySectionFromPrevious(section, this);
            }
        });
    });

    // Hitung Hari Kerja Audit dari Tabel Acuan via fetch + CSRF
    const btnHitungAcuan = document.getElementById('btnHitungDariTabelAcuan');
    const alertHitungAcuan = document.getElementById('alertHitungAcuan');
    const inpSumberPerhitungan = document.getElementById('inpSumberPerhitungan');
    const lblSumberPerhitungan = document.getElementById('lblSumberPerhitungan');
    const badgeSumberPerhitungan = document.getElementById('badgeSumberPerhitungan');

    function setSumberPerhitungan(mode) {
        if (inpSumberPerhitungan) inpSumberPerhitungan.value = mode;
        if (lblSumberPerhitungan) {
            lblSumberPerhitungan.textContent = mode === 'tabel' ? 'Tabel Acuan' : 'Manual';
        }
        if (badgeSumberPerhitungan) {
            badgeSumberPerhitungan.className = mode === 'tabel'
                ? 'badge rounded-pill px-2.5 py-1 bg-success bg-opacity-10 text-success border border-success border-opacity-25'
                : 'badge rounded-pill px-2.5 py-1 bg-secondary bg-opacity-10 text-secondary border';
        }
    }

    function showAlertAcuan(type, message) {
        if (!alertHitungAcuan) return;
        alertHitungAcuan.className = `alert alert-${type} py-2 px-3 small mb-3 rounded-3`;
        alertHitungAcuan.textContent = message;
    }

    document.querySelectorAll('.mandays-manual-watch').forEach(inp => {
        inp.addEventListener('input', function () {
            setSumberPerhitungan('manual');
        });
    });

    if (btnHitungAcuan) {
        btnHitungAcuan.addEventListener('click', async function () {
            const endpoint = this.getAttribute('data-endpoint');
            const jumlahPekerja = parseInt(document.getElementById('inpJumlahPekerja')?.value || '', 10);
            const kelasRisiko = document.getElementById('inpKelasRisiko')?.value || '';
            const faktorIds = Array.from(document.querySelectorAll('.chk-master-faktor:checked'))
                .map(el => parseInt(el.value, 10))
                .filter(v => !isNaN(v) && v > 0);

            if (isNaN(jumlahPekerja) || jumlahPekerja < 1) {
                showAlertAcuan('danger', 'Isi Jumlah Pekerja Auditi (minimal 1) sebelum menghitung dari tabel acuan.');
                return;
            }
            if (!kelasRisiko) {
                showAlertAcuan('danger', 'Pilih Kelas Risiko terlebih dahulu sebelum menghitung dari tabel acuan.');
                return;
            }

            const csrfToken = document.querySelector('#formTahapAwal input[name="_token"]')?.value
                || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || '';

            btnHitungAcuan.disabled = true;
            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        jumlah_pekerja: jumlahPekerja,
                        kelas_risiko: kelasRisiko,
                        faktor_ids: faktorIds,
                    }),
                });
                const payload = await res.json();
                if (!res.ok || !payload.success) {
                    showAlertAcuan('danger', payload.message || 'Gagal menghitung hari kerja dari tabel acuan.');
                    return;
                }

                const d = payload.data;
                const inpMandays = document.getElementById('inpMandaysAwal');
                const inpFaktor = document.getElementById('inpFaktorHari');
                const inpTotal = document.getElementById('inpTotalMandays');
                if (inpMandays) inpMandays.value = Number(d.mandays_awal).toFixed(1);
                if (inpFaktor) inpFaktor.value = Number(d.faktor_hari).toFixed(1);
                if (inpTotal) inpTotal.value = Number(d.total_mandays).toFixed(1);

                const tahap1 = Math.round(Number(d.total_mandays) * 0.1 * 10) / 10;
                const sisa = Math.max(0, Number(d.total_mandays) - tahap1);
                const jmlAuditor = document.querySelectorAll('#tableTim2 tbody tr').length || 1;
                const hariPerAuditor = (sisa / jmlAuditor).toFixed(1);
                const inpT1 = document.getElementById('inpAlokasiTahap1');
                const inpT2 = document.getElementById('inpAlokasiTahap2');
                if (inpT1) inpT1.value = tahap1.toFixed(1);
                if (inpT2) inpT2.value = `${sisa.toFixed(1).replace('.', ',')} Hari ( ${hariPerAuditor} Hari, ${jmlAuditor} Auditor)`;

                setSumberPerhitungan('tabel');
                if (typeof syncTanggalSelesaiOtomatis === 'function') {
                    syncTanggalSelesaiOtomatis();
                }

                if (d.warning) {
                    showAlertAcuan('warning', `Perhitungan berhasil (${d.rentang_label}). ${d.warning}`);
                } else {
                    showAlertAcuan('success', `Perhitungan berhasil dari tabel acuan (${d.rentang_label}). Anda tetap dapat menyesuaikan angka secara manual bila diperlukan.`);
                }
            } catch (err) {
                showAlertAcuan('danger', 'Terjadi kesalahan jaringan saat menghitung dari tabel acuan.');
            } finally {
                btnHitungAcuan.disabled = false;
            }
        });
    }

    // Inisialisasi awal sinkronisasi durasi & jadwal saat halaman dimuat
    syncTanggalSelesaiOtomatis();
    syncKetuaTimAuditFromTim1();
});
</script>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
