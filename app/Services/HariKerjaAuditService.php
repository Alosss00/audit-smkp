<?php

namespace App\Services;

use App\Models\MasterHariKerjaAudit;
use DomainException;

class HariKerjaAuditService
{
    /**
     * Hitung hari kerja audit (mandays) berdasarkan tabel acuan & faktor penyesuaian
     * yang disinkronkan dengan tabel 7 Kondisi Kepdirjen 185 pada Formulir Tahap Awal.
     *
     * @param  int               $jumlahPekerja
     * @param  string            $kelasRisiko
     * @param  float             $faktorPenyesuaianHari
     * @param  array<int, int>   $kondisiYa Nomor kondisi (1..7) yang bernilai "Ya"
     * @return array<string, mixed>
     *
     * @throws DomainException
     */
    public function hitung(
        int $jumlahPekerja,
        string $kelasRisiko,
        float $faktorPenyesuaianHari = 0.0,
        array $kondisiYa = []
    ): array {
        if ($jumlahPekerja < 1) {
            throw new DomainException('Jumlah pekerja harus minimal 1 orang.');
        }

        $kelasNormalized = $this->normalizeKelasRisiko($kelasRisiko);
        if ($kelasNormalized === null) {
            throw new DomainException('Kelas risiko tidak valid. Gunakan Tinggi, Menengah (Sedang), atau Rendah.');
        }

        $barisAcuan = MasterHariKerjaAudit::query()
            ->where('jumlah_min', '<=', $jumlahPekerja)
            ->where(function ($q) use ($jumlahPekerja) {
                $q->whereNull('jumlah_max')
                    ->orWhere('jumlah_max', '>=', $jumlahPekerja);
            })
            ->orderBy('jumlah_min')
            ->first();

        if (! $barisAcuan) {
            throw new DomainException("Tidak ditemukan baris acuan hari kerja untuk jumlah pekerja {$jumlahPekerja}. Silakan lengkapi Master Hari Kerja Audit terlebih dahulu.");
        }

        $mandaysAwal = match ($kelasNormalized) {
            'Tinggi'   => (float) $barisAcuan->hari_tinggi,
            'Menengah' => (float) $barisAcuan->hari_menengah,
            'Rendah'   => (float) $barisAcuan->hari_rendah,
        };

        $cleanKondisiYa = array_values(array_unique(array_filter(
            array_map('intval', $kondisiYa),
            fn (int $no) => $no >= 1 && $no <= 7
        )));
        sort($cleanKondisiYa);

        $faktorHari = round($faktorPenyesuaianHari, 1);
        $totalMandays = round(max(0.0, $mandaysAwal + $faktorHari), 1);

        $warnings = [];
        if (! $barisAcuan->is_verified) {
            $warnings[] = "Baris acuan ({$barisAcuan->rentang_label}) belum berstatus terverifikasi oleh admin.";
        }
        if (! empty($cleanKondisiYa) && abs($faktorHari) < 0.001) {
            $warnings[] = 'Terdapat ' . count($cleanKondisiYa) . ' kondisi penyesuaian Kepdirjen 185 bernilai "Ya" (No. ' . implode(', ', $cleanKondisiYa) . '); silakan isi penambahan/pengurangan pada kolom Faktor Penyesuaian (Hari) bila diperlukan.';
        }

        return [
            'mandays_awal'   => round($mandaysAwal, 1),
            'faktor_hari'    => $faktorHari,
            'total_mandays'  => $totalMandays,
            'baris_acuan_id' => $barisAcuan->id,
            'rentang_label'  => $barisAcuan->rentang_label,
            'is_verified'    => (bool) $barisAcuan->is_verified,
            'warning'        => ! empty($warnings) ? 'Perhatian: ' . implode(' ', $warnings) : null,
            'kondisi_ya'     => $cleanKondisiYa,
        ];
    }

    /**
     * Cek apakah rentang [min, max] bertumpuk dengan rentang aktif yang sudah ada.
     */
    public function findOverlappingRange(int $min, ?int $max, ?int $ignoreId = null): ?MasterHariKerjaAudit
    {
        $query = MasterHariKerjaAudit::query();
        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->where(function ($q) use ($min, $max) {
            if ($max === null) {
                $q->whereNull('jumlah_max')
                    ->orWhere('jumlah_max', '>=', $min);
            } else {
                $q->where('jumlah_min', '<=', $max)
                    ->where(function ($sub) use ($min) {
                        $sub->whereNull('jumlah_max')
                            ->orWhere('jumlah_max', '>=', $min);
                    });
            }
        })->orderBy('jumlah_min')->first();
    }

    /**
     * Cek tumpang tindih dua rentang [minA, maxA] dan [minB, maxB].
     */
    public function rangesOverlap(int $minA, ?int $maxA, int $minB, ?int $maxB): bool
    {
        $aEndsBeforeB = ($maxA !== null && $maxA < $minB);
        $bEndsBeforeA = ($maxB !== null && $maxB < $minA);

        return ! $aEndsBeforeB && ! $bEndsBeforeA;
    }

    /**
     * Normalisasi label kelas risiko ke Tinggi | Menengah | Rendah.
     */
    public function normalizeKelasRisiko(string $kelasRisiko): ?string
    {
        return match (strtolower(trim($kelasRisiko))) {
            'tinggi'             => 'Tinggi',
            'menengah', 'sedang' => 'Menengah',
            'rendah'             => 'Rendah',
            default              => null,
        };
    }
}
