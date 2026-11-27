<?php
/**
 * Cetak Laporan Audit Internal SMKP Minerba (A4 Portrait)
 * Disesuaikan dengan format resmi Laporan Internal Audit SMKP (Toka Tindung Project / Archi Indonesia):
 * - Header 3 kolom & Footer berulang di tiap halaman cetak.
 * - Sampul, Daftar Isi, Bab I–V (termasuk 5.1.3 OFI & 5.2 Tabel + Radar SVG), Bab VI Lampiran, Bab VII Halaman Penutup.
 * - Sebelum disahkan: dirender dari data hidup dengan watermark DRAFT.
 * - Setelah disahkan: dirender sepenuhnya dari snapshot JSON + SHA-256.
 */
$fontFamily   = (string) config('laporan.font_family', 'Arial, Helvetica, sans-serif');
$sizeJudul    = (string) config('laporan.font_size_judul', '16pt');
$sizeSubjudul = (string) config('laporan.font_size_subjudul', '12pt');
$sizeIsi      = (string) config('laporan.font_size_isi', '11pt');

$meta         = $payload['meta'] ?? [];
$perusahaan   = $payload['perusahaan'] ?? [];
$legalitas    = $perusahaan['legalitas'] ?? null;
$profil       = $payload['profil'] ?? null;
$peralatans   = $payload['peralatans'] ?? [];
$alatTotals   = $payload['peralatan_totals'] ?? ['per_kelompok' => [], 'grand_total' => 0];
$narasiElemen = $payload['narasi_elemens'] ?? [];
$lingkupAreas = $payload['lingkup_areas'] ?? [];
$timAudit     = $payload['tim_audit'] ?? [];
$narasiTpl    = $payload['narasi_template'] ?? [];
$pencapaian   = $payload['pencapaian'] ?? ['skor_akhir' => 0, 'skor_akhir_terbilang' => 'nol', 'rekap_elemen' => []];
$rekapElemen  = $pencapaian['rekap_elemen'] ?? [];
$praktikBaik  = $payload['praktik_terbaik'] ?? [];
$temuan       = $payload['temuan'] ?? ['kritikal_count' => 0, 'mayor_count' => 0, 'minor_count' => 0, 'total_temuan' => 0, 'kritikal' => [], 'mayor' => [], 'minor' => []];
$ofis         = $payload['ofis'] ?? [];
$lampiran     = $payload['lampiran'] ?? [];
$iujpEligible = (bool) ($lampiran['iujp_eligible'] ?? false);

$namaPerusahaan = (string) ($perusahaan['nama_perusahaan'] ?? $sesi->area_audit ?? '-');
$kodePerusahaan = (string) ($perusahaan['kode_perusahaan'] ?? '');
$searchLogo     = strtolower($namaPerusahaan . ' ' . $kodePerusahaan);
$companyLogo    = (str_contains($searchLogo, 'tondano') || str_contains($searchLogo, 'ttn'))
    ? 'logo_ttn.jpg'
    : 'logo_msm.jpg';

$tahunPeriode = (string) ($meta['tahun_periode'] ?? $sesi->tahun_periode ?? date('Y'));
$tempatTerbit = (string) ($meta['tempat_penerbitan'] ?: 'Likupang Timur');

$bulanIndo = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
];
$fmtTglIndo = function (?string $dateStr) use ($bulanIndo): string {
    if (empty($dateStr)) {
        return '-';
    }
    $dt = \Carbon\Carbon::parse($dateStr);

    return (int) $dt->format('d') . ' ' . ($bulanIndo[(int) $dt->format('n')] ?? '') . ' ' . $dt->format('Y');
};

$tglMulaiObj   = ! empty($meta['tanggal_mulai']) ? \Carbon\Carbon::parse($meta['tanggal_mulai']) : null;
$tglSelesaiObj = ! empty($meta['tanggal_selesai']) ? \Carbon\Carbon::parse($meta['tanggal_selesai']) : null;
if ($tglMulaiObj && $tglSelesaiObj) {
    if ($tglMulaiObj->format('Y-m') === $tglSelesaiObj->format('Y-m')) {
        $rentangPelaksanaan = (int) $tglMulaiObj->format('d') . ' - ' . (int) $tglSelesaiObj->format('d') . ' ' . ($bulanIndo[(int) $tglSelesaiObj->format('n')] ?? '') . ' ' . $tglSelesaiObj->format('Y');
    } else {
        $rentangPelaksanaan = $fmtTglIndo($meta['tanggal_mulai']) . ' - ' . $fmtTglIndo($meta['tanggal_selesai']);
    }
    $rentangPelaksanaanTeks = $fmtTglIndo($meta['tanggal_mulai']) . ' sampai dengan ' . $fmtTglIndo($meta['tanggal_selesai']);
} else {
    $rentangPelaksanaan = '-';
    $rentangPelaksanaanTeks = '-';
}
$tanggalLaporanIndo = $fmtTglIndo($meta['tanggal_laporan'] ?? null);

$peranShort = [
    'lead_auditor' => 'Lead Auditor',
    'auditor'      => 'Auditor',
    'tenaga_ahli'  => 'Tenaga Ahli',
    'pengamat'     => 'Pengamat',
    'pemandu'      => 'Pemandu',
];

// Gabungkan seluruh temuan menjadi 1 daftar terpadu untuk tabel 5.1.2 (sesuai dokumen halaman 27-36)
$allTemuan = [];
foreach (['kritikal' => 'Kritikal', 'mayor' => 'Mayor', 'minor' => 'Minor'] as $kKey => $kLbl) {
    foreach (($temuan[$kKey] ?? []) as $it) {
        $it['_kategori_label'] = $kLbl;
        $allTemuan[] = $it;
    }
}

$isAuditorRole = auth()->check() && auth()->user()->role === 'auditor';
$routeKriteria = $isAuditorRole ? route('auditor.audit-sesi.cetak', $sesi->id) : route('admin.audit-sesi.cetak', $sesi->id);
$routeRtl      = $isAuditorRole ? route('auditor.audit-sesi.cetak-ketidaksesuaian', $sesi->id) : route('admin.audit-sesi.cetak-ketidaksesuaian', $sesi->id);
$routeDhOpen   = $isAuditorRole ? route('auditor.audit-sesi.laporan.daftar-hadir.cetak', [$sesi->id, 'pembukaan']) : route('admin.audit-sesi.laporan.daftar-hadir.cetak', [$sesi->id, 'pembukaan']);
$routeDhClose  = $isAuditorRole ? route('auditor.audit-sesi.laporan.daftar-hadir.cetak', [$sesi->id, 'penutupan']) : route('admin.audit-sesi.laporan.daftar-hadir.cetak', [$sesi->id, 'penutupan']);

$strukturPath = (string) ($profil['struktur_organisasi_path'] ?? '');
$strukturExt  = strtolower(pathinfo($strukturPath, PATHINFO_EXTENSION));
$strukturIsImg = in_array($strukturExt, ['jpg', 'jpeg', 'png'], true) && ! empty($profil['id']);
$strukturUrl   = ! empty($profil['id']) ? route('attachments.show', ['type' => 'struktur_organisasi', 'id' => $profil['id']]) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Audit Internal SMKP - <?php echo e($namaPerusahaan); ?> (<?php echo e($tahunPeriode); ?>)</title>
    <link href="<?php echo asset('vendor/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 16mm 14mm 16mm;
        }
        body {
            font-family: <?php echo e($fontFamily); ?>;
            font-size: <?php echo e($sizeIsi); ?>;
            line-height: 1.5;
            color: #000000;
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
            padding: 12mm 16mm;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
        }
        .watermark-draft {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-32deg);
            font-size: 42pt;
            font-weight: 800;
            color: rgba(220, 38, 38, 0.11);
            letter-spacing: 4px;
            pointer-events: none;
            z-index: 10;
            white-space: nowrap;
        }
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
        .header-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .header-box td {
            border: 1px solid #475569;
            padding: 6px 10px;
            vertical-align: middle;
        }
        .header-logo-left {
            width: 22%;
            text-align: center;
        }
        .header-title-center {
            width: 56%;
            text-align: center;
            font-weight: 700;
            font-size: 12pt;
            letter-spacing: 0.4px;
        }
        .header-logo-right {
            width: 22%;
            text-align: center;
        }
        .header-box img {
            max-height: 46px;
            max-width: 100%;
            object-fit: contain;
        }
        .footer-line {
            border-top: 1px solid #000000;
            padding-top: 4px;
            margin-top: 12px;
            font-size: 9.5pt;
            display: flex;
            justify-content: space-between;
        }
        .cover-section {
            min-height: 225mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            padding: 18mm 6mm 14mm 6mm;
            page-break-after: always;
        }
        .cover-title {
            font-size: <?php echo e($sizeJudul); ?>;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.55;
        }
        .cover-company {
            font-size: <?php echo e($sizeSubjudul); ?>;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        .cover-block {
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.45;
            margin-bottom: 14px;
        }
        .toc-section {
            min-height: 220mm;
            page-break-after: always;
        }
        .toc-title {
            font-size: 14pt;
            font-weight: 400;
            margin-bottom: 16px;
        }
        .toc-row {
            display: flex;
            align-items: baseline;
            margin-bottom: 10px;
            font-size: 11pt;
        }
        .toc-roman {
            width: 34px;
            flex-shrink: 0;
        }
        .toc-dots {
            flex-grow: 1;
            border-bottom: 1px dotted #334155;
            margin: 0 8px;
        }
        .bab-heading {
            font-size: 11.5pt;
            font-weight: 400;
            margin: 16px 0 8px 0;
            page-break-after: avoid;
        }
        .subbab-indent {
            padding-left: 26px;
        }
        .body-p {
            text-align: justify;
            margin-bottom: 10px;
            white-space: pre-line;
        }
        .formal-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 12px 0;
            font-size: 10pt;
        }
        .formal-table th, .formal-table td {
            border: 1px solid #000000;
            padding: 5px 7px;
            vertical-align: top;
        }
        .formal-table th {
            background-color: #d9d9d9;
            font-weight: 700;
            text-align: center;
        }
        .score-table th {
            background-color: #e6b8af;
            text-align: center;
            vertical-align: middle;
        }
        .score-total-row td {
            font-weight: 700;
            background-color: #93c47d;
        }
        .score-total-final {
            background-color: #e06666 !important;
            color: #000000;
        }
        .ofi-card-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10pt;
            page-break-inside: avoid;
        }
        .ofi-card-table td {
            border: 1px solid #000000;
            padding: 5px 8px;
            vertical-align: top;
        }
        .ofi-card-label {
            width: 34%;
        }
        .tim-row {
            display: flex;
            margin-bottom: 6px;
            padding-left: 26px;
        }
        .tim-role {
            width: 150px;
            flex-shrink: 0;
        }
        .closing-section {
            page-break-before: always;
            padding-top: 8px;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 22px;
        }
        .ttd-table td {
            padding: 16px 8px 6px 0;
            vertical-align: bottom;
        }
        .ttd-dotted {
            border-bottom: 1px dotted #000000;
            display: inline-block;
            width: 100%;
            min-height: 24px;
        }
        .radar-wrap {
            text-align: center;
            margin: 16px 0;
            page-break-inside: avoid;
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
        <table class="master-print-layout">
            <thead>
                <tr>
                    <td>
                        <table class="header-box">
                            <tr>
                                <td class="header-logo-left">
                                    <img src="<?php echo asset('images/' . $companyLogo); ?>" alt="Logo Perusahaan">
                                </td>
                                <td class="header-title-center">TOKA TINDUNG PROJECT</td>
                                <td class="header-logo-right">
                                    <img src="<?php echo asset('images/logo_archi.png'); ?>" alt="Archi Indonesia">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td>
                        <div class="footer-line">
                            <span>Laporan Audit Internal SMKP - <?php echo e($namaPerusahaan); ?></span>
                            <span>Periode <?php echo e($tahunPeriode); ?></span>
                        </div>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <tr>
                    <td>
                        <!-- HALAMAN 1: SAMPUL -->
                        <div class="cover-section">
                            <div class="cover-title">
                                LAPORAN <em>INTERNAL AUDIT</em><br>
                                PENERAPAN SISTEM MANAJEMEN KESELAMATAN<br>
                                PERTAMBANGAN MINERAL DAN BATUBARA
                            </div>

                            <div>
                                <div class="cover-company"><?php echo e(strtoupper($namaPerusahaan)); ?></div>

                                <?php if (! empty($perusahaan['alamat_kantor_pusat'])): ?>
                                    <div class="cover-block">
                                        ALAMAT KANTOR PUSAT :<br>
                                        <?php echo e(strtoupper((string) $perusahaan['alamat_kantor_pusat'])); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (! empty($perusahaan['alamat_kantor_site']) || ! empty($perusahaan['alamat_lokasi_tambang'])): ?>
                                    <div class="cover-block">
                                        ALAMAT KANTOR <em>SITE</em> :<br>
                                        <?php echo e(strtoupper((string) ($perusahaan['alamat_kantor_site'] ?: $perusahaan['alamat_lokasi_tambang']))); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="cover-block">
                                PELAKSANAAN AUDIT :<br>
                                <?php echo e(strtoupper($rentangPelaksanaan)); ?>
                            </div>
                        </div>

                        <!-- HALAMAN 2: DAFTAR ISI -->
                        <div class="toc-section">
                            <div class="toc-title">Daftar isi</div>
                            <div class="toc-row"><span class="toc-roman">I.</span><span>Latar Belakang</span><span class="toc-dots"></span></div>
                            <div class="toc-row"><span class="toc-roman">II.</span><span>Gambaran Umum Perusahaan</span><span class="toc-dots"></span></div>
                            <div class="toc-row"><span class="toc-roman">III.</span><span>Lingkup Audit</span><span class="toc-dots"></span></div>
                            <div class="toc-row"><span class="toc-roman">IV.</span><span>Pelaksanaan Audit dan Tim Auditor</span><span class="toc-dots"></span></div>
                            <div class="toc-row"><span class="toc-roman">V.</span><span>Ringkasan Laporan &amp; Penilaian Audit</span><span class="toc-dots"></span></div>
                            <div class="toc-row"><span class="toc-roman">VI.</span><span>Lampiran – Lampiran</span><span class="toc-dots"></span></div>
                            <div class="toc-row"><span class="toc-roman">VII.</span><span>Halaman Penutup</span><span class="toc-dots"></span></div>
                        </div>

                        <!-- 1. LATAR BELAKANG -->
                        <div class="bab-heading">1. &nbsp;&nbsp;Latar Belakang</div>
                        <div class="subbab-indent">
                            <div class="body-p"><?php echo e($narasiTpl['latar_belakang'] ?? ''); ?></div>
                        </div>

                        <!-- 2. GAMBARAN UMUM PERUSAHAAN -->
                        <div class="bab-heading">2. &nbsp;&nbsp;Gambaran Umum Perusahaan</div>
                        <div class="subbab-indent">
                            <p class="body-p">Adapun gambaran umum <?php echo e($namaPerusahaan); ?> adalah sebagai berikut:</p>

                            <!-- 2.1 Domisili & Legalitas -->
                            <div class="body-p">
                                <strong>2.1</strong>
                                <?php
                                $wilayahParts = array_filter([
                                    $legalitas['lokasi_desa'] ?? null ? 'Desa ' . $legalitas['lokasi_desa'] : null,
                                    $legalitas['lokasi_kecamatan'] ?? null ? 'Kecamatan ' . $legalitas['lokasi_kecamatan'] : null,
                                    $legalitas['lokasi_kabupaten'] ?? null ? $legalitas['lokasi_kabupaten'] : null,
                                    $legalitas['lokasi_provinsi'] ?? null ? 'Provinsi ' . $legalitas['lokasi_provinsi'] : null,
                                ]);
                                $wilayahTeks = ! empty($wilayahParts) ? implode(', ', $wilayahParts) : ($perusahaan['alamat_lokasi_tambang'] ?? '-');
                                ?>
                                Wilayah <?php echo e($legalitas['jenis_izin_label'] ?? 'Usaha Pertambangan'); ?> <?php echo e($namaPerusahaan); ?> terletak di <?php echo e($wilayahTeks); ?>.
                                <?php if (! empty($perusahaan['alamat_kantor_site'])): ?>
                                    Kantor site <?php echo e($namaPerusahaan); ?> terletak di <?php echo e($perusahaan['alamat_kantor_site']); ?>
                                <?php endif; ?>
                                <?php if (! empty($perusahaan['alamat_kantor_pusat'])): ?>
                                    dan Kantor Pusat <?php echo e($namaPerusahaan); ?> berlokasi di <?php echo e($perusahaan['alamat_kantor_pusat']); ?>.
                                <?php endif; ?>
                                <?php if ($legalitas): ?>
                                    <?php echo e($namaPerusahaan); ?> merupakan pemegang <?php echo e($legalitas['jenis_izin_label'] ?? '-'); ?>
                                    <?php if (! empty($legalitas['status_izin'])): ?>dengan status <?php echo e($legalitas['status_izin']); ?><?php endif; ?>
                                    <?php if (! empty($legalitas['nomor_sk_izin'])): ?>berdasarkan nomor perizinan <?php echo e($legalitas['nomor_sk_izin']); ?><?php endif; ?>
                                    <?php if (! empty($legalitas['tanggal_sk'])): ?>tanggal <?php echo e($fmtTglIndo($legalitas['tanggal_sk'])); ?><?php endif; ?>
                                    <?php if ($legalitas['luas_ha'] !== null): ?>dengan luas <?php echo e(number_format((float) $legalitas['luas_ha'], 2, ',', '.')); ?> Ha<?php endif; ?>
                                    <?php if (! empty($legalitas['kode_wiup'])): ?>(Kode WIUP: <?php echo e($legalitas['kode_wiup']); ?>)<?php endif; ?>
                                    <?php if (! empty($legalitas['tanggal_mulai_berlaku']) || ! empty($legalitas['tanggal_berakhir'])): ?>
                                        , berlaku mulai <?php echo e($fmtTglIndo($legalitas['tanggal_mulai_berlaku'] ?? null)); ?> hingga <?php echo e($fmtTglIndo($legalitas['tanggal_berakhir'] ?? null)); ?>.
                                    <?php else: ?>
                                        .
                                    <?php endif; ?>
                                    <?php if (! empty($legalitas['nomor_persetujuan_studi_kelayakan']) || ! empty($legalitas['nomor_persetujuan_dokumen_lingkungan'])): ?>
                                        <?php echo e($namaPerusahaan); ?> memiliki
                                        <?php if (! empty($legalitas['nomor_persetujuan_studi_kelayakan'])): ?>
                                            Persetujuan Studi Kelayakan <?php echo e($legalitas['nomor_persetujuan_studi_kelayakan']); ?>
                                            <?php if (! empty($legalitas['tanggal_persetujuan_studi_kelayakan'])): ?>tanggal <?php echo e($fmtTglIndo($legalitas['tanggal_persetujuan_studi_kelayakan'])); ?><?php endif; ?>
                                        <?php endif; ?>
                                        <?php if (! empty($legalitas['nomor_persetujuan_studi_kelayakan']) && ! empty($legalitas['nomor_persetujuan_dokumen_lingkungan'])): ?> dan <?php endif; ?>
                                        <?php if (! empty($legalitas['nomor_persetujuan_dokumen_lingkungan'])): ?>
                                            Persetujuan Lingkungan nomor <?php echo e($legalitas['nomor_persetujuan_dokumen_lingkungan']); ?>
                                            <?php if (! empty($legalitas['tanggal_persetujuan_dokumen_lingkungan'])): ?>tanggal <?php echo e($fmtTglIndo($legalitas['tanggal_persetujuan_dokumen_lingkungan'])); ?><?php endif; ?>
                                        <?php endif; ?>.
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <!-- 2.2 Kegiatan, Produksi, Peralatan, Tenaga Kerja, Struktur Organisasi -->
                            <div class="body-p">
                                <strong>2.2</strong>
                                <?php if (! empty($profil['narasi_kegiatan'])): ?>
                                    <?php echo e($profil['narasi_kegiatan']); ?>
                                <?php else: ?>
                                    <?php echo e($namaPerusahaan); ?> melaksanakan kegiatan usaha pertambangan dengan komoditas <?php echo e($perusahaan['jenis_komoditas'] ?? '-'); ?>.
                                <?php endif; ?>
                                <?php if ($profil && ($profil['kapasitas_produksi'] !== null || $profil['realisasi_produksi'] !== null)): ?>
                                    Kapasitas produksi sebesar <?php echo e($profil['kapasitas_produksi'] !== null ? number_format((float) $profil['kapasitas_produksi'], 2, ',', '.') : '-'); ?> <?php echo e($profil['satuan_produksi'] ?? ''); ?>
                                    dengan realisasi produksi sebesar <?php echo e($profil['realisasi_produksi'] !== null ? number_format((float) $profil['realisasi_produksi'], 2, ',', '.') : '-'); ?> <?php echo e($profil['satuan_produksi'] ?? ''); ?>.
                                <?php endif; ?>
                            </div>

                            <?php if (! empty($peralatans)): ?>
                                <table class="formal-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;">No.</th>
                                            <th>Nama Peralatan dan/atau Instalasi</th>
                                            <th style="width: 28%;">Kelompok / Lokasi</th>
                                            <th style="width: 85px;">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($peralatans as $idx => $alat): ?>
                                            <tr>
                                                <td class="text-center"><?php echo e($idx + 1); ?></td>
                                                <td>
                                                    <?php echo e($alat['jenis'] ?? '-'); ?>
                                                    <?php if (! empty($alat['tipe'])): ?> (<?php echo e($alat['tipe']); ?>)<?php endif; ?>
                                                    <?php if (! empty($alat['kapasitas'])): ?> — <?php echo e($alat['kapasitas']); ?><?php endif; ?>
                                                </td>
                                                <td><?php echo e($alat['kelompok'] ?? '-'); ?></td>
                                                <td class="text-center"><?php echo e((int) ($alat['jumlah'] ?? 0)); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" style="text-align: right;">Total Peralatan</th>
                                            <th style="text-align: center;"><?php echo e((int) ($alatTotals['grand_total'] ?? 0)); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="text-center small mb-3">Tabel 1 Daftar Peralatan <?php echo e($namaPerusahaan); ?></div>
                            <?php endif; ?>

                            <?php if ($profil && $profil['jumlah_pekerja'] !== null): ?>
                                <p class="body-p">
                                    Saat ini jumlah tenaga kerja <?php echo e($namaPerusahaan); ?> adalah sebanyak <?php echo e(number_format((int) $profil['jumlah_pekerja'], 0, ',', '.')); ?> orang
                                    <?php if (! empty($profil['jumlah_pekerja_asing'])): ?>
                                        (termasuk <?php echo e(number_format((int) $profil['jumlah_pekerja_asing'], 0, ',', '.')); ?> orang tenaga kerja asing)
                                    <?php endif; ?>.
                                </p>
                            <?php endif; ?>

                            <?php if ($strukturUrl && ! empty($strukturPath)): ?>
                                <p class="body-p">Struktur Organisasi <?php echo e($namaPerusahaan); ?> adalah sebagai berikut:</p>
                                <?php if ($strukturIsImg): ?>
                                    <div class="text-center my-2">
                                        <img src="<?php echo e($strukturUrl); ?>" alt="Struktur Organisasi" style="max-width: 100%; max-height: 145mm; border: 1px solid #cbd5e1;">
                                    </div>
                                <?php else: ?>
                                    <p class="small text-muted"><em>(Bagan Struktur Organisasi terlampir dalam berkas: <?php echo e(basename($strukturPath)); ?>)</em></p>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- 2.3 Penerapan SMKP per Elemen -->
                            <div class="body-p mt-3">
                                <strong>2.3</strong>
                                <?php if (! empty($profil['narasi_penerapan_smkp'])): ?>
                                    <?php echo e($profil['narasi_penerapan_smkp']); ?>
                                <?php else: ?>
                                    <?php echo e($namaPerusahaan); ?> telah menerapkan Sistem Manajemen Keselamatan Pertambangan (SMKP) Mineral dan Batubara. Ringkasan penerapan SMKP <?php echo e($namaPerusahaan); ?> untuk setiap elemen adalah sebagai berikut:
                                <?php endif; ?>
                            </div>

                            <?php if (! empty($narasiElemen)): ?>
                                <?php foreach ($narasiElemen as $ne): ?>
                                    <?php if (! empty($ne['narasi'])): ?>
                                        <div class="mt-2">
                                            <div><?php echo e(($ne['kode_elemen'] ?? '-') . '.  Elemen ' . ($ne['kode_elemen'] ?? '-')); ?></div>
                                            <div class="subbab-indent body-p"><?php echo e($ne['narasi']); ?></div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- 3. LINGKUP AUDIT -->
                        <div class="bab-heading">3. &nbsp;&nbsp;Lingkup Audit</div>
                        <div class="subbab-indent">
                            <?php if (! empty($payload['ruang_lingkup_teks'])): ?>
                                <p class="body-p"><?php echo e($payload['ruang_lingkup_teks']); ?></p>
                            <?php else: ?>
                                <p class="body-p">Audit SMKP Minerba ini merupakan audit internal berkala untuk mendapatkan gambaran tingkat penerapan SMKP Minerba pada <?php echo e($namaPerusahaan); ?>, yang terdiri atas beberapa area kegiatan:</p>
                            <?php endif; ?>

                            <?php if (! empty($lingkupAreas)): ?>
                                <?php foreach ($lingkupAreas as $idx => $area): ?>
                                    <div class="mb-1">3.<?php echo e($idx + 1); ?> &nbsp;<?php echo e($area); ?></div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- 4. PELAKSANAAN AUDIT DAN TIM AUDITOR -->
                        <div class="bab-heading">4. &nbsp;&nbsp;Pelaksanaan Audit dan Tim Auditor</div>
                        <div class="subbab-indent">
                            <p class="body-p">Audit Internal SMKP Minerba di <?php echo e($namaPerusahaan); ?> dilaksanakan sejak periode tanggal <?php echo e($rentangPelaksanaanTeks); ?>, dengan tim auditor yang terdiri atas:</p>
                            <?php if (! empty($timAudit)): ?>
                                <?php foreach ($timAudit as $t): ?>
                                    <div class="tim-row">
                                        <span class="tim-role"><?php echo e($peranShort[$t['peran'] ?? ''] ?? ($t['peran'] ?? 'Auditor')); ?></span>
                                        <span>: <?php echo e($t['nama'] ?? '-'); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="tim-row">
                                    <span class="tim-role">Lead Auditor</span>
                                    <span>: <?php echo e($meta['penanggung_jawab_nama'] ?? '-'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- 5. RINGKASAN LAPORAN & PENILAIAN AUDIT -->
                        <div class="bab-heading page-break">5. &nbsp;&nbsp;Ringkasan Laporan &amp; Penilaian Audit</div>
                        <div class="subbab-indent">
                            <div class="mb-2">5.1. Hasil Audit</div>
                            <p class="body-p">
                                Hasil Audit SMKP Minerba yang dilaksanakan terhadap <?php echo e($namaPerusahaan); ?> adalah audit internal untuk periode tahun <?php echo e($tahunPeriode); ?>, dengan memperoleh informasi sebagai berikut:
                            </p>

                            <!-- 5.1.1 Informasi Praktek Terbaik -->
                            <div class="mb-2">5.1.1. Informasi Praktek Terbaik</div>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 22%;">Elemen</th>
                                        <th style="width: 28%;">Sub Elemen</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (! empty($praktikBaik)): ?>
                                        <?php foreach ($praktikBaik as $pb): ?>
                                            <tr>
                                                <td><?php echo e(($pb['elemen_kode'] ?? '') . '. ' . ($pb['elemen_nama'] ?? '')); ?></td>
                                                <td><?php echo e(($pb['kode_sub'] ?? '-') . ' ' . ($pb['nama_sub'] ?? '')); ?></td>
                                                <td class="body-p"><?php echo e($pb['keterangan'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center text-muted">Belum terdapat sub-elemen dengan kesesuaian penuh 100%.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <!-- 5.1.2 Jumlah dan Ringkasan Temuan Ketidaksesuaian -->
                            <div class="mt-3 mb-2">5.1.2. Jumlah dan Ringkasan Temuan Ketidaksesuaian</div>
                            <p class="body-p">Berikut ini jumlah dan ringkasan informasi mengenai temuan-temuan sesuai dengan kategorinya:</p>
                            <?php
                            $kCount  = (int) ($temuan['kritikal_count'] ?? 0);
                            $myCount = (int) ($temuan['mayor_count'] ?? 0);
                            $mnCount = (int) ($temuan['minor_count'] ?? 0);
                            ?>
                            <div class="subbab-indent mb-2">
                                <div>a. Kategori Kritikal &nbsp;&nbsp;&nbsp;: <?php echo $kCount > 0 ? e($kCount . ' temuan') : 'Tidak ada'; ?></div>
                                <div>b. Kategori Mayor &nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $myCount > 0 ? e($myCount . ' temuan') : 'Tidak ada'; ?></div>
                                <div>c. Kategori Minor &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo $mnCount > 0 ? e($mnCount . ' temuan') : 'Tidak ada'; ?></div>
                            </div>
                            <p class="mb-1">Berikut ini uraian temuan audit:</p>
                            <table class="formal-table">
                                <thead>
                                    <tr>
                                        <th style="width: 48px;">No</th>
                                        <th>Deskripsi</th>
                                        <th style="width: 95px;">Kategori</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (! empty($allTemuan)): ?>
                                        <?php foreach ($allTemuan as $idx => $it): ?>
                                            <tr>
                                                <td class="text-center"><?php echo e($idx + 1); ?>.</td>
                                                <td class="body-p">
                                                    <strong>[<?php echo e($it['kode_temuan'] ?? '-'); ?>]</strong>
                                                    <?php if (! empty($it['plor_problem']) || ! empty($it['plor_location']) || ! empty($it['plor_objective_evidence']) || ! empty($it['plor_reference'])): ?>
                                                        <?php echo e(implode(' ', array_filter([$it['plor_problem'] ?? '', $it['plor_location'] ? 'di ' . $it['plor_location'] . '.' : '', $it['plor_objective_evidence'] ? 'Berdasarkan ' . $it['plor_objective_evidence'] . '.' : '', $it['plor_reference'] ? 'Hal ini tidak sesuai dengan ' . $it['plor_reference'] . '.' : '']))); ?>
                                                    <?php else: ?>
                                                        <?php echo e($it['deskripsi_temuan'] ?? '-'); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?php echo e($it['_kategori_label']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center text-muted">Tidak ada temuan ketidaksesuaian.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <!-- 5.1.3 Rekomendasi dan Peluang untuk Perbaikan (OFI) -->
                            <?php if (! empty($ofis)): ?>
                                <div class="mt-3 mb-2">5.1.3. Rekomendasi dan Peluang untuk Perbaikan</div>
                                <?php foreach ($ofis as $ofi): ?>
                                    <table class="ofi-card-table">
                                        <tbody>
                                            <tr>
                                                <td class="ofi-card-label">Elemen</td>
                                                <td><?php echo e(trim(($ofi['elemen_kode'] ?? '') . ' ' . ($ofi['elemen_nama'] ?? '')) ?: '-'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="ofi-card-label">Sub Elemen</td>
                                                <td><?php echo e(trim(($ofi['sub_elemen_kode'] ?? '') . ' ' . ($ofi['sub_elemen_nama'] ?? '')) ?: '-'); ?></td>
                                            </tr>
                                            <?php if (! empty($ofi['kriteria_kode'])): ?>
                                                <tr>
                                                    <td class="ofi-card-label">Sub Sub Elemen</td>
                                                    <td><?php echo e(trim(($ofi['kriteria_kode'] ?? '') . ' ' . ($ofi['kriteria_deskripsi'] ?? ''))); ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td class="ofi-card-label">Rekomendasi dan Peluang untuk Perbaikan</td>
                                                <td class="body-p"><?php echo e(trim(implode(' — ', array_filter([$ofi['deskripsi'] ?? '', $ofi['rekomendasi'] ?? ''])))); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- 5.2 Tingkat Pencapaian Penerapan SMKP Minerba -->
                            <?php
                            $sumBobot  = 0.0;
                            $sumAktual = 0;
                            $sumMaks   = 0;
                            $sumSkor   = 0.0;
                            foreach ($rekapElemen as $rTmp) {
                                $sumBobot  += (float) ($rTmp['bobot'] ?? 0);
                                $sumAktual += (int) ($rTmp['total_nilai_aktual'] ?? 0);
                                $sumMaks   += (int) ($rTmp['total_nilai_maks_efektif'] ?? 0);
                                $sumSkor   += (float) ($rTmp['skor_elemen'] ?? 0);
                            }
                            $skorAkhirVal = (float) ($pencapaian['skor_akhir'] ?? $sumSkor);
                            $skorTerbilangTitle = ucwords((string) ($pencapaian['skor_akhir_terbilang'] ?? \App\Support\Terbilang::decimal($skorAkhirVal)));
                            ?>
                            <div class="mt-3 mb-2">5.2. Tingkat Pencapaian Penerapan SMKP Minerba</div>
                            <p class="body-p">
                                Selanjutnya, berdasarkan Pedoman Teknis SMKP sebagaimana dimaksud, maka tingkat Pencapaian Penerapan SMKP di <?php echo e($namaPerusahaan); ?> adalah
                                <strong><?php echo e(number_format($skorAkhirVal, 2, '.', '')); ?> %</strong>
                                (<?php echo e($skorTerbilangTitle); ?> Persen).
                            </p>

                            <table class="formal-table score-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="width: 40px;">NO</th>
                                        <th rowspan="2">Elemen</th>
                                        <th colspan="2">Nilai Maksimal Kriteria</th>
                                        <th colspan="2">Nilai Audit</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 78px;">Nilai Sub Elemen</th>
                                        <th style="width: 78px;">Persentase</th>
                                        <th style="width: 78px;">Total Nilai Elemen</th>
                                        <th style="width: 88px;">Persentase Nilai Elemen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rekapElemen as $idx => $row): ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                                            <td><?php echo e(strtoupper((string) ($row['nama_elemen'] ?? '-'))); ?></td>
                                            <td class="text-center"><?php echo e((int) ($row['total_nilai_maks_efektif'] ?? 0)); ?></td>
                                            <td class="text-center"><?php echo e(number_format((float) ($row['bobot'] ?? 0), 0)); ?>%</td>
                                            <td class="text-center"><?php echo e((int) ($row['total_nilai_aktual'] ?? 0)); ?></td>
                                            <td class="text-center"><?php echo e(number_format((float) ($row['skor_elemen'] ?? 0), 1)); ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="score-total-row">
                                        <td colspan="2" class="text-center">TOTAL</td>
                                        <td class="text-center"><?php echo e($sumMaks); ?></td>
                                        <td class="text-center"><?php echo e(number_format($sumBobot, 0)); ?>%</td>
                                        <td class="text-center"><?php echo e($sumAktual); ?></td>
                                        <td class="text-center score-total-final"><?php echo e(number_format($skorAkhirVal, 2)); ?>%</td>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="radar-wrap">
                                <?php echo $radarChartSvg; ?>
                                <div class="small text-muted mt-1">Grafik Radar Tingkat Pencapaian Penerapan SMKP per Elemen</div>
                            </div>
                        </div>

                        <!-- VI. LAMPIRAN – LAMPIRAN -->
                        <div class="bab-heading page-break">VI. &nbsp;&nbsp;Lampiran – Lampiran</div>
                        <div class="subbab-indent">
                            <ol class="mt-2">
                                <li class="mb-2">
                                    Formulir Kriteria Audit
                                    <a href="<?php echo e($routeKriteria); ?>" target="_blank" class="d-print-none small ms-1">[Buka]</a>
                                </li>
                                <li class="mb-2">
                                    Tindak Lanjut Hasil Audit
                                    <a href="<?php echo e($routeRtl); ?>" target="_blank" class="d-print-none small ms-1">[Buka]</a>
                                </li>
                                <li class="mb-2">
                                    Daftar Hadir Peserta Pertemuan Pembukaan Audit
                                    <a href="<?php echo e($routeDhOpen); ?>" target="_blank" class="d-print-none small ms-1">[Cetak]</a>
                                </li>
                                <li class="mb-2">
                                    Daftar Hadir Peserta Pertemuan Penutupan Audit
                                    <a href="<?php echo e($routeDhClose); ?>" target="_blank" class="d-print-none small ms-1">[Cetak]</a>
                                </li>
                                <li class="mb-2">Respon Perusahaan Terhadap Pelaksanaan Audit</li>
                                <?php if ($iujpEligible): ?>
                                    <li class="mb-2">Hasil Audit Internal Perusahaan Jasa Pertambangan (IUJP)</li>
                                <?php endif; ?>
                            </ol>
                        </div>

                        <!-- VII. HALAMAN PENUTUP -->
                        <div class="closing-section">
                            <div class="bab-heading">VII. &nbsp;&nbsp;Halaman Penutup</div>
                            <p class="mt-3"><em><?php echo e($tempatTerbit); ?></em>, <?php echo e($tanggalLaporanIndo); ?></p>
                            <p class="mb-1">Dibuat oleh,</p>
                            <p class="mb-4">Tim Internal Auditor</p>

                            <table class="ttd-table">
                                <tbody>
                                    <?php if (! empty($timAudit)): ?>
                                        <?php foreach ($timAudit as $t): ?>
                                            <tr>
                                                <td style="width: 22%;"><?php echo e($peranShort[$t['peran'] ?? ''] ?? ($t['peran'] ?? 'Auditor')); ?></td>
                                                <td style="width: 38%;">
                                                    <div><?php echo e($t['nama'] ?? '-'); ?></div>
                                                    <span class="ttd-dotted"></span>
                                                </td>
                                                <td style="width: 18%; text-align: center;">Tanda Tangan</td>
                                                <td style="width: 22%;"><span class="ttd-dotted"></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td style="width: 22%;">Lead Auditor</td>
                                            <td style="width: 38%;">
                                                <div><?php echo e($meta['penanggung_jawab_nama'] ?? '-'); ?></div>
                                                <span class="ttd-dotted"></span>
                                            </td>
                                            <td style="width: 18%; text-align: center;">Tanda Tangan</td>
                                            <td style="width: 22%;"><span class="ttd-dotted"></span></td>
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
                    </td>
                </tr>
            </tbody>
        </table>
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
