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
            <td style="font-weight: bold;" colspan="2">Area / Departemen Audit:</td>
            <td colspan="11">{{ $sesi->area_audit }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold;" colspan="2">Tanggal Audit:</td>
            <td colspan="11">{{ $sesi->tanggal_audit->format('d F Y') }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold;" colspan="2">Auditor Pelaksana:</td>
            <td colspan="11">{{ $sesi->user->name }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold;" colspan="2">Status Sesi Audit:</td>
            <td colspan="11">{{ strtoupper($sesi->status) }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-weight: bold;" colspan="2">Pencapaian Skor Akhir (%):</td>
            <td style="font-weight: bold;" colspan="11">{{ number_format($skorAkhir, 2) }}%</td>
        </tr>
        <tr></tr>

        <!-- Table Header (Matching TT-MGT-FRS-026B Columns B to P) -->
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
            @php $no = 1; @endphp
            @foreach($elemens as $elemen)
                @php
                    $elemenData = collect($rekapElemen)->firstWhere('elemen_id', $elemen->id);
                @endphp
                <!-- LEVEL 1: BARIS ELEMEN -->
                <tr style="background-color: #cbd5e1; font-weight: bold;">
                    <td style="text-align: center; border: 1px solid #000000; font-weight: bold;">{{ $no++ }}</td>
                    <td style="text-align: center; border: 1px solid #000000; font-weight: bold;">{{ $elemen->kode_elemen }}</td>
                    <td style="border: 1px solid #000000; font-weight: bold;" colspan="2">ELEMEN {{ $elemen->kode_elemen }}: {{ $elemen->nama_elemen }}</td>
                    <td style="border: 1px solid #000000;">-</td>
                    <td style="text-align: right; border: 1px solid #000000; font-weight: bold;">{{ number_format($elemen->bobot, 2) }}%</td>
                    <td style="text-align: right; border: 1px solid #000000; font-weight: bold;">{{ number_format($elemenData['total_nilai_maks_efektif'] ?? 0, 2) }}</td>
                    <td style="border: 1px solid #000000;">-</td>
                    <td style="border: 1px solid #000000;">-</td>
                    <td style="border: 1px solid #000000;">-</td>
                    <td style="text-align: right; border: 1px solid #000000; font-weight: bold;">{{ number_format($elemenData['total_nilai_aktual'] ?? 0, 2) }}</td>
                    <td style="text-align: right; border: 1px solid #000000; font-weight: bold;">{{ number_format($elemenData['persentase'] ?? 0, 2) }}%</td>
                    <td style="text-align: center; border: 1px solid #000000; font-weight: bold;">ELEMEN</td>
                    <td style="border: 1px solid #000000;">-</td>
                </tr>

                @foreach($elemen->subElemens as $sub)
                    @php
                        $subData = $rekapSub[$sub->id] ?? null;
                    @endphp
                    <!-- LEVEL 2: BARIS SUB ELEMEN -->
                    <tr style="background-color: #f1f5f9; font-weight: bold;">
                        <td style="border: 1px solid #000000;"></td>
                        <td style="border: 1px solid #000000;"></td>
                        <td style="border: 1px solid #000000; font-weight: bold;">{{ $sub->nama_sub }}</td>
                        <td style="text-align: center; border: 1px solid #000000; font-weight: bold;">{{ $sub->kode_sub }}</td>
                        <td style="border: 1px solid #000000;">-</td>
                        <td style="border: 1px solid #000000;">-</td>
                        <td style="text-align: right; border: 1px solid #000000; font-weight: bold;">{{ number_format($subData['total_nilai_maks_efektif'] ?? 0, 2) }}</td>
                        <td style="border: 1px solid #000000;">-</td>
                        <td style="text-align: right; border: 1px solid #000000; font-weight: bold;">{{ number_format($subData['total_nilai_aktual'] ?? 0, 2) }}</td>
                        <td style="border: 1px solid #000000;">-</td>
                        <td style="border: 1px solid #000000;">-</td>
                        <td style="border: 1px solid #000000;">-</td>
                        <td style="text-align: center; border: 1px solid #000000; font-weight: bold;">SUB ELEMEN</td>
                        <td style="border: 1px solid #000000;">-</td>
                    </tr>

                    @foreach($sub->kriterias as $kriteria)
                        @php
                            $detail = $details->firstWhere('kriteria_id', $kriteria->id);
                            $nilaiAktual = $detail ? ($detail->is_na ? 0 : (float)$detail->nilai) : 0;
                            $catatan = $detail ? $detail->catatan : '';
                            $isNa = $detail ? $detail->is_na : false;
                        @endphp
                        <!-- LEVEL 3: BARIS SUB-SUB ELEMEN / KRITERIA PENILAIAN -->
                        <tr>
                            <td style="border: 1px solid #000000;"></td>
                            <td style="border: 1px solid #000000;"></td>
                            <td style="border: 1px solid #000000;"></td>
                            <td style="text-align: center; border: 1px solid #000000; font-weight: bold;">{{ $kriteria->kode_kriteria }}</td>
                            <td style="border: 1px solid #000000;">{{ $kriteria->deskripsi }}</td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="text-align: right; border: 1px solid #000000;">{{ number_format($kriteria->nilai_maksimal, 2) }}</td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="text-align: right; border: 1px solid #000000;">{{ $isNa ? 'N/A' : number_format($nilaiAktual, 2) }}</td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="border: 1px solid #000000;">-</td>
                            <td style="text-align: center; border: 1px solid #000000;">{{ $isNa ? 'N/A' : 'KRITERIA' }}</td>
                            <td style="border: 1px solid #000000;">{{ $catatan }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
