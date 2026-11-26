<?php
/**
 * Cetak Formulir Rencana Audit Internal SMKP (TT-MGT-FRS-026A) — Orientasi A4 Landscape
 */
$namaPerusahaan = $sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit;
$noDokumen = $rencana->no_dokumen ?: 'TT-MGT-FRS-026A';
$thnPeriode = $sesi->tahun_periode ?? ($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y') : date('Y'));
$dokumenRelevan = is_array($rencana->dokumen_relevan) ? $rencana->dokumen_relevan : [];
$sampelTerisi = $rencana->sampels->filter(function ($s) {
    return !empty($s->metode) || !empty($s->teknik_sampling) || !empty($s->deskripsi_sampel) || !empty($s->jumlah_sampel);
});
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rencana Audit <?php echo e($namaPerusahaan); ?> - <?php echo e($noDokumen); ?></title>
    <link href="<?php echo asset('vendor/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 14mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #111827;
            background: #f1f5f9;
        }
        .sheet {
            background: #ffffff;
            max-width: 297mm;
            margin: 16px auto;
            padding: 14mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .formal-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .formal-table th, .formal-table td {
            border: 1px solid #334155;
            padding: 5px 7px;
            vertical-align: top;
        }
        .formal-table th {
            background-color: #e2e8f0;
            font-weight: 700;
            text-align: center;
        }
        .section-heading {
            font-weight: 700;
            font-size: 10pt;
            margin: 14px 0 6px 0;
            padding-bottom: 3px;
            border-bottom: 2px solid #0f172a;
        }
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1.5px solid #0f172a;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .no-print {
            max-width: 297mm;
            margin: 14px auto 0 auto;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        @media print {
            body { background: #ffffff; }
            .sheet { margin: 0; padding: 0; box-shadow: none; max-width: none; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" class="btn btn-dark btn-sm px-3" id="btnPrintRencana">Cetak / Simpan PDF</button>
    </div>

    <div class="sheet">
        <div class="doc-header">
            <div>
                <div style="font-size: 11pt; font-weight: 800; letter-spacing: 0.4px;">FORMULIR RENCANA AUDIT INTERNAL SMKP MINERBA</div>
                <div style="font-size: 9pt; color: #334155;"><?php echo e($namaPerusahaan); ?> &mdash; Periode Audit Tahun <?php echo e($thnPeriode); ?></div>
            </div>
            <div class="text-end">
                <div style="font-weight: 700; font-family: monospace;"><?php echo e($noDokumen); ?></div>
                <div style="font-size: 8pt;">Status: <strong><?php echo e(strtoupper($rencana->status)); ?></strong></div>
            </div>
        </div>

        <!-- I. INFORMASI UMUM -->
        <div class="section-heading">I. INFORMASI UMUM RENCANA AUDIT</div>
        <table class="formal-table">
            <tbody>
                <tr>
                    <td style="width: 22%; font-weight: 700;">Perusahaan Auditi</td>
                    <td style="width: 28%;"><?php echo e($namaPerusahaan); ?></td>
                    <td style="width: 20%; font-weight: 700;">Jenis Audit</td>
                    <td style="width: 30%;"><?php echo e($rencana->jenis_audit ?: '-'); ?></td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Tanggal Pelaksanaan</td>
                    <td><?php echo e($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('d/m/Y') : '-'); ?> s/d <?php echo e($sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('d/m/Y') : '-'); ?></td>
                    <td style="font-weight: 700;">Nomor Dokumen</td>
                    <td><?php echo e($noDokumen); ?></td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Kriteria Audit</td>
                    <td colspan="3"><?php echo nl2br(e($rencana->kriteria_audit ?: '-')); ?></td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Tujuan Audit</td>
                    <td colspan="3"><?php echo nl2br(e($rencana->tujuan ?: '-')); ?></td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Ruang Lingkup Audit</td>
                    <td colspan="3"><?php echo nl2br(e($rencana->ruang_lingkup ?: '-')); ?></td>
                </tr>
                <?php if (!empty($rencana->pengecualian)): ?>
                <tr>
                    <td style="font-weight: 700;">Pengecualian</td>
                    <td colspan="3"><?php echo nl2br(e($rencana->pengecualian)); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="font-weight: 700;">Dokumen Relevan</td>
                    <td colspan="3">
                        <?php if (!empty($dokumenRelevan)): ?>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($dokumenRelevan as $dok): ?>
                                    <li><?php echo e($dok); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Fasilitas Pendukung</td>
                    <td><?php echo nl2br(e($rencana->fasilitas ?: '-')); ?></td>
                    <td style="font-weight: 700;">Distribusi Laporan</td>
                    <td><?php echo nl2br(e($rencana->distribusi_laporan ?: '-')); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- II. TIM AUDIT -->
        <div class="section-heading">II. SUSUNAN TIM AUDIT</div>
        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Nama</th>
                    <th>Jabatan / Nomor Registrasi</th>
                    <th style="width: 200px;">Peran</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rencana->tims->isEmpty()): ?>
                    <tr><td colspan="4" class="text-center text-muted">Belum ada anggota tim terdaftar.</td></tr>
                <?php else: ?>
                    <?php foreach ($rencana->tims as $idx => $t): ?>
                        <tr>
                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                            <td><?php echo e($t->nama); ?></td>
                            <td><?php echo e($t->jabatan ?: '-'); ?></td>
                            <td class="text-center"><?php echo e(\App\Models\AuditRencana::PERAN_OPTIONS[$t->peran] ?? $t->peran); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- III. JADWAL PELAKSANAAN -->
        <div class="section-heading">III. JADWAL PELAKSANAAN AUDIT</div>
        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 55px;">Hari Ke</th>
                    <th style="width: 85px;">Tanggal</th>
                    <th style="width: 95px;">Waktu</th>
                    <th style="width: 120px;">Jenis Kegiatan</th>
                    <th>Kegiatan</th>
                    <th style="width: 110px;">Elemen</th>
                    <th style="width: 110px;">Auditor</th>
                    <th style="width: 110px;">PIC Auditi</th>
                    <th style="width: 100px;">Lokasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rencana->jadwals->isEmpty()): ?>
                    <tr><td colspan="9" class="text-center text-muted">Belum ada jadwal audit.</td></tr>
                <?php else: ?>
                    <?php foreach ($rencana->jadwals as $j): ?>
                        <tr>
                            <td class="text-center"><?php echo e($j->hari_ke); ?></td>
                            <td class="text-center"><?php echo e($j->tanggal ? $j->tanggal->format('d/m/Y') : '-'); ?></td>
                            <td class="text-center"><?php echo e(substr((string) $j->jam_mulai, 0, 5)); ?> - <?php echo e(substr((string) $j->jam_selesai, 0, 5)); ?></td>
                            <td><?php echo e(\App\Models\AuditRencana::JENIS_KEGIATAN_OPTIONS[$j->jenis_kegiatan] ?? $j->jenis_kegiatan); ?></td>
                            <td><?php echo e($j->kegiatan); ?></td>
                            <td><?php echo e($j->elemen ? $j->elemen->kode_elemen : '-'); ?></td>
                            <td><?php echo e($j->auditor ?: '-'); ?></td>
                            <td><?php echo e($j->auditi_pic ?: '-'); ?></td>
                            <td><?php echo e($j->lokasi ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- IV. MATRIKS METODE & SAMPEL -->
        <div class="section-heading page-break">IV. MATRIKS METODE DAN SAMPEL AUDIT</div>
        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 160px;">Sub-Elemen</th>
                    <th style="width: 70px;">Jenis Data</th>
                    <th style="width: 135px;">Metode</th>
                    <th style="width: 120px;">Teknik Sampling</th>
                    <th style="width: 110px;">Penentuan</th>
                    <th style="width: 60px;">N / e</th>
                    <th style="width: 65px;">Jml Sampel</th>
                    <th>Deskripsi Sampel</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($sampelTerisi->isEmpty()): ?>
                    <tr><td colspan="8" class="text-center text-muted">Belum ada rencana sampel yang diisi.</td></tr>
                <?php else: ?>
                    <?php foreach ($sampelTerisi as $s): ?>
                        <?php
                            $metodeLabels = [];
                            foreach ((array) ($s->metode ?? []) as $mk) {
                                $metodeLabels[] = \App\Models\AuditRencana::METODE_OPTIONS[$mk] ?? $mk;
                            }
                        ?>
                        <tr>
                            <td><strong><?php echo e($s->subElemen->kode_sub ?? '-'); ?></strong> <?php echo e($s->subElemen->nama_sub ?? ''); ?></td>
                            <td class="text-center"><?php echo e($s->jenis_data ? ucfirst($s->jenis_data) : '-'); ?></td>
                            <td><?php echo e(!empty($metodeLabels) ? implode(', ', $metodeLabels) : '-'); ?></td>
                            <td><?php echo e($s->teknik_sampling ? (\App\Models\AuditRencana::TEKNIK_SAMPLING_OPTIONS[$s->teknik_sampling] ?? $s->teknik_sampling) : '-'); ?></td>
                            <td><?php echo e($s->metode_penentuan ? (\App\Models\AuditRencana::METODE_PENENTUAN_OPTIONS[$s->metode_penentuan] ?? $s->metode_penentuan) : '-'); ?></td>
                            <td class="text-center">
                                <?php if ($s->populasi_n !== null): ?>
                                    N=<?php echo e($s->populasi_n); ?><?php echo $s->margin_error_e !== null ? '<br>e=' . e($s->margin_error_e) : ''; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-center fw-bold"><?php echo e($s->jumlah_sampel ?? '-'); ?></td>
                            <td><?php echo nl2br(e($s->deskripsi_sampel ?: '-')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- V. PENGESAHAN -->
        <div class="section-heading">V. PENGESAHAN RENCANA AUDIT</div>
        <table class="formal-table">
            <thead>
                <tr>
                    <?php foreach ($rencana->pengesahans as $p): ?>
                        <th><?php echo e($p->jabatan); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr style="height: 85px;">
                    <?php foreach ($rencana->pengesahans as $p): ?>
                        <td class="text-center" style="vertical-align: bottom;">
                            <div style="font-weight: 700;"><?php echo e($p->nama ?: '____________________'); ?></div>
                            <div style="font-size: 8pt; color: #475569;">Tanggal: <?php echo e($p->tanggal_ttd ? $p->tanggal_ttd->format('d/m/Y') : '..............'); ?></div>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        document.getElementById('btnPrintRencana')?.addEventListener('click', function () {
            window.print();
        });
    </script>
</body>
</html>
