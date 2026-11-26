<?php

namespace App\Services;

use App\Models\AuditRencana;
use App\Models\AuditSesi;
use App\Models\AuditTahapAwal;
use Carbon\Carbon;
use InvalidArgumentException;

class RencanaAuditService
{
    /**
     * Hitung ukuran sampel dengan rumus Slovin: n = ceil(N / (1 + N * e^2)).
     * Tidak ada nilai bawaan untuk margin error (e); wajib 0 < e < 1.
     */
    public function hitungSlovin(int $populasiN, float $marginErrorE): int
    {
        if ($populasiN < 1) {
            throw new InvalidArgumentException('Populasi (N) untuk rumus Slovin harus bilangan bulat minimal 1.');
        }

        if ($marginErrorE <= 0.0 || $marginErrorE >= 1.0) {
            throw new InvalidArgumentException('Margin error (e) untuk rumus Slovin harus lebih besar dari 0 dan kurang dari 1 (contoh: 0.05).');
        }

        $denominator = 1.0 + ($populasiN * $marginErrorE * $marginErrorE);

        return (int) ceil($populasiN / $denominator);
    }

    /**
     * Validasi baris-baris jadwal (rentang tanggal sesi & jam selesai > jam mulai).
     *
     * @param  array<int, array<string, mixed>>  $jadwals
     * @return array<string, string>
     */
    public function validateJadwals(AuditSesi $sesi, array $jadwals, bool $requireOpeningClosing = false): array
    {
        $errors = [];

        if ($requireOpeningClosing && empty($jadwals)) {
            $errors['jadwals'] = 'Jadwal pelaksanaan audit wajib diisi sebelum finalisasi (minimal memuat rapat pembukaan dan rapat penutupan).';
            return $errors;
        }

        $sesiMulai = $sesi->tanggal_mulai ? Carbon::parse($sesi->tanggal_mulai)->startOfDay() : null;
        $sesiSelesai = $sesi->tanggal_selesai ? Carbon::parse($sesi->tanggal_selesai)->startOfDay() : null;

        $hariSet = [];
        $kegiatanPerHari = [];

        foreach ($jadwals as $idx => $row) {
            $rowNo = $idx + 1;
            $hariKe = isset($row['hari_ke']) ? (int) $row['hari_ke'] : 0;
            $tanggalStr = trim((string) ($row['tanggal'] ?? ''));
            $jamMulai = trim((string) ($row['jam_mulai'] ?? ''));
            $jamSelesai = trim((string) ($row['jam_selesai'] ?? ''));
            $jenis = (string) ($row['jenis_kegiatan'] ?? '');

            if ($hariKe < 1) {
                $errors["jadwals.{$idx}.hari_ke"] = "Jadwal baris #{$rowNo}: Hari ke- harus minimal 1.";
            }

            if ($tanggalStr !== '') {
                try {
                    $tgl = Carbon::parse($tanggalStr)->startOfDay();
                    if ($sesiMulai && $tgl->lt($sesiMulai)) {
                        $errors["jadwals.{$idx}.tanggal"] = "Jadwal baris #{$rowNo}: Tanggal ({$tgl->format('d/m/Y')}) lebih awal dari tanggal mulai sesi audit ({$sesiMulai->format('d/m/Y')}).";
                    }
                    if ($sesiSelesai && $tgl->gt($sesiSelesai)) {
                        $errors["jadwals.{$idx}.tanggal"] = "Jadwal baris #{$rowNo}: Tanggal ({$tgl->format('d/m/Y')}) melewati tanggal selesai sesi audit ({$sesiSelesai->format('d/m/Y')}).";
                    }
                } catch (\Throwable $e) {
                    $errors["jadwals.{$idx}.tanggal"] = "Jadwal baris #{$rowNo}: Format tanggal tidak valid.";
                }
            }

            if ($jamMulai !== '' && $jamSelesai !== '') {
                $mulaiNorm = substr($jamMulai, 0, 5);
                $selesaiNorm = substr($jamSelesai, 0, 5);
                if ($selesaiNorm <= $mulaiNorm) {
                    $errors["jadwals.{$idx}.jam_selesai"] = "Jadwal baris #{$rowNo}: Jam selesai ({$selesaiNorm}) harus setelah jam mulai ({$mulaiNorm}).";
                }
            }

            if ($hariKe >= 1) {
                $hariSet[$hariKe] = true;
                $kegiatanPerHari[$hariKe][] = $jenis;
            }
        }

        if ($requireOpeningClosing && ! empty($hariSet)) {
            $hariPertama = min(array_keys($hariSet));
            $hariTerakhir = max(array_keys($hariSet));

            if (! in_array('rapat_pembukaan', $kegiatanPerHari[$hariPertama] ?? [], true)) {
                $errors['jadwals_opening'] = "Hari pertama jadwal (Hari ke-{$hariPertama}) wajib memuat kegiatan Rapat Pembukaan.";
            }
            if (! in_array('rapat_penutupan', $kegiatanPerHari[$hariTerakhir] ?? [], true)) {
                $errors['jadwals_closing'] = "Hari terakhir jadwal (Hari ke-{$hariTerakhir}) wajib memuat kegiatan Rapat Penutupan.";
            }
        }

        return $errors;
    }

    /**
     * Validasi matriks sampel sesuai aturan materi (slide 29) dan rumus Slovin.
     *
     * @param  array<int, array<string, mixed>>  $sampels
     * @return array{errors: array<string, string>, computed: array<int, array<string, mixed>>}
     */
    public function validateAndNormalizeSampels(array $sampels, bool $isFinalizing = false): array
    {
        $errors = [];
        $computed = [];

        foreach ($sampels as $key => $row) {
            $metode = array_values(array_filter(
                (array) ($row['metode'] ?? []),
                fn ($m) => in_array($m, array_keys(AuditRencana::METODE_OPTIONS), true)
            ));
            $teknik = trim((string) ($row['teknik_sampling'] ?? '')) ?: null;
            $penentuan = trim((string) ($row['metode_penentuan'] ?? '')) ?: null;
            $jenisData = trim((string) ($row['jenis_data'] ?? '')) ?: null;
            $populasiN = ($row['populasi_n'] ?? '') !== '' && $row['populasi_n'] !== null ? (int) $row['populasi_n'] : null;
            $marginE = ($row['margin_error_e'] ?? '') !== '' && $row['margin_error_e'] !== null ? (float) $row['margin_error_e'] : null;
            $jumlahSampel = ($row['jumlah_sampel'] ?? '') !== '' && $row['jumlah_sampel'] !== null ? (int) $row['jumlah_sampel'] : null;
            $deskripsi = trim((string) ($row['deskripsi_sampel'] ?? '')) ?: null;

            $isTouched = ! empty($metode) || $teknik !== null || $penentuan !== null || $jenisData !== null
                || $populasiN !== null || $marginE !== null || $jumlahSampel !== null || $deskripsi !== null;

            // Slovin calculation when metode_penentuan = slovin
            if ($penentuan === 'slovin') {
                if ($populasiN === null || $populasiN < 1) {
                    $errors["sampels.{$key}.populasi_n"] = "Sampel #{$key}: Populasi (N) wajib diisi minimal 1 untuk metode Slovin.";
                }
                if ($marginE === null || $marginE <= 0.0 || $marginE >= 1.0) {
                    $errors["sampels.{$key}.margin_error_e"] = "Sampel #{$key}: Margin error (e) wajib diisi dengan nilai 0 < e < 1 untuk metode Slovin.";
                }
                if ($populasiN !== null && $populasiN >= 1 && $marginE !== null && $marginE > 0.0 && $marginE < 1.0) {
                    $slovinVal = $this->hitungSlovin($populasiN, $marginE);
                    // Isi otomatis jika jumlah_sampel kosong; jika auditor mengisi manual, tetap hormati manual
                    if ($jumlahSampel === null) {
                        $jumlahSampel = $slovinVal;
                    }
                }
            }

            // Aturan slide 29: jika metode atau teknik sampling diisi, deskripsi_sampel wajib & jumlah_sampel >= 1
            if (! empty($metode) || $teknik !== null) {
                if ($deskripsi === null) {
                    $errors["sampels.{$key}.deskripsi_sampel"] = "Sampel #{$key}: Deskripsi sampel wajib diisi bila metode atau teknik sampling dipilih (sampel harus spesifik dan terukur).";
                }
                if ($jumlahSampel === null || $jumlahSampel < 1) {
                    $errors["sampels.{$key}.jumlah_sampel"] = "Sampel #{$key}: Jumlah sampel wajib bilangan bulat minimal 1 bila metode atau teknik sampling dipilih.";
                }
            } elseif ($isFinalizing && $isTouched) {
                if ($jumlahSampel !== null && $jumlahSampel < 1) {
                    $errors["sampels.{$key}.jumlah_sampel"] = "Sampel #{$key}: Jumlah sampel minimal 1.";
                }
            }

            $computed[$key] = [
                'jenis_data'       => $jenisData,
                'metode'           => ! empty($metode) ? $metode : null,
                'teknik_sampling'  => $teknik,
                'metode_penentuan' => $penentuan,
                'populasi_n'       => $populasiN,
                'margin_error_e'   => $marginE !== null ? round($marginE, 3) : null,
                'jumlah_sampel'    => $jumlahSampel,
                'deskripsi_sampel' => $deskripsi,
            ];
        }

        return [
            'errors'   => $errors,
            'computed' => $computed,
        ];
    }

    /**
     * Validasi keseluruhan saat finalisasi Rencana Audit.
     *
     * @return array<string, string>
     */
    public function validateForFinalization(AuditRencana $rencana): array
    {
        $rencana->loadMissing(['auditSesi', 'tims', 'jadwals', 'sampels']);
        $errors = [];

        // 1. Tepat satu Lead Auditor
        $leadCount = $rencana->tims->where('peran', 'lead_auditor')->count();
        if ($leadCount !== 1) {
            $errors['tims'] = "Rencana Audit wajib memiliki tepat 1 anggota tim dengan peran Ketua Tim (Lead Auditor). Saat ini terdapat {$leadCount}.";
        }

        // 2. Jadwal lengkap (rapat pembukaan hari 1 & rapat penutupan hari terakhir + rentang sesi)
        $jadwalRows = $rencana->jadwals->map(fn ($j) => [
            'hari_ke'        => $j->hari_ke,
            'tanggal'        => $j->tanggal ? $j->tanggal->format('Y-m-d') : '',
            'jam_mulai'      => $j->jam_mulai,
            'jam_selesai'    => $j->jam_selesai,
            'jenis_kegiatan' => $j->jenis_kegiatan,
        ])->all();
        $errors = array_merge($errors, $this->validateJadwals($rencana->auditSesi, $jadwalRows, true));

        // 3. Validasi sampel yang disentuh
        $sampelRows = [];
        foreach ($rencana->sampels as $s) {
            $sampelRows[$s->id] = [
                'jenis_data'       => $s->jenis_data,
                'metode'           => $s->metode ?? [],
                'teknik_sampling'  => $s->teknik_sampling,
                'metode_penentuan' => $s->metode_penentuan,
                'populasi_n'       => $s->populasi_n,
                'margin_error_e'   => $s->margin_error_e,
                'jumlah_sampel'    => $s->jumlah_sampel,
                'deskripsi_sampel' => $s->deskripsi_sampel,
            ];
        }
        $sampelCheck = $this->validateAndNormalizeSampels($sampelRows, true);
        $errors = array_merge($errors, $sampelCheck['errors']);

        return $errors;
    }

    /**
     * Cek peringatan (non-blocking) jika total hari pada jadwal melebihi total_mandays Tahap Awal.
     */
    public function getMandaysWarning(AuditRencana $rencana): ?string
    {
        $tahapAwal = AuditTahapAwal::where('audit_sesi_id', $rencana->audit_sesi_id)->first();
        if (! $tahapAwal || $tahapAwal->total_mandays === null) {
            return null;
        }

        $totalHariJadwal = $rencana->jadwals()->distinct('hari_ke')->count('hari_ke');
        $totalMandays = (float) $tahapAwal->total_mandays;

        if ($totalHariJadwal > 0 && $totalHariJadwal > $totalMandays) {
            return "Peringatan: Total hari pada jadwal ({$totalHariJadwal} hari) melebihi Total Mandays pada Dokumen Tahap Awal (" . number_format($totalMandays, 1) . " hari).";
        }

        return null;
    }
}
