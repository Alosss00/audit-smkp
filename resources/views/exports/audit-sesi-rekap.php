<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table>
        <!-- Meta Header Audit Sesi -->
        <tr>
            <td colspan="14" style="font-weight: bold; font-size: 14pt; text-align: center;">LAPORAN REKAPITULASI &amp; PENILAIAN AUDIT INTERNAL SMKP MINERBA</td>
        </tr>
        <tr>
            <td colspan="14" style="font-size: 10pt; text-align: center; color: #475569;">Berdasarkan Keputusan Direktur Jenderal Mineral dan Batubara ESDM Nomor 185.K/37.04/DJB/2019</td>
        </tr>
        <tr></tr>
        <tr>
            <td></td>
            <td style="font-weight: bold;" colspan="2">Formulir Acuan:</td>
            <td colspan="11">TT-MGT-FRS-026B (Checklist Kriteria Audit SMKP)</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold;" colspan="2">Perusahaan / Area Audit:</td>
            <td colspan="11"><?php echo e($sesi->area_audit); ?></td>
        </tr>
        <?php
            $thn = $sesi->tahun_periode ?? $sesi->tanggal_mulai->format('Y');
        ?>
        <tr>
            <td></td>
            <td style="font-weight: bold;" colspan="2">Tahun Periode Audit:</td>
            <td colspan="11">Tahun <?php echo e($thn); ?> (1 Januari <?php echo e($thn); ?> - 31 Desember <?php echo e($thn); ?>)</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold;" colspan="2">Jadwal Pelaksanaan Audit:</td>
            <td colspan="11"><?php echo e($sesi->tanggal_mulai->format('d F Y')); ?> - <?php echo e($sesi->tanggal_selesai->format('d F Y')); ?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold;" colspan="2">Auditor Pelaksana:</td>
            <td colspan="11"><?php echo e($sesi->user->name); ?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold;" colspan="2">Status Sesi Audit:</td>
            <td colspan="11"><?php echo e(strtoupper($sesi->status)); ?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold;" colspan="2">Pencapaian Skor Akhir (%):</td>
            <td style="font-weight: bold;" colspan="11"><?php echo e(number_format($skorAkhir, 2)); ?>%</td>
        </tr>
        <tr></tr>

        <thead>
            <tr>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">No</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">Kode Romawi</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">KRITERIA / ELEMEN AUDIT</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">Kode Sub / Sub-sub</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">Nama Sub-sub Elemen / Deskripsi Kriteria Penilaian</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">Nilai Elemen %</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">Nilai Sub Elemen (Maks)</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">Nilai Sub sub Elemen (Maks)</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">Nilai Sub Elemen (Aktual)</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">Nilai sub sub elemen (Aktual)</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">Total Nilai Elemen</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">Presentase Nilai Elemen (%)</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">KETERANGAN</th>
                <th style="font-weight: bold; text-align: center; background-color: #0f172a; color: #ffffff; border: 1px solid #000000;">Catatan Temuan Audit / Bukti Kepatuhan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach($elemens as $elemen): ?>
                <?php
                    $elemenData = collect($rekapElemen)->firstWhere('elemen_id', $elemen->id);
                ?>
                <tr style="background-color: #cbd5e1; font-weight: bold;">
                    <td style="text-align: center; border: 1px solid #000000; font-weight: bold;"><?php echo e($no++); ?></td>
                    <td style="text-align: center; border: 1px solid #000000; font-weight: bold;"><?php echo e($elemen->kode_elemen); ?></td>
                    <td style="border: 1px solid #000000; font-weight: bold;" colspan="2">ELEMEN <?php echo e($elemen->kode_elemen); ?>: <?php echo e($elemen->nama_elemen); ?></td>
                    <td style="border: 1px solid #000000;">-</td>
                    <td style="text-align: right; border: 1px solid #000000; font-weight: bold;"><?php echo e(number_format($elemen->bobot, 2)); ?>%</td>
                    <td style="text-align: right; border: 1px solid #000000; font-weight: bold;"><?php echo e(number_format($elemenData['total_nilai_maks_efektif'] ?? 0, 0)); ?></td>
                    <td style="border: 1px solid #000000;">-</td>
                    <td style="border: 1px solid #000000;">-</td>
                    <td style="border: 1px solid #000000;">-</td>
                    <td style="text-align: right; border: 1px solid #000000; font-weight: bold;"><?php echo e(number_format($elemenData['total_nilai_aktual'] ?? 0, 0)); ?></td>
                    <td style="text-align: right; border: 1px solid #000000; font-weight: bold;"><?php echo e(number_format($elemenData['persentase'] ?? 0, 2)); ?>%</td>
                    <td style="text-align: center; border: 1px solid #000000; font-weight: bold;">ELEMEN</td>
                    <td style="border: 1px solid #000000;">-</td>
                </tr>

                <?php foreach($elemen->subElemens as $sub): ?>
                    <?php
                        $subData = $rekapSub[$sub->id] ?? null;
                        $singleKriteria = $sub->kriterias->count() === 1 ? $sub->kriterias->first() : null;
                        $isDirect = ($singleKriteria && ($singleKriteria->kode_kriteria === $sub->kode_sub || $singleKriteria->deskripsi === $sub->nama_sub));
                        $directDetail = $singleKriteria ? $details->firstWhere('kriteria_id', $singleKriteria->id) : null;
                    ?>

                    <?php if($isDirect): ?>
                        <tr style="background-color: #f1f5f9; font-weight: bold;">
                            <td style="border: 1px solid #000000;"></td>
                            <td style="border: 1px solid #000000;"></td>
                            <td style="border: 1px solid #000000; font-weight: bold;"><?php echo e($sub->nama_sub); ?></td>
                            <td style="text-align: center; border: 1px solid #000000; font-weight: bold;"><?php echo e($sub->kode_sub); ?></td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="text-align: right; border: 1px solid #000000; font-weight: bold;"><?php echo e(number_format($subData['total_nilai_maks_efektif'] ?? 0, 0)); ?></td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="text-align: right; border: 1px solid #000000; font-weight: bold;"><?php echo e(number_format($subData['total_nilai_aktual'] ?? 0, 0)); ?></td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="text-align: center; border: 1px solid #000000; font-weight: bold;">
                                <?php echo e((!empty($directDetail) && $directDetail->is_na) ? 'N/A' : 'SUB ELEMEN'); ?>

                            </td>
                            <td style="border: 1px solid #000000;"><?php echo e($directDetail->catatan ?? ''); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr style="background-color: #f1f5f9; font-weight: bold;">
                            <td style="border: 1px solid #000000;"></td>
                            <td style="border: 1px solid #000000;"></td>
                            <td style="border: 1px solid #000000; font-weight: bold;"><?php echo e($sub->nama_sub); ?></td>
                            <td style="text-align: center; border: 1px solid #000000; font-weight: bold;"><?php echo e($sub->kode_sub); ?></td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="text-align: right; border: 1px solid #000000; font-weight: bold;"><?php echo e(number_format($subData['total_nilai_maks_efektif'] ?? 0, 0)); ?></td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="text-align: right; border: 1px solid #000000; font-weight: bold;"><?php echo e(number_format($subData['total_nilai_aktual'] ?? 0, 0)); ?></td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="text-align: center; border: 1px solid #000000; font-weight: bold;">SUB ELEMEN</td>
                            <td style="border: 1px solid #000000;">-</td>
                        </tr>

                        <?php foreach($sub->kriterias as $kriteria): ?>
                            <?php
                                $detail = $details->firstWhere('kriteria_id', $kriteria->id);
                                $nilaiAktual = $detail ? ($detail->is_na ? 0 : (float)$detail->nilai) : 0;
                                $catatan = $detail ? $detail->catatan : '';
                                $isNa = $detail ? $detail->is_na : false;
                            ?>
                            <tr>
                                <td style="border: 1px solid #000000;"></td>
                                <td style="border: 1px solid #000000;"></td>
                                <td style="border: 1px solid #000000;"></td>
                                <td style="text-align: center; border: 1px solid #000000; font-weight: bold;"><?php echo e($kriteria->kode_kriteria); ?></td>
                                <td style="border: 1px solid #000000;"><?php echo e($kriteria->deskripsi); ?></td>
                                <td style="border: 1px solid #000000;">-</td>
                                <td style="border: 1px solid #000000;">-</td>
                                <td style="text-align: right; border: 1px solid #000000;"><?php echo e(number_format($kriteria->nilai_maksimal, 0)); ?></td>
                                <td style="border: 1px solid #000000;">-</td>
                                <td style="text-align: right; border: 1px solid #000000;"><?php echo e($isNa ? 'N/A' : number_format($nilaiAktual, 0)); ?></td>
                                <td style="border: 1px solid #000000;">-</td>
                                <td style="border: 1px solid #000000;">-</td>
                                <td style="text-align: center; border: 1px solid #000000;"><?php echo e($isNa ? 'N/A' : 'KRITERIA'); ?></td>
                                <td style="border: 1px solid #000000;"><?php echo e($catatan); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
