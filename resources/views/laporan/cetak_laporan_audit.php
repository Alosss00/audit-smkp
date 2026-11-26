<?php
/**
 * Cetak Laporan Audit Internal SMKP Minerba (A4 Portrait)
 * - Sebelum disahkan: dirender dari data hidup dengan watermark DRAFT.
 * - Setelah disahkan: dirender sepenuhnya dari snapshot JSON + SHA-256.
 */
$fontFamily   = (string) config('laporan.font_family', 'Arial, Helvetica, sans-serif');
$sizeJudul    = (string) config('laporan.font_size_judul', '18pt');
$sizeSubjudul = (string) config('laporan.font_size_subjudul', '14pt');
$sizeIsi      = (string) config('laporan.font_size_isi', '12pt');
$marginPage   = (string) config('laporan.margin_halaman', '20mm 18mm');

$meta         = $payload['meta'] ?? [];
$perusahaan   = $payload['perusahaan'] ?? [];
$legalitas    = $perusahaan['legalitas'] ?? null;
$profil       = $payload['profil'] ?? null;
$peralatans   = $payload['peralatans'] ?? [];
$alatTotals   = $payload['peralatan_totals'] ?? ['per_kelompok' => [], 'grand_total' => 0];
$narasiElemen = $payload['narasi_elemens'] ?? [];
$lingkupAreas = $payload['lingkup_areas'] ?? [];
$timAudit     = $payload['tim_audit'] ?? [];
$dasarHukum   = $payload['dasar_hukum'] ?? [];
$narasiTpl    = $payload['narasi_template'] ?? [];
$pencapaian   = $payload['pencapaian'] ?? ['skor_akhir' => 0, 'skor_akhir_terbilang' => 'nol', 'rekap_elemen' => []];
$rekapElemen  = $pencapaian['rekap_elemen'] ?? [];
$praktikBaik  = $payload['praktik_terbaik'] ?? [];
$temuan       = $payload['temuan'] ?? ['kritikal_count' => 0, 'mayor_count' => 0, 'minor_count' => 0, 'total_temuan' => 0, 'kritikal' => [], 'mayor' => [], 'minor' => []];
$ofis         = $payload['ofis'] ?? [];

$namaPerusahaan = (string) ($perusahaan['nama_perusahaan'] ?? $sesi->area_audit ?? '-');
$tahunPeriode   = (string) ($meta['tahun_periode'] ?? $sesi->tahun_periode ?? date('Y'));
$nomorLaporan   = (string) ($meta['nomor_laporan'] ?: '-');
$tempatTerbit   = (string) ($meta['tempat_penerbitan'] ?: '-');
$tanggalLaporan = ! empty($meta['tanggal_laporan']) ? \Carbon\Carbon::parse($meta['tanggal_laporan'])->format('d/m/Y') : '-';
$tglMulai       = ! empty($meta['tanggal_mulai']) ? \Carbon\Carbon::parse($meta['tanggal_mulai'])->format('d/m/Y') : '-';
$tglSelesai     = ! empty($meta['tanggal_selesai']) ? \Carbon\Carbon::parse($meta['tanggal_selesai'])->format('d/m/Y') : '-';

$peranLabels = \App\Models\AuditRencana::PERAN_OPTIONS;
$isAuditorRole = auth()->check() && auth()->user()->role === 'auditor';
$routeKriteria = $isAuditorRole
    ? route('auditor.audit-sesi.cetak', $sesi->id)
    : route('admin.audit-sesi.cetak', $sesi->id);
$routeRtl = $isAuditorRole
    ? route('auditor.audit-sesi.cetak-ketidaksesuaian', $sesi->id)
    : route('admin.audit-sesi.cetak-ketidaksesuaian', $sesi->id);
$routeKesesuaian = $isAuditorRole
    ? route('auditor.audit-sesi.cetak-kesesuaian', $sesi->id)
    : route('admin.audit-sesi.cetak-kesesuaian', $sesi->id);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Audit Internal SMKP — <?php echo e($namaPerusahaan); ?> (<?php echo e($tahunPeriode); ?>)</title>
    <link href="<?php echo asset('vendor/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: <?php echo e($marginPage); ?>;
        }
        body {
            font-family: <?php echo e($fontFamily); ?>;
            font-size: <?php echo e($sizeIsi); ?>;
            line-height: 1.5;
            color: #111827;
            background: #e2e8f0;
            margin: 0;
        }
        .toolbar-print {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .sheet {
            position: relative;
            background: #ffffff;
            max-width: 210mm;
            margin: 18px auto;
            padding: 20mm 18mm;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
        }
        .watermark-draft {
            position: fixed;
            top: 42%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-32deg);
            font-size: 44pt;
            font-weight: 800;
            color: rgba(220, 38, 38, 0.12);
            letter-spacing: 4px;
            pointer-events: none;
            z-index: 10;
            white-space: nowrap;
        }
        .cover-page {
            min-height: 245mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            padding: 18mm 8mm;
            border: 2px solid #0f172a;
            page-break-after: always;
        }
        .cover-title {
            font-size: <?php echo e($sizeJudul); ?>;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.35;
            margin-bottom: 14px;
        }
        .cover-subtitle {
            font-size: <?php echo e($sizeSubjudul); ?>;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .bab-title {
            font-size: <?php echo e($sizeSubjudul); ?>;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 4px;
            margin: 22px 0 12px 0;
            page-break-after: avoid;
        }
        .subbab-title {
            font-size: <?php echo e($sizeIsi); ?>;
            font-weight: 700;
            margin: 14px 0 6px 0;
            page-break-after: avoid;
        }
        .body-text {
            text-align: justify;
            margin-bottom: 10px;
            white-space: pre-line;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 14px 0;
            font-size: 10pt;
        }
        .report-table th, .report-table td {
            border: 1px solid #334155;
            padding: 5px 7px;
            vertical-align: top;
        }
        .report-table th {
            background-color: #e2e8f0;
            font-weight: 700;
            text-align: center;
        }
        .radar-wrap {
            text-align: center;
            margin: 16px 0;
            page-break-inside: avoid;
        }
        .signature-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin-top: 24px;
            page-break-inside: avoid;
        }
        .signature-box {
            flex: 1 1 42%;
            border: 1px solid #cbd5e1;
            padding: 12px;
            text-align: center;
            min-height: 110px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 10.5pt;
        }
        .snapshot-footer {
            margin-top: 24px;
            padding-top: 8px;
            border-top: 1px dashed #64748b;
            font-size: 8.5pt;
            color: #475569;
            word-break: break-all;
        }
        .page-break {
            page-break-before: always;
        }
        @media print {
            body {
                background: #ffffff;
            }
            .toolbar-print {
                display: none !important;
            }
            .sheet {
                margin: 0;
                padding: 0;
                max-width: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <?php if (! $isFromSnapshot): ?>
        <div class="watermark-draft">DRAFT — BELUM DISAHKAN</div>
    <?php endif; ?>

    <!-- Toolbar Non-Print -->
    <div class="toolbar-print">
        <div class="small">
            <strong>Laporan Audit Internal SMKP</strong> &bull;
            <?php if ($isFromSnapshot): ?>
                <span class="badge bg-success">DISAHKAN (Snapshot Revisi #<?php echo e($snapshot->revisi_ke); ?>)</span>
            <?php else: ?>
                <span class="badge bg-warning text-dark">DRAFT — DATA HIDUP</span>
            <?php endif; ?>
        </div>
        <div class="d-flex gap-2">
            <button type="button" id="btnPrintLaporan" class="btn btn-sm btn-primary fw-semibold">Cetak / Simpan PDF</button>
            <button type="button" id="btnCloseWindow" class="btn btn-sm btn-outline-light">Tutup</button>
        </div>
    </div>

    <div class="sheet">
        <!-- SAMPUL & HALAMAN JUDUL -->
        <div class="cover-page">
            <div>
                <div class="cover-title">LAPORAN AUDIT INTERNAL<br>SISTEM MANAJEMEN KESELAMATAN PERTAMBANGAN<br>(SMKP MINERBA)</div>
                <div class="small text-muted">Nomor Laporan: <?php echo e($nomorLaporan); ?></div>
            </div>

            <div>
                <div class="cover-subtitle"><?php echo e(strtoupper($namaPerusahaan)); ?></div>
                <?php if (! empty($perusahaan['alamat_kantor_site']) || ! empty($perusahaan['alamat_lokasi_tambang'])): ?>
                    <div class="small mt-2">
                        <?php echo e($perusahaan['alamat_kantor_site'] ?: $perusahaan['alamat_lokasi_tambang']); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <div class="cover-subtitle">PERIODE TAHUN <?php echo e($tahunPeriode); ?></div>
                <div class="small">Pelaksanaan Audit: <?php echo e($tglMulai); ?> s/d <?php echo e($tglSelesai); ?></div>
                <div class="small mt-1">Diterbitkan di <?php echo e($tempatTerbit); ?>, <?php echo e($tanggalLaporan); ?></div>
            </div>
        </div>

        <!-- BAB 1: LATAR BELAKANG -->
        <div class="bab-title">1. LATAR BELAKANG</div>
        <div class="body-text"><?php echo e($narasiTpl['latar_belakang'] ?? ''); ?></div>

        <?php if (! empty($narasiTpl['tujuan_audit'])): ?>
            <div class="subbab-title">1.1 Tujuan Audit</div>
            <div class="body-text"><?php echo e($narasiTpl['tujuan_audit']); ?></div>
        <?php endif; ?>

        <?php if (! empty($dasarHukum)): ?>
            <div class="subbab-title">1.2 Dasar Hukum Acuan</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 38px;">No</th>
                        <th style="width: 34%;">Nomor Peraturan</th>
                        <th>Judul / Ringkasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dasarHukum as $idx => $dh): ?>
                        <tr>
                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                            <td><?php echo e($dh['nomor_peraturan'] ?? '-'); ?></td>
                            <td>
                                <strong><?php echo e($dh['judul'] ?? '-'); ?></strong>
                                <?php if (! empty($dh['ringkasan'])): ?>
                                    <div class="small text-muted"><?php echo e($dh['ringkasan']); ?></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- BAB 2: GAMBARAN UMUM PERUSAHAAN -->
        <div class="bab-title">2. GAMBARAN UMUM PERUSAHAAN</div>

        <div class="subbab-title">2.1 Domisili dan Legalitas Perusahaan</div>
        <table class="report-table">
            <tbody>
                <tr><th style="width: 34%; text-align: left;">Nama Perusahaan</th><td><?php echo e($namaPerusahaan); ?></td></tr>
                <tr><th style="text-align: left;">Kategori / Komoditas</th><td><?php echo e(($perusahaan['kategori'] ?? '-') . ' / ' . ($perusahaan['jenis_komoditas'] ?? '-')); ?></td></tr>
                <tr><th style="text-align: left;">KTT / PTL / PJO</th><td><?php echo e($perusahaan['nama_ktt_default'] ?? '-'); ?></td></tr>
                <tr><th style="text-align: left;">Alamat Kantor Pusat</th><td><?php echo e($perusahaan['alamat_kantor_pusat'] ?? '-'); ?></td></tr>
                <tr><th style="text-align: left;">Alamat Kantor Site</th><td><?php echo e($perusahaan['alamat_kantor_site'] ?? '-'); ?></td></tr>
                <tr><th style="text-align: left;">Alamat Lokasi Tambang</th><td><?php echo e($perusahaan['alamat_lokasi_tambang'] ?? '-'); ?></td></tr>
                <?php if ($legalitas): ?>
                    <tr><th style="text-align: left;">Jenis &amp; Nomor SK Izin</th><td><?php echo e(($legalitas['jenis_izin_label'] ?? '-') . ' — ' . ($legalitas['nomor_sk_izin'] ?? '-')); ?></td></tr>
                    <tr><th style="text-align: left;">Status &amp; Jangka Waktu Izin</th><td><?php echo e(($legalitas['status_izin'] ?? '-') . ' (' . ($legalitas['tanggal_mulai_berlaku'] ?? '-') . ' s/d ' . ($legalitas['tanggal_berakhir'] ?? '-') . ')'); ?></td></tr>
                    <tr><th style="text-align: left;">Kode WIUP &amp; Luas Wilayah</th><td><?php echo e(($legalitas['kode_wiup'] ?? '-') . ($legalitas['luas_ha'] !== null ? ' / ' . number_format((float) $legalitas['luas_ha'], 2, ',', '.') . ' Ha' : '')); ?></td></tr>
                    <tr>
                        <th style="text-align: left;">Wilayah Administratif</th>
                        <td><?php echo e(implode(', ', array_filter([$legalitas['lokasi_desa'] ?? null, $legalitas['lokasi_kecamatan'] ?? null, $legalitas['lokasi_kabupaten'] ?? null, $legalitas['lokasi_provinsi'] ?? null])) ?: '-'); ?></td>
                    </tr>
                    <tr><th style="text-align: left;">Persetujuan Studi Kelayakan</th><td><?php echo e(($legalitas['nomor_persetujuan_studi_kelayakan'] ?? '-') . (! empty($legalitas['tanggal_persetujuan_studi_kelayakan']) ? ' (' . $legalitas['tanggal_persetujuan_studi_kelayakan'] . ')' : '')); ?></td></tr>
                    <tr><th style="text-align: left;">Persetujuan Dok. Lingkungan</th><td><?php echo e(($legalitas['nomor_persetujuan_dokumen_lingkungan'] ?? '-') . (! empty($legalitas['tanggal_persetujuan_dokumen_lingkungan']) ? ' (' . $legalitas['tanggal_persetujuan_dokumen_lingkungan'] . ')' : '')); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="subbab-title">2.2 Kegiatan, Produksi, Tenaga Kerja, Peralatan, dan Struktur Organisasi</div>
        <?php if (! empty($profil['narasi_kegiatan'])): ?>
            <div class="body-text"><?php echo e($profil['narasi_kegiatan']); ?></div>
        <?php endif; ?>
        <table class="report-table">
            <tbody>
                <tr>
                    <th style="width: 34%; text-align: left;">Kapasitas / Realisasi Produksi</th>
                    <td>
                        Kapasitas: <?php echo e($profil && $profil['kapasitas_produksi'] !== null ? number_format((float) $profil['kapasitas_produksi'], 2, ',', '.') : '-'); ?>
                        <?php echo e($profil['satuan_produksi'] ?? ''); ?>
                        &nbsp;|&nbsp;
                        Realisasi: <?php echo e($profil && $profil['realisasi_produksi'] !== null ? number_format((float) $profil['realisasi_produksi'], 2, ',', '.') : '-'); ?>
                        <?php echo e($profil['satuan_produksi'] ?? ''); ?>
                    </td>
                </tr>
                <tr>
                    <th style="text-align: left;">Jumlah Tenaga Kerja</th>
                    <td>
                        Total: <?php echo e($profil && $profil['jumlah_pekerja'] !== null ? number_format((int) $profil['jumlah_pekerja'], 0, ',', '.') . ' orang' : '-'); ?>
                        (TKA: <?php echo e($profil && $profil['jumlah_pekerja_asing'] !== null ? number_format((int) $profil['jumlah_pekerja_asing'], 0, ',', '.') . ' orang' : '0 orang'); ?>)
                    </td>
                </tr>
                <tr>
                    <th style="text-align: left;">Struktur Organisasi</th>
                    <td>
                        <?php if (! empty($profil['struktur_organisasi_path']) && ! empty($profil['id'])): ?>
                            Terlampir (<?php echo e(basename((string) $profil['struktur_organisasi_path'])); ?>)
                        <?php else: ?>
                            Belum dilampirkan.
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php if (! empty($peralatans)): ?>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 38px;">No</th>
                        <th>Kelompok</th>
                        <th>Jenis</th>
                        <th>Tipe / Model</th>
                        <th>Kapasitas</th>
                        <th style="width: 75px;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($peralatans as $idx => $alat): ?>
                        <tr>
                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                            <td><?php echo e($alat['kelompok'] ?? '-'); ?></td>
                            <td><?php echo e($alat['jenis'] ?? '-'); ?></td>
                            <td><?php echo e($alat['tipe'] ?? '-'); ?></td>
                            <td><?php echo e($alat['kapasitas'] ?? '-'); ?></td>
                            <td class="text-end"><?php echo e((int) ($alat['jumlah'] ?? 0)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align: right;">Grand Total Peralatan</th>
                        <th style="text-align: right;"><?php echo e((int) ($alatTotals['grand_total'] ?? 0)); ?></th>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>

        <div class="subbab-title">2.3 Gambaran Penerapan SMKP Minerba</div>
        <?php if (! empty($profil['narasi_penerapan_smkp'])): ?>
            <div class="body-text"><?php echo e($profil['narasi_penerapan_smkp']); ?></div>
        <?php endif; ?>
        <?php if (! empty($narasiElemen)): ?>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 28%;">Elemen SMKP</th>
                        <th>Uraian Penerapan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($narasiElemen as $ne): ?>
                        <?php if (! empty($ne['narasi'])): ?>
                            <tr>
                                <td><strong>Elemen <?php echo e($ne['kode_elemen'] ?? '-'); ?></strong><br><?php echo e($ne['nama_elemen'] ?? ''); ?></td>
                                <td class="body-text"><?php echo e($ne['narasi']); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- BAB 3: LINGKUP AUDIT -->
        <div class="bab-title">3. LINGKUP AUDIT</div>
        <?php if (! empty($payload['ruang_lingkup_teks'])): ?>
            <div class="body-text"><?php echo e($payload['ruang_lingkup_teks']); ?></div>
        <?php endif; ?>
        <?php if (! empty($lingkupAreas)): ?>
            <ol class="small">
                <?php foreach ($lingkupAreas as $area): ?>
                    <li><?php echo e($area); ?></li>
                <?php endforeach; ?>
            </ol>
        <?php else: ?>
            <p class="small text-muted">Daftar area lingkup audit terstruktur belum dicatat pada Rencana Audit.</p>
        <?php endif; ?>

        <!-- BAB 4: PELAKSANAAN AUDIT DAN TIM AUDITOR -->
        <div class="bab-title">4. PELAKSANAAN AUDIT DAN TIM AUDITOR</div>
        <p class="body-text">Audit Internal SMKP dilaksanakan mulai tanggal <?php echo e($tglMulai); ?> sampai dengan <?php echo e($tglSelesai); ?> oleh Tim Audit sebagai berikut:</p>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 38px;">No</th>
                    <th>Nama</th>
                    <th>Jabatan / Registrasi</th>
                    <th style="width: 26%;">Peran dalam Tim</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($timAudit)): ?>
                    <?php foreach ($timAudit as $idx => $t): ?>
                        <tr>
                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                            <td><?php echo e($t['nama'] ?? '-'); ?></td>
                            <td><?php echo e($t['jabatan'] ?? '-'); ?></td>
                            <td><?php echo e($peranLabels[$t['peran'] ?? ''] ?? ($t['peran'] ?? '-')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted">Tim audit belum dicatat pada Rencana Audit.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- BAB 5: RINGKASAN LAPORAN DAN PENILAIAN AUDIT -->
        <div class="bab-title page-break">5. RINGKASAN LAPORAN DAN PENILAIAN AUDIT</div>

        <div class="subbab-title">5.1.1 Praktik Terbaik (Kesesuaian Penuh 100%)</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 38px;">No</th>
                    <th style="width: 22%;">Elemen / Sub-Elemen</th>
                    <th>Uraian Kesesuaian</th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($praktikBaik)): ?>
                    <?php foreach ($praktikBaik as $idx => $pb): ?>
                        <tr>
                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                            <td><strong><?php echo e($pb['kode_sub'] ?? '-'); ?></strong><br><?php echo e($pb['nama_sub'] ?? '-'); ?></td>
                            <td><?php echo e($pb['keterangan'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center text-muted">Belum terdapat sub-elemen dengan kesesuaian penuh 100%.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="subbab-title">5.1.2 Temuan Ketidaksesuaian per Kategori</div>
        <p class="body-text">
            Total temuan ketidaksesuaian tercatat sebanyak <strong><?php echo e((int) ($temuan['total_temuan'] ?? 0)); ?></strong> sub-elemen,
            terdiri dari <strong><?php echo e((int) ($temuan['kritikal_count'] ?? 0)); ?></strong> Kritikal,
            <strong><?php echo e((int) ($temuan['mayor_count'] ?? 0)); ?></strong> Mayor, dan
            <strong><?php echo e((int) ($temuan['minor_count'] ?? 0)); ?></strong> Minor.
        </p>

        <?php
        $kategoriSections = [
            'kritikal' => 'Kategori Kritikal',
            'mayor'    => 'Kategori Mayor',
            'minor'    => 'Kategori Minor',
        ];
        ?>
        <?php foreach ($kategoriSections as $katKey => $katTitle): ?>
            <?php $katItems = $temuan[$katKey] ?? []; ?>
            <div class="small fw-bold mt-2 mb-1"><?php echo e($katTitle); ?> (<?php echo e(count($katItems)); ?> item)</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 115px;">Kode Temuan</th>
                        <th style="width: 14%;">Kriteria</th>
                        <th>Uraian Ketidaksesuaian (PLOR)</th>
                        <th style="width: 16%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($katItems)): ?>
                        <?php foreach ($katItems as $it): ?>
                            <tr>
                                <td class="text-center font-monospace fw-bold"><?php echo e($it['kode_temuan'] ?? '-'); ?></td>
                                <td class="text-center"><?php echo e($it['kriteria_kode'] ?? $it['sub_elemen_kode'] ?? '-'); ?></td>
                                <td>
                                    <?php if (! empty($it['plor_problem']) || ! empty($it['plor_location']) || ! empty($it['plor_objective_evidence']) || ! empty($it['plor_reference'])): ?>
                                        <div><strong>P:</strong> <?php echo e($it['plor_problem'] ?: '-'); ?></div>
                                        <div><strong>L:</strong> <?php echo e($it['plor_location'] ?: '-'); ?></div>
                                        <div><strong>O:</strong> <?php echo e($it['plor_objective_evidence'] ?: '-'); ?></div>
                                        <div><strong>R:</strong> <?php echo e($it['plor_reference'] ?: '-'); ?></div>
                                    <?php else: ?>
                                        <?php echo e($it['deskripsi_temuan'] ?? '-'); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?php echo e(strtoupper((string) ($it['status'] ?? 'open'))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted">Nihil (tidak ada temuan <?php echo e(strtolower($katTitle)); ?>).</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <div class="subbab-title">5.2 Tingkat Pencapaian Penerapan SMKP Minerba</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 65px;">Elemen</th>
                    <th>Nama Elemen</th>
                    <th style="width: 65px;">Bobot (%)</th>
                    <th style="width: 75px;">Nilai Aktual</th>
                    <th style="width: 75px;">Nilai Maks</th>
                    <th style="width: 85px;">Pencapaian (%)</th>
                    <th style="width: 85px;">Nilai Elemen (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sumBobot = 0.0;
                $sumAktual = 0;
                $sumMaks = 0;
                $sumSkor = 0.0;
                ?>
                <?php foreach ($rekapElemen as $row): ?>
                    <?php
                    $sumBobot  += (float) ($row['bobot'] ?? 0);
                    $sumAktual += (int) ($row['total_nilai_aktual'] ?? 0);
                    $sumMaks   += (int) ($row['total_nilai_maks_efektif'] ?? 0);
                    $sumSkor   += (float) ($row['skor_elemen'] ?? 0);
                    ?>
                    <tr>
                        <td class="text-center fw-bold"><?php echo e($row['kode_elemen'] ?? '-'); ?></td>
                        <td><?php echo e($row['nama_elemen'] ?? '-'); ?></td>
                        <td class="text-end"><?php echo e(number_format((float) ($row['bobot'] ?? 0), 2, ',', '.')); ?></td>
                        <td class="text-end"><?php echo e((int) ($row['total_nilai_aktual'] ?? 0)); ?></td>
                        <td class="text-end"><?php echo e((int) ($row['total_nilai_maks_efektif'] ?? 0)); ?></td>
                        <td class="text-end"><?php echo e(number_format((float) ($row['persentase'] ?? 0), 2, ',', '.')); ?>%</td>
                        <td class="text-end fw-bold"><?php echo e(number_format((float) ($row['skor_elemen'] ?? 0), 2, ',', '.')); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" style="text-align: right;">TOTAL TINGKAT PENCAPAIAN</th>
                    <th style="text-align: right;"><?php echo e(number_format($sumBobot, 2, ',', '.')); ?></th>
                    <th style="text-align: right;"><?php echo e($sumAktual); ?></th>
                    <th style="text-align: right;"><?php echo e($sumMaks); ?></th>
                    <th style="text-align: center;">-</th>
                    <th style="text-align: right;"><?php echo e(number_format((float) ($pencapaian['skor_akhir'] ?? $sumSkor), 2, ',', '.')); ?>%</th>
                </tr>
            </tfoot>
        </table>
        <p class="body-text">
            Berdasarkan hasil perhitungan otomatis dari seluruh elemen penilaian SMKP Minerba, tingkat pencapaian total adalah sebesar
            <strong><?php echo e(number_format((float) ($pencapaian['skor_akhir'] ?? $sumSkor), 2, ',', '.')); ?>%</strong>
            (<em><?php echo e($pencapaian['skor_akhir_terbilang'] ?? \App\Support\Terbilang::decimal((float) ($pencapaian['skor_akhir'] ?? $sumSkor))); ?> persen</em>).
        </p>

        <div class="radar-wrap">
            <?php echo $radarChartSvg; ?>
            <div class="small text-muted mt-1">Gambar 1. Grafik Radar Tingkat Pencapaian Penerapan SMKP per Elemen</div>
        </div>

        <!-- BAB 6: PENUTUP & TANDA TANGAN TIM AUDITOR -->
        <div class="bab-title">6. PENUTUP</div>
        <div class="body-text"><?php echo e($narasiTpl['penutup'] ?? ''); ?></div>

        <div class="mt-3 small">
            Diterbitkan di <strong><?php echo e($tempatTerbit); ?></strong> pada tanggal <strong><?php echo e($tanggalLaporan); ?></strong>.
        </div>

        <div class="signature-grid">
            <?php if (! empty($timAudit)): ?>
                <?php foreach ($timAudit as $t): ?>
                    <div class="signature-box">
                        <div class="fw-bold"><?php echo e($peranLabels[$t['peran'] ?? ''] ?? ($t['peran'] ?? 'Auditor')); ?></div>
                        <div style="height: 46px;"></div>
                        <div>
                            <div class="fw-bold text-decoration-underline"><?php echo e($t['nama'] ?? '-'); ?></div>
                            <div class="small text-muted"><?php echo e($t['jabatan'] ?? ''); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="signature-box">
                    <div class="fw-bold">Ketua Tim (Lead Auditor)</div>
                    <div style="height: 46px;"></div>
                    <div class="fw-bold text-decoration-underline"><?php echo e($meta['penanggung_jawab_nama'] ?? '-'); ?></div>
                </div>
            <?php endif; ?>
        </div>

        <!-- DAFTAR LAMPIRAN A–F -->
        <?php
        $lampiran = $payload['lampiran'] ?? [];
        $dhP = $lampiran['daftar_hadir_pembukaan'] ?? null;
        $dhT = $lampiran['daftar_hadir_penutupan'] ?? null;
        $rm  = $lampiran['respon_manajemen'] ?? null;
        $iujpEligible = (bool) ($lampiran['iujp_eligible'] ?? false);
        $iujpList = $lampiran['iujps'] ?? [];
        $routeDhPembukaan = $isAuditorRole
            ? route('auditor.audit-sesi.laporan.daftar-hadir.cetak', [$sesi->id, 'pembukaan'])
            : route('admin.audit-sesi.laporan.daftar-hadir.cetak', [$sesi->id, 'pembukaan']);
        $routeDhPenutupan = $isAuditorRole
            ? route('auditor.audit-sesi.laporan.daftar-hadir.cetak', [$sesi->id, 'penutupan'])
            : route('admin.audit-sesi.laporan.daftar-hadir.cetak', [$sesi->id, 'penutupan']);
        ?>
        <div class="bab-title">LAMPIRAN (A–<?php echo $iujpEligible ? 'F' : 'E'; ?>)</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 95px;">Lampiran</th>
                    <th>Nama Dokumen Lampiran</th>
                    <th>Ringkasan / Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center fw-bold">Lampiran A</td>
                    <td>Formulir Kriteria Audit SMKP &amp; Formulir Kesesuaian</td>
                    <td>
                        Tersedia &nbsp;
                        <a href="<?php echo e($routeKriteria); ?>" target="_blank" class="d-print-none">[Kriteria]</a>
                        <a href="<?php echo e($routeKesesuaian); ?>" target="_blank" class="d-print-none">[Kesesuaian]</a>
                    </td>
                </tr>
                <tr>
                    <td class="text-center fw-bold">Lampiran B</td>
                    <td>Rencana Tindak Lanjut (RTL) &amp; Rekapitulasi Ketidaksesuaian</td>
                    <td>
                        <?php echo e((int) ($temuan['total_temuan'] ?? 0)); ?> sub-elemen temuan &nbsp;
                        <a href="<?php echo e($routeRtl); ?>" target="_blank" class="d-print-none">[Buka RTL]</a>
                    </td>
                </tr>
                <tr>
                    <td class="text-center fw-bold">Lampiran C</td>
                    <td>Daftar Hadir Pembukaan Audit Internal SMKP</td>
                    <td>
                        <?php if ($dhP): ?>
                            <?php echo e(($dhP['hari'] ?? '-') . ', ' . ($dhP['tanggal'] ?? '-') . ' (' . (int) ($dhP['jumlah_peserta'] ?? 0) . ' peserta)'); ?>
                        <?php else: ?>
                            Belum diisi
                        <?php endif; ?>
                        &nbsp;<a href="<?php echo e($routeDhPembukaan); ?>" target="_blank" class="d-print-none">[Cetak]</a>
                    </td>
                </tr>
                <tr>
                    <td class="text-center fw-bold">Lampiran D</td>
                    <td>Daftar Hadir Penutupan Audit Internal SMKP</td>
                    <td>
                        <?php if ($dhT): ?>
                            <?php echo e(($dhT['hari'] ?? '-') . ', ' . ($dhT['tanggal'] ?? '-') . ' (' . (int) ($dhT['jumlah_peserta'] ?? 0) . ' peserta)'); ?>
                        <?php else: ?>
                            Belum diisi
                        <?php endif; ?>
                        &nbsp;<a href="<?php echo e($routeDhPenutupan); ?>" target="_blank" class="d-print-none">[Cetak]</a>
                    </td>
                </tr>
                <tr>
                    <td class="text-center fw-bold">Lampiran E</td>
                    <td>Respon Manajemen terhadap Hasil Audit</td>
                    <td>
                        <?php if ($rm && ! empty($rm['isi_respon'])): ?>
                            <div><strong><?php echo e(($rm['nama_penandatangan'] ?? '-') . ' (' . ($rm['tanggal_respon'] ?? '-') . ')'); ?>:</strong></div>
                            <div><?php echo e($rm['isi_respon']); ?></div>
                        <?php else: ?>
                            Belum diisi
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ($iujpEligible): ?>
                    <tr>
                        <td class="text-center fw-bold">Lampiran F</td>
                        <td>Hasil Audit Internal Perusahaan Jasa Pertambangan (IUJP)</td>
                        <td>
                            <?php if (! empty($iujpList)): ?>
                                <?php foreach ($iujpList as $iu): ?>
                                    <div>
                                        <strong><?php echo e($iu['nama_perusahaan_iujp'] ?? '-'); ?></strong>
                                        (Skor: <?php echo $iu['skor_pencapaian'] !== null ? e(number_format((float) $iu['skor_pencapaian'], 2, ',', '.')) . '%' : '-'; ?>,
                                        Temuan K/My/Mn: <?php echo e(($iu['jumlah_temuan_kritikal'] ?? 0) . '/' . ($iu['jumlah_temuan_mayor'] ?? 0) . '/' . ($iu['jumlah_temuan_minor'] ?? 0)); ?>)
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                Belum ada hasil audit IUJP yang dilampirkan.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($isFromSnapshot && $snapshot): ?>
            <div class="snapshot-footer">
                <strong>Dokumen Terkunci (Snapshot Revisi #<?php echo e($snapshot->revisi_ke); ?>)</strong> &bull;
                Waktu Pengesahan: <?php echo e($snapshot->created_at ? $snapshot->created_at->format('d/m/Y H:i:s') : '-'); ?> &bull;
                Status Integritas: <?php echo ($integrity['valid'] ?? false) ? 'VALID' : 'TIDAK COCOK'; ?><br>
                SHA-256: <?php echo e($snapshot->hash_sha256); ?>
            </div>
        <?php endif; ?>
    </div>

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('btnPrintLaporan')?.addEventListener('click', function () {
            window.print();
        });
        document.getElementById('btnCloseWindow')?.addEventListener('click', function () {
            window.close();
        });
    });
    </script>
</body>
</html>
