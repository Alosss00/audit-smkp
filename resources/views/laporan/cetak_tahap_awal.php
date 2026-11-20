<?php
/**
 * Cetak Berita Acara Hasil Pelaksanaan Tahapan Awal Audit Internal SMKP (TT-MGT-FRS-026L)
 * Mengacu pada Kepdirjen Minerba No. 185.K/37.04/DJB/2019 & SE Dirjen Minerba No. B-7/MB.07/DBT.KP/2022
 */

$namaPerusahaan = $tahapAwal->nama_perusahaan_auditi ?: ($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit);
$kodePerusahaan = $sesi->perusahaan->kode_perusahaan ?? '';
$searchStr = strtolower($namaPerusahaan . ' ' . $kodePerusahaan . ' ' . ($sesi->area_audit ?? ''));
if (str_contains($searchStr, 'tondano') || str_contains($searchStr, 'ttn')) {
    $companyLogo = 'logo_ttn.jpg';
} else {
    $companyLogo = 'logo_msm.jpg';
}

if (!function_exists('formatTglIndoLengkap')) {
    function formatTglIndoLengkap($dateObj, $fallback = '-') {
        if (!$dateObj) return $fallback;
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return (int)$dateObj->format('d') . ' ' . ($bulan[(int)$dateObj->format('n')] ?? '') . ' ' . $dateObj->format('Y');
    }
}

$thnPeriode = $sesi->tahun_periode ?? ($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y') : 2025);
$thnDepan = ((int) $thnPeriode) + 1;
$thnSebelumnya = ((int) $thnPeriode) - 1;

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

$tglSuratTugasFormatted = formatTglIndoLengkap($tahapAwal->tanggal_surat_tugas, '25 September 2024');
$tglTerbitFormatted = $tahapAwal->tanggal_terbit ? formatTglIndoLengkap($tahapAwal->tanggal_terbit) : '07 Mei 2025';
$tglTinjauFormatted = $tahapAwal->tanggal_tinjau_ulang ? formatTglIndoLengkap($tahapAwal->tanggal_tinjau_ulang) : '07 Mei 2028';
$tglBeritaAcaraFormatted = $tahapAwal->tanggal_berita_acara ? formatTglIndoLengkap($tahapAwal->tanggal_berita_acara) : '4 Januari 2026';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tahap Awal Audit_<?php echo e($namaPerusahaan); ?>-<?php echo e($thnPeriode); ?> (<?php echo e($tahapAwal->no_dokumen_header); ?>)</title>

    <link href="<?php echo asset('vendor/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo asset('vendor/bootstrap-icons/bootstrap-icons.min.css'); ?>" rel="stylesheet">

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 16mm 14mm 16mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #e2e8f0;
            color: #000000;
            font-size: 10.5pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .action-bar-top {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 1050;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .doc-wrapper {
            max-width: 210mm;
            margin: 24px auto;
            background: #ffffff;
            padding: 14mm 18mm;
            box-shadow: 0 6px 24px rgba(0,0,0,0.12);
        }

        /* Master Layout Table for Repeating Header & Footer on Print */
        table.master-print-layout {
            width: 100%;
            border-collapse: collapse;
        }

        table.master-print-layout > thead {
            display: table-header-group;
        }

        table.master-print-layout > tfoot {
            display: table-footer-group;
        }

        /* 3-Box Header Table (Matching TT-MGT-FRS-026L) */
        .header-box-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #cbd5e1;
        }

        .header-box-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            vertical-align: middle;
        }

        .header-logo-col {
            width: 22%;
            text-align: center;
        }

        .header-logo-col img {
            max-height: 52px;
            max-width: 155px;
            object-fit: contain;
        }

        .header-title-col {
            width: 56%;
            text-align: center;
            font-family: "Times New Roman", Times, serif;
            color: #000000;
            line-height: 1.25;
        }

        .header-title-col .proj-name {
            font-size: 13pt;
            font-weight: 700;
            text-transform: uppercase;
        }

        .header-title-col .doc-name {
            font-size: 12pt;
            font-weight: 700;
        }

        .header-title-col .doc-code {
            font-size: 9.5pt;
            font-weight: 700;
            margin-top: 2px;
        }

        /* Controlled Document Footer Table */
        .footer-doc-table {
            width: 100%;
            border-collapse: collapse;
            font-family: "Times New Roman", Times, serif;
            font-size: 8pt;
            margin-top: 18px;
            border: 1px solid #cbd5e1;
        }

        .footer-doc-table td {
            border: 1px solid #cbd5e1;
            padding: 2px 6px;
            vertical-align: middle;
        }

        .footer-red-warning {
            color: #c00000;
            font-weight: 700;
            text-align: center;
        }

        /* Content Headings & Tables */
        .bab-title {
            font-size: 10.5pt;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 14px;
            margin-bottom: 10px;
            display: flex;
            gap: 14px;
        }

        .sub-bab-title {
            font-size: 10.5pt;
            font-weight: 700;
            margin-top: 12px;
            margin-bottom: 8px;
            margin-left: 24px;
        }

        .content-indent {
            margin-left: 44px;
        }

        .sub-section-heading {
            font-size: 10pt;
            font-weight: 700;
            margin-top: 14px;
            margin-bottom: 8px;
        }

        table.formal-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10pt;
        }

        table.formal-table th,
        table.formal-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
            vertical-align: middle;
        }

        table.formal-table thead th {
            background-color: #d9d9d9 !important;
            color: #000000 !important;
            font-weight: 700;
            text-align: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        table.formal-table.white-header thead th {
            background-color: #ffffff !important;
        }

        table.formal-table tr {
            page-break-inside: avoid;
        }

        p.narrative {
            text-align: justify;
            margin-bottom: 10px;
            line-height: 1.45;
        }

        ol.formal-list {
            margin-top: 4px;
            margin-bottom: 10px;
            padding-left: 22px;
        }

        ol.formal-list > li {
            margin-bottom: 4px;
            text-align: justify;
        }

        .strikethrough {
            text-decoration: line-through;
        }

        @media print {
            body {
                background: #ffffff !important;
            }
            .no-print {
                display: none !important;
            }
            .doc-wrapper {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            .page-break-before {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (No Print) -->
    <div class="action-bar-top no-print">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-info text-dark fw-bold px-2.5 py-1.5"><?php echo e($tahapAwal->no_dokumen_header); ?></span>
            <div>
                <div class="fw-bold">Formulir Berita Acara Hasil Pelaksanaan Tahapan Awal Audit Internal SMKP</div>
                <small class="text-light opacity-75"><?php echo e($namaPerusahaan); ?> &bull; Periode Audit <?php echo e($thnPeriode); ?></small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <?php if(auth()->check() && in_array(auth()->user()->role, ['admin', 'auditor_smkp'])): ?>
                <a href="<?php echo e(route('admin.audit-sesi.tahap-awal', $sesi->id)); ?>" class="btn btn-sm btn-warning fw-bold px-3">
                    <i class="bi bi-pencil-square me-1"></i> Edit Formulir Tahap Awal
                </a>
            <?php endif; ?>
            <button type="button" id="btnPrintDoc" class="btn btn-sm btn-info fw-bold px-3 text-dark">
                <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan PDF (A4)
            </button>
            <button type="button" id="btnCloseDoc" class="btn btn-sm btn-outline-light px-3">
                <i class="bi bi-x-lg me-1"></i> Tutup
            </button>
        </div>
    </div>

    <div class="doc-wrapper" id="printableTahapAwal">
        <table class="master-print-layout">
            <!-- REPEATING DOCUMENT HEADER -->
            <thead>
                <tr>
                    <td>
                        <table class="header-box-table">
                            <tr>
                                <td class="header-logo-col">
                                    <img src="<?php echo asset('images/' . $companyLogo); ?>" alt="Logo Perusahaan">
                                </td>
                                <td class="header-title-col">
                                    <div class="proj-name">TOKA TINDUNG PROJECT</div>
                                    <div class="doc-name">Formulir Berita Acara Hasil Pelaksanaan<br>Tahapan Awal Audit Internal SMKP</div>
                                    <div class="doc-code"><?php echo e($tahapAwal->no_dokumen_header); ?></div>
                                </td>
                                <td class="header-logo-col">
                                    <img src="<?php echo asset('images/logo_archi.png'); ?>" alt="Archi Indonesia">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </thead>

            <!-- MAIN DOCUMENT BODY -->
            <tbody>
                <tr>
                    <td>
                        <!-- ==========================================
                             BAB I. INFORMASI PELAKSANAAN AUDIT SMKP
                             ========================================== -->
                        <div class="bab-title">
                            <span>I.</span>
                            <span>INFORMASI PELAKSANAAN AUDIT SMKP</span>
                        </div>

                        <div class="sub-bab-title">A. Data Perusahaan Auditi</div>

                        <div class="content-indent">
                            <table class="formal-table">
                                <tbody>
                                    <tr>
                                        <td style="width: 44%;">Nama Perusahaan Auditi</td>
                                        <td><?php echo e($namaPerusahaan); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Perizinan</td>
                                        <td><?php echo nl2br(e($tahapAwal->jenis_perizinan)); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Komoditas</td>
                                        <td><?php echo e($tahapAwal->jenis_komoditas); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Alamat Perusahaan</td>
                                        <td>
                                            <?php if(!empty($tahapAwal->alamat_kantor_site)): ?>
                                                <div>Kantor Site:</div>
                                                <div class="mb-2"><?php echo nl2br(e($tahapAwal->alamat_kantor_site)); ?></div>
                                            <?php endif; ?>
                                            <?php if(!empty($tahapAwal->alamat_lokasi_tambang)): ?>
                                                <div>Lokasi Tambang:</div>
                                                <div class="mb-2"><?php echo nl2br(e($tahapAwal->alamat_lokasi_tambang)); ?></div>
                                            <?php endif; ?>
                                            <?php if(!empty($tahapAwal->alamat_kantor_pusat)): ?>
                                                <div>Kantor Pusat:</div>
                                                <div><?php echo nl2br(e($tahapAwal->alamat_kantor_pusat)); ?></div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Periode Audit</td>
                                        <td><?php echo e($tahapAwal->periode_audit_label ?: "1 Januari {$thnPeriode} s/d 31 Desember {$thnPeriode}"); ?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="sub-section-heading">Data Kinerja Keselamatan Pertambangan pada Periode Audit</div>
                            <table class="formal-table">
                                <tbody>
                                    <tr>
                                        <td style="width: 55%;">Jumlah Kecelakaan Tambang<br>Berakibat Cidera Ringan</td>
                                        <td><?php echo e($kinerja['cidera_ringan'] ?? '0'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah Kecelakaan Tambang<br>Berakibat Cidera Berat</td>
                                        <td><?php echo e($kinerja['cidera_berat'] ?? '0'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah Kecelakaan Tambang<br>Berakibat Mati</td>
                                        <td><?php echo e($kinerja['mati'] ?? '0'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><em>Frequency Rate</em> Kecelakaan Tambang</td>
                                        <td><?php echo e($kinerja['fr'] ?? '0.33'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><em>Severity Rate</em> Kecelakaan Tambang</td>
                                        <td><?php echo e($kinerja['sr'] ?? '1,973.19'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah Kejadian Berbahaya</td>
                                        <td><?php echo e($kinerja['kejadian_berbahaya'] ?? '1'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><em>Absence Severity Rate</em></td>
                                        <td><?php echo e($kinerja['asr'] ?? '16.97'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><em>Morbidity Frequency Rate</em></td>
                                        <td><?php echo e($kinerja['mfr'] ?? '16.97'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah Kejadian Akibat Penyakit<br>Tenaga Kerja</td>
                                        <td><?php echo e($kinerja['penyakit_tk'] ?? '0'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Frekuensi Penyakit Akibat Kerja</td>
                                        <td><?php echo e($kinerja['frekuensi_pak'] ?? '0'); ?></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="sub-section-heading">Dasar Faktor Penyesuaian</div>
                            <table class="formal-table white-header">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Kondisi</th>
                                        <th style="width: 75px;">Ya</th>
                                        <th style="width: 75px;">Tidak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($kondisiPenyesuaian as $no => $kondisiText): ?>
                                        <?php $isYa = strtolower($faktorPenyesuaian[$no] ?? 'tidak') === 'ya'; ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($no); ?></td>
                                            <td style="text-align: justify;"><?php echo e($kondisiText); ?></td>
                                            <td class="text-center fw-bold"><?php echo $isYa ? '&radic;' : ''; ?></td>
                                            <td class="text-center fw-bold"><?php echo !$isYa ? '&radic;' : ''; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="sub-bab-title">B. Perhitungan Hari Kerja Audit dan Jumlah Auditor</div>
                        <div class="content-indent">
                            <table class="formal-table">
                                <tbody>
                                    <tr>
                                        <td style="width: 44%;">Jumlah Pekerja Auditi</td>
                                        <td><?php echo e($tahapAwal->jumlah_pekerja); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Kelas Risiko</td>
                                        <td><?php echo e($tahapAwal->kelas_risiko); ?></td>
                                    </tr>
                                    <tr>
                                        <td><em>Mandays</em></td>
                                        <td><?php echo e(rtrim(rtrim(number_format((float)$tahapAwal->mandays_awal, 1, '.', ''), '0'), '.')); ?> Hari</td>
                                    </tr>
                                    <tr>
                                        <td>Faktor Penyesuaian</td>
                                        <td><?php echo e(rtrim(rtrim(number_format((float)$tahapAwal->faktor_penyesuaian_hari, 1, '.', ''), '0'), '.')); ?> Hari</td>
                                    </tr>
                                    <tr>
                                        <td>Total <em>Mandays</em></td>
                                        <td><?php echo e(rtrim(rtrim(number_format((float)$tahapAwal->total_mandays, 1, '.', ''), '0'), '.')); ?> Hari</td>
                                    </tr>
                                    <tr>
                                        <td>Alokasi <em>Mandays</em><br>untuk Tahap I Audit<br>(Maksimal 10% dari Total)</td>
                                        <td><?php echo e(rtrim(rtrim(number_format((float)$tahapAwal->alokasi_tahap_1, 1, '.', ''), '0'), '.')); ?> Hari</td>
                                    </tr>
                                    <tr>
                                        <td>Alokasi <em>Mandays</em><br>untuk Tahap II Audit<br>(Minimal 90% dari Total)</td>
                                        <td><?php echo e($tahapAwal->alokasi_tahap_2); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- ==========================================
                             BAB II. HASIL PERMULAAN AUDIT & DOKUMEN
                             ========================================== -->
                        <div class="bab-title page-break-before">
                            <span>II.</span>
                            <span>HASIL PERMULAAN AUDIT DAN PENINJAUAN DOKUMEN</span>
                        </div>

                        <div class="sub-bab-title">A. Penetapan Tim Audit Tahap I</div>
                        <div class="content-indent">
                            <p class="narrative">
                                Kepala Teknik Tambang <?php echo e($namaPerusahaan); ?> melalui Surat Pengangkatan Tim Audit nomor <?php echo e($tahapAwal->no_surat_tugas); ?> tanggal <?php echo e($tglSuratTugasFormatted); ?> telah menugaskan Tim Audit untuk Tahap Pertama Audit Internal Sistem Manajemen Keselamatan Pertambangan (Permulaan Audit, Peninjauan Dokumen, Persiapan Audit Lapangan) di <?php echo e($namaPerusahaan); ?>, sebagai berikut.
                            </p>

                            <table class="formal-table white-header">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Nama</th>
                                        <th style="width: 150px;">Jabatan</th>
                                        <th style="width: 240px;">Nomor<br>Registrasi Auditor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($tim1 as $idx => $t): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($t['nama'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo e($t['jabatan'] ?? 'Auditor'); ?></td>
                                            <td class="text-center"><?php echo e($t['no_registrasi'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="sub-bab-title">B. Pelaksanaan Kontak Awal dengan Auditi</div>
                        <div class="content-indent">
                            <p class="narrative mb-1">
                                Kontak awal dengan auditi telah dilakukan secara formal/informal dengan rincian sebagai berikut:
                            </p>
                            <table style="width: 100%; margin-bottom: 12px; font-size: 10pt;">
                                <tr>
                                    <td style="width: 20px;">&bull;</td>
                                    <td style="width: 210px;">Hari / Tanggal</td>
                                    <td style="width: 12px;">:</td>
                                    <td><?php echo e($kontak['hari_tanggal'] ?? '-'); ?></td>
                                </tr>
                                <tr>
                                    <td>&bull;</td>
                                    <td>Media</td>
                                    <td>:</td>
                                    <td><?php echo e($kontak['media'] ?? 'Meeting'); ?></td>
                                </tr>
                                <tr>
                                    <td>&bull;</td>
                                    <td>Perwakilan Auditi</td>
                                    <td>:</td>
                                    <td><?php echo e($kontak['perwakilan_auditi'] ?? '-'); ?></td>
                                </tr>
                                <tr>
                                    <td>&bull;</td>
                                    <td>Jabatan Perwakilan Auditi</td>
                                    <td>:</td>
                                    <td><?php echo e($kontak['jabatan_perwakilan'] ?? 'Kepala Teknik Tambang'); ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="sub-bab-title">C. Penentuan Kelayakan Audit</div>
                        <div class="content-indent">
                            <p class="narrative">
                                Penentuan Kelayakan Audit Internal Sistem Manajemen Keselamatan Pertambangan dilakukan pada hari <?php echo e($kontak['hari_tanggal'] ?? '-'); ?>, dengan hasil sebagai berikut:
                            </p>

                            <table class="formal-table white-header">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Indikator Kelayakan Audit</th>
                                        <th style="width: 180px;">Hasil Evaluasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($indikatorKelayakan as $no => $indText): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($no); ?></td>
                                            <td><?php echo e($indText); ?></td>
                                            <td class="text-center"><?php echo e($kelayakan[$no] ?? 'Tersedia dan Layak'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <p class="narrative">
                                Dengan mempertimbangkan ketersediaan faktor-faktor tersebut di atas, maka pelaksanaan Audit Internal Sistem Manajemen Keselamatan Pertambangan di <?php echo e($namaPerusahaan); ?>

                                <?php if($tahapAwal->kesimpulan_kelayakan === 'layak'): ?>
                                    <strong>layak / <span class="strikethrough">tidak layak</span></strong>
                                <?php else: ?>
                                    <strong><span class="strikethrough">layak</span> / tidak layak</strong>
                                <?php endif; ?>
                                untuk dilaksanakan.
                            </p>
                        </div>

                        <div class="sub-bab-title">D. Penentuan Kecukupan Dokumentasi</div>
                        <div class="content-indent">
                            <p class="narrative">
                                Peninjauan dokumentasi Sistem Manajemen Keselamatan Pertambangan mencakup dokumen dan rekaman milik <?php echo e($namaPerusahaan); ?> pada periode audit, dengan memperhatikan ukuran, sifat dan kompleksitas organisasi, serta tujuan dan ruang lingkup audit terhadap 7 (tujuh) elemen Sistem Manajemen Keselamatan Pertambangan sesuai Lampiran II Keputusan Direktur Jenderal Mineral dan Batubara Nomor 185.K/37.04/DJB/2019.<br>
                                Hasil peninjauan dokumen Sistem Manajemen Keselamatan Pertambangan <?php echo e($namaPerusahaan); ?> sebagai berikut:
                            </p>

                            <table class="formal-table white-header">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th style="width: 32%;">Peninjauan Dokumentasi</th>
                                        <th>Hasil Evaluasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($elemenDokumentasi as $no => $elText): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($no); ?></td>
                                            <td><?php echo e($elText); ?></td>
                                            <td><?php echo e($kecukupan[$no] ?? 'Baik'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <p class="narrative">
                                Berdasarkan hasil evaluasi tersebut di atas, maka pelaksanaan Audit Internal Sistem Manajemen Keselamatan Pertambangan di <?php echo e($namaPerusahaan); ?><br>
                                <strong><?php echo e($tahapAwal->kesimpulan_kecukupan === 'dapat_dilanjutkan' ? 'dapat dilanjutkan*)' : 'belum dapat dilanjutkan*)'); ?></strong> ke tahap berikutnya.
                            </p>
                        </div>

                        <!-- ==========================================
                             BAB III. PERSIAPAN AUDIT LAPANGAN
                             ========================================== -->
                        <div class="bab-title page-break-before">
                            <span>III.</span>
                            <span>PERSIAPAN AUDIT LAPANGAN</span>
                        </div>

                        <div class="sub-bab-title">A. Penetapan Tim Audit Tahap II</div>
                        <div class="content-indent">
                            <p class="narrative">
                                Kepala Teknik Tambang <?php echo e($namaPerusahaan); ?> melalui Surat Pengangkatan Tim Audit nomor <?php echo e($tahapAwal->no_surat_tugas); ?> tanggal <?php echo e($tglSuratTugasFormatted); ?> telah menugaskan Tim Audit untuk Tahap Kedua Audit Internal Sistem Manajemen Keselamatan Pertambangan (Pelaksanaan Rapat Pembukaan, Pengumpulan dan Verifikasi Informasi, Perumusan Temuan Audit, Penyiapan Kesimpulan Audit, Pelaksanaan Rapat Penutupan), sebagai berikut:
                            </p>

                            <table class="formal-table white-header">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Nama</th>
                                        <th style="width: 150px;">Jabatan</th>
                                        <th style="width: 240px;">Nomor<br>Registrasi Auditor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($tim2 as $idx => $t): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($t['nama'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo e($t['jabatan'] ?? 'Auditor'); ?></td>
                                            <td class="text-center"><?php echo e($t['no_registrasi'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <p class="narrative">
                                Penugasan tersebut telah mempertimbangkan tujuan, ruang lingkup, kriteria dan perkiraan waktu audit, kompetensi tim audit secara keseluruhan yang diperlukan untuk mencapai tujuan audit, peraturan perundang-undangan yang berlaku, kebutuhan untuk menjamin keindependenan tim audit dari kegiatan yang diaudit dan untuk menghindarkan konflik kepentingan kemampuan anggota tim audit untuk berinteraksi secara efektif dengan auditi dan untuk bekerja bersama dalam tim, dan bahasa yang digunakan dalam audit, dan pemahaman terhadap karakteristik sosial dan budaya tertentu dari auditi. Seluruh auditor internal yang diangkat memiliki komitmen untuk menjaga integritas dan perilaku yang profesional, independen, jujur, dan objektif dalam pelaksanaan tugas, yang dibuktikan dengan Surat Penugasan.
                            </p>
                            <p class="narrative">
                                Auditor yang ditugaskan juga telah memperhatikan persyaratan yang ditetapkan pada Lampiran II Keputusan Direktur Jenderal Mineral dan Batubara nomor 185.K/37.04/DJB/2019 tentang Petunjuk Teknis Pelaksanaan Keselamatan Pertambangan dan Pelaksanaan, Penilaian, dan Pelaporan Sistem Manajemen Keselamatan Pertambangan dan Surat Edaran Direktur Teknik dan Lingkungan Mineral dan Batubara/Kepala Inspektur Tambang nomor B-7/MB.07/DBT.KP/2022 tanggal 3 Januari 2022 perihal Auditor Internal Sistem Manajemen Keselamatan Pertambangan.
                            </p>
                            <p class="narrative">
                                Penetapan tanggung jawab Auditor ditetapkan sesuai Prosedur Audit Internal Sistem Manajemen Keselamatan Pertambangan <?php echo e($namaPerusahaan); ?> dan Surat Pengangkatan Auditor nomor <?php echo e($tahapAwal->no_surat_tugas); ?> tanggal <?php echo e($tglSuratTugasFormatted); ?>.
                            </p>
                        </div>

                        <div class="sub-bab-title">B. Penyiapan Rencana Audit</div>
                        <div class="content-indent">
                            <p class="narrative">
                                Rencana Audit Internal Sistem Manajemen Keselamatan Pertambangan menjadi dasar kesepakatan antara klien audit, Tim audit dan auditi terkait dengan pelaksanaan Audit Internal Sistem Manajemen Keselamatan Pertambangan Tahap II di <?php echo e($namaPerusahaan); ?>, yang meliputi kegiatan:
                            </p>
                            <ol class="formal-list" style="list-style-type: none; padding-left: 0;">
                                <li>1) &nbsp;Pelaksanaan Rapat Pembukaan</li>
                                <li>2) &nbsp;Pengumpulan dan Verifikasi Informasi</li>
                                <li>3) &nbsp;Perumusan Temuan Audit</li>
                                <li>4) &nbsp;Penyiapan Kesimpulan Audit</li>
                                <li>5) &nbsp;Pelaksanaan Rapat Penutupan</li>
                            </ol>

                            <p class="narrative">
                                Rencana Audit Internal Sistem Manajemen Keselamatan Pertambangan Tahap II mencakup:
                            </p>
                            <ol class="formal-list" style="list-style-type: none; padding-left: 0;">
                                <li>1) &nbsp;Penetapan Tujuan Audit</li>
                                <li>2) &nbsp;Penetapan Kriteria Audit</li>
                                <li>3) &nbsp;Penetapan Ruang Lingkup Audit</li>
                                <li>4) &nbsp;Penetapan Tanggal Pelaksanaan Audit</li>
                                <li>5) &nbsp;Penetapan Susunan Kegiatan Audit</li>
                                <li>6) &nbsp;Pembagian Tugas dan Tanggung Jawab Anggota Tim Audit</li>
                                <li>7) &nbsp;Penetapan Alokasi Sumberdaya terkait</li>
                                <li>8) &nbsp;Penetapan Metode dan Sampel Audit</li>
                                <li>9) &nbsp;Pengesahan Rencana Audit dari Kepala Teknik Tambang, Penanggung Jawab Operasional (untuk auditi yang merupakan perusahaan jasa pertambangan) dan Ketua Tim Audit</li>
                            </ol>

                            <p class="narrative">
                                Rencana Audit yang memuat informasi angka nomor 1 (satu) s.d. 9 (sembilan) tersebut di atas ditetapkan secara lengkap pada Formulir Rencana Audit <?php echo e($namaPerusahaan); ?> nomor TT-MGT-FRS-026 Formulir Rencanan Audit
                            </p>
                            <p class="narrative">
                                Mempertimbangkan bahwa program Audit Internal Sistem Manajemen Keselamatan Pertambangan wajib didasarkan pada hasil penilaian risiko pada kegiatan operasional dan hasil penilaian kinerja Keselamatan Pertambangan dari Auditi, dan berikut informasi terkait dari <?php echo e($namaPerusahaan); ?> yang <strong>wajib dipertimbangkan</strong> Auditor dalam penetapan metode dan sampel khususnya sebagai <em>critical point</em> untuk jenis pembuktian primer:
                            </p>

                            <!-- Tabel 1: Top Risks Periode Audit Berjalan -->
                            <div class="sub-section-heading">Data Top Risks aspek Keselamatan Pertambangan: Periode Audit <?php echo e($thnPeriode); ?> (<em>Risk of Present</em>)</div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="width: 42px;">No</th>
                                        <th rowspan="2">Kegiatan</th>
                                        <th rowspan="2" style="width: 175px;">Risiko</th>
                                        <th colspan="2">Nilai Risiko</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 100px;">Awal</th>
                                        <th style="width: 100px;">Sisa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($risksPresent as $idx => $rp): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($rp['kegiatan'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo e($rp['risiko'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo e($rp['nilai_awal'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo e($rp['nilai_sisa'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Tabel 2: Top Risks Rencana Kegiatan Tahun Depan -->
                            <div class="sub-section-heading">Data Top Risks aspek Keselamatan Pertambangan: Rencana Kegiatan <?php echo e($thnDepan); ?> (<em>Risk of Future</em>)</div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="width: 42px;">No</th>
                                        <th rowspan="2">Kegiatan</th>
                                        <th rowspan="2" style="width: 175px;">Risiko</th>
                                        <th colspan="2">Nilai Risiko</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 100px;">Awal</th>
                                        <th style="width: 100px;">Sisa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($risksFuture as $idx => $rf): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($rf['kegiatan'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo e($rf['risiko'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo e($rf['nilai_awal'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo e($rf['nilai_sisa'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Enam Tabel Trend -->
                            <div class="sub-section-heading">Data <em>Trend</em> Faktor Penyebab <em>Near Miss, Property Damage</em>, Kejadian Berbahaya, dan/atau Kecelakaan di <?php echo e($namaPerusahaan); ?> pada Periode Audit <?php echo e($thnPeriode); ?></div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Faktor Penyebab Dominan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(($trends['faktor_penyebab'] ?? []) as $idx => $val): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($val); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="sub-section-heading">Data <em>Trend</em> Lokasi Terjadinya <em>Near Miss, Property Damage</em>, Kejadian Berbahaya, dan/atau Kecelakaan di <?php echo e($namaPerusahaan); ?> pada Periode Audit <?php echo e($thnPeriode); ?></div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Lokasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(($trends['lokasi'] ?? []) as $idx => $val): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($val); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="sub-section-heading">Data <em>Trend</em> Jenis Kegiatan terkait Kejadian <em>Near Miss, Property Damage</em>, Kejadian Berbahaya, dan/atau Kecelakaan di <?php echo e($namaPerusahaan); ?> pada Periode Audit <?php echo e($thnPeriode); ?></div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Jenis Kegiatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(($trends['jenis_kegiatan'] ?? []) as $idx => $val): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($val); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="sub-section-heading">Data <em>Trend</em> Jenis Jabatan terkait Kejadian <em>Near Miss, Property Damage</em>, Kejadian Berbahaya, dan/atau Kecelakaan di <?php echo e($namaPerusahaan); ?> pada Periode Audit <?php echo e($thnPeriode); ?></div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Jenis Jabatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(($trends['jenis_jabatan'] ?? []) as $idx => $val): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($val); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="sub-section-heading">Data <em>Trend</em> Deviasi Keselamatan Pertambangan berdasarkan Hasil Temuan Inspeksi di <?php echo e($namaPerusahaan); ?> pada Periode Audit <?php echo e($thnPeriode); ?></div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Deviasi Penerapan Keselamatan Pertambangan Hasil Inspeksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(($trends['deviasi_inspeksi'] ?? []) as $idx => $val): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($val); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="sub-section-heading">Data <em>Trend</em> Faktor Penyebab Kejadian Akibat Penyakit Tenaga Kerja dan/atau Penyakit Akibat Kerja di <?php echo e($namaPerusahaan); ?> pada Periode Audit <?php echo e($thnPeriode); ?></div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Faktor Penyebab Dominan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(($trends['faktor_pak'] ?? ['Tidak Ada']) as $idx => $val): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($val); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Data Unjuk Kerja Peralatan -->
                            <div class="sub-section-heading">Data Unjuk Kerja Peralatan Pertambangan <?php echo e($namaPerusahaan); ?> pada Periode Audit <?php echo e($thnPeriode); ?></div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Jenis Peralatan</th>
                                        <th style="width: 130px;"><em>Physical<br>Availability</em></th>
                                        <th style="width: 130px;"><em>Mechanical<br>Availability</em></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($peralatan as $idx => $alat): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($alat['jenis_peralatan'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo e($alat['pa'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo e($alat['ma'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Data Capaian KLI -->
                            <div class="sub-section-heading">Data Capaian <em>Key Leading Indicator</em> <?php echo e($namaPerusahaan); ?> pada Periode Audit <?php echo e($thnPeriode); ?></div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th><em>Key Leading Indicator</em></th>
                                        <th style="width: 220px;">Status Pencapaian hingga<br>Tanggal Pelaksanaan Audit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($klis as $idx => $kli): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($kli['indikator'] ?? ''); ?></td>
                                            <td class="text-center"><?php echo e($kli['capaian'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Data Audit Internal Tahun Sebelumnya -->
                            <?php $thnPrevLabel = $auditPrev['tahun'] ?? $thnSebelumnya; ?>
                            <div class="sub-section-heading">Data Audit Internal SMKP <?php echo e($namaPerusahaan); ?> Tahun <?php echo e($thnPrevLabel); ?></div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Data Audit Internal <?php echo e($thnPrevLabel); ?></th>
                                        <th style="width: 240px;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>Capaian Nilai Audit</td>
                                        <td><?php echo e($auditPrev['nilai_audit'] ?? '79.50 %'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td>Status Penyelesaian Tindak Lanjut Audit untuk Ketidaksesuaian Kategori Kritikal</td>
                                        <td><?php echo e($auditPrev['status_kritikal'] ?? 'Tidak ada temuan kritikal'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">3</td>
                                        <td>Status Penyelesaian Tindak Lanjut Audit untuk Ketidaksesuaian Kategori Mayor</td>
                                        <td><?php echo e($auditPrev['status_mayor'] ?? 'Tidak ada temuan Mayor'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">4</td>
                                        <td>Status Penyelesaian Tindak Lanjut Audit untuk Ketidaksesuaian Kategori Minor</td>
                                        <td style="text-align: justify;"><?php echo e($auditPrev['status_minor'] ?? '98% temuan sudah ditindaklanjuti'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">5</td>
                                        <td>Catatan Hasil Evaluasi dari Instansi Pembina Sektor</td>
                                        <td>
                                            <?php if(!empty($auditPrev['catatan_instansi'])): ?>
                                                <ol class="mb-0 ps-3" style="text-align: left;">
                                                    <?php foreach($auditPrev['catatan_instansi'] as $ci): ?>
                                                        <li class="mb-1"><?php echo e($ci); ?></li>
                                                    <?php endforeach; ?>
                                                </ol>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Daftar Harapan dan Kebutuhan Pemangku Kepentingan -->
                            <div class="sub-section-heading">Daftar Harapan dan Kebutuhan Pemangku Kepentingan terkait Audit Internal SMKP berdasarkan Komunikasi dan Konsultasi Risiko yang dilakukan <?php echo e($namaPerusahaan); ?> Tahun <?php echo e($thnPeriode); ?></div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th>Masukan dari Pemangku Kepentingan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($harapanList as $idx => $hrp): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e($hrp); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <p class="narrative">
                                Selain mempertimbangkan bahwa hasil penilaian risiko pada kegiatan operasional dan hasil penilaian kinerja Keselamatan Pertambangan dari Auditi, penetapan metode dan sampel juga wajib memperhatikan <em>lesson learned</em> dari kinerja Keselamatan Pertambangan Nasional yang disampaikan oleh instansi pembina sektor.
                            </p>
                        </div>

                        <div class="sub-bab-title">C. Penyiapan Dokumen Kerja</div>
                        <div class="content-indent">
                            <p class="narrative">
                                Tim Audit Internal Sistem Manajemen Keselamatan Pertambangan menyiapkan dokumen kerja yang diperlukan untuk rujukan dan untuk merekam pelaksanaan audit internal.<br>
                                Dokumen kerja tersebut mencakup hal-hal sebagai berikut:
                            </p>
                            <ol class="formal-list" style="list-style-type: none; padding-left: 0;">
                                <li>1) &nbsp;Rencana <em>Sampling</em> yang dimuat pada Formulir Rencana Audit <?php echo e($namaPerusahaan); ?> nomor TT-MGT-FRS-026A Formulir Rencana Audit</li>
                                <li>2) &nbsp;Kriteria Audit yaitu Lampiran II Keputusan Direktur Jenderal Mineral dan Batubara Nomor 185.K/37.04/DJB/2019</li>
                                <li>3) &nbsp;Perangkat Penunjang Pengambilan Data sesuai kebutuhan audit seperti: Daftar Pertanyaan Wawancara Terstruktur, Daftar Periksa Observasi Objek Audit, Kuesioner Audit</li>
                                <li>4) &nbsp;Formulir Pendokumentasian Hasil Audit, antara lain:
                                    <ol type="a" class="mt-1 mb-1 ps-4">
                                        <li>Formulir Kesesuaian Audit (Formulir nomor TT-MGT-FRS-026E Formulir Kesesuaian)</li>
                                        <li>Formulir Ketidaksesuaian dan Tindak Lanjut Ketidaksesuaian Audit (Formulir nomor TT-MGT-FRS-026M Formulir Ketidaksesuaian dan Tindak Lanjut Ketidaksesuaian)</li>
                                        <li>Formulir Rekapitulasi Ketidaksesuaian (Formulir nomor TT-MGT-FRS-026F Formulir Rekapitulasi Ketidaksesuaian)</li>
                                        <li>Formulir Rencana Tindak Lanjut Audit (Formulir nomor TT-MGT-FRS-026H Rencana Tindak Lanjut Audit)</li>
                                        <li>Formulir Rekomendasi dan Peluang untuk Perbaikan (Formulir nomor TT-MGT-FRS-026G Formulir Rekomendasi dan OFI)</li>
                                    </ol>
                                </li>
                                <li>5) &nbsp;Formulir Rekaman Rapat nomor TT-MGT-FRS-011A Daftar Hadir</li>
                            </ol>
                        </div>

                        <!-- ==========================================
                             PENUTUP & PENGESAHAN
                             ========================================== -->
                        <div class="sub-bab-title" style="margin-left: 0; margin-top: 18px;">PENUTUP</div>
                        <div class="content-indent">
                            <p class="narrative">
                                Berita Acara Hasil Pelaksanaan Tahapan Awal Audit Internal Sistem Manajemen Keselamatan Pertambangan ini dibuat pada tanggal <?php echo e($tglBeritaAcaraFormatted); ?>, dan diketahui oleh klien audit, tim audit, serta auditor.
                            </p>

                            <table class="formal-table" style="margin-top: 14px;">
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="fw-normal py-2">Ketua Tim Audit</td>
                                    </tr>
                                    <tr style="height: 85px;">
                                        <td style="width: 35%; vertical-align: middle;"><?php echo e($tahapAwal->ketua_tim_audit ?: 'Fiska Polii'); ?></td>
                                        <td style="width: 35%; vertical-align: bottom;">
                                            <span class="fst-italic" style="font-size: 8pt;">(Tanda Tangan)</span>
                                        </td>
                                        <td style="width: 30%; vertical-align: middle;">
                                            <div><?php echo e($tglBeritaAcaraFormatted); ?></div>
                                            <div class="fst-italic" style="font-size: 8pt;">(Tanggal)</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fw-normal py-2">Kepala Teknik Tambang</td>
                                    </tr>
                                    <tr style="height: 85px;">
                                        <td style="vertical-align: middle;"><?php echo e($tahapAwal->nama_ktt ?: 'Tejo Prihantoro'); ?></td>
                                        <td style="vertical-align: bottom;">
                                            <span class="fst-italic" style="font-size: 8pt;">(Tanda Tangan)</span>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <div><?php echo e($tglBeritaAcaraFormatted); ?></div>
                                            <div class="fst-italic" style="font-size: 8pt;">(Tanggal)</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>

            <!-- REPEATING CONTROLLED DOCUMENT FOOTER -->
            <tfoot>
                <tr>
                    <td>
                        <table class="footer-doc-table">
                            <tr>
                                <td style="width: 18%;">Nama Dokumen</td>
                                <td colspan="3">Formulir Berita Acara Hasil Pelaksanaan Tahapan Awal Audit Internal SMKP</td>
                                <td style="width: 24%;"></td>
                            </tr>
                            <tr>
                                <td>Ditetapkan Oleh</td>
                                <td colspan="2">Kepala Teknik Tambang</td>
                                <td style="width: 24%;">Tanggal Terbit</td>
                                <td><?php echo e($tglTerbitFormatted); ?></td>
                            </tr>
                            <tr>
                                <td>No Dokumen</td>
                                <td colspan="2"><?php echo e($tahapAwal->no_dokumen_footer ?: 'TT-MGT-FRS-026K'); ?></td>
                                <td>Tanggal Tinjau Ulang</td>
                                <td><?php echo e($tglTinjauFormatted); ?></td>
                            </tr>
                            <tr>
                                <td>No Revisi</td>
                                <td style="width: 7%;"><?php echo e($tahapAwal->no_revisi ?: '01'); ?></td>
                                <td colspan="2" class="footer-red-warning">Dokumen terkendali dan valid hanya ada di sharepoint Archi Indonesia</td>
                                <td class="fw-bold">Dokumen Resmi SMKP</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('btnPrintDoc')?.addEventListener('click', function () {
                window.print();
            });
            document.getElementById('btnCloseDoc')?.addEventListener('click', function () {
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    window.close();
                }
            });
        });
    </script>
</body>
</html>

