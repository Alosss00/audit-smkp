<?php

namespace App\Services;

use App\Models\MasterFaktorPenyesuaian;
use App\Models\MasterHariKerjaAudit;
use DomainException;

class HariKerjaAuditService
{
    /**
     * Hitung hari kerja audit (mandays) berdasarkan tabel acuan & faktor penyesuaian.
     *
     * @param  int   $jumlahPekerja
     * @param  string $kelasRisiko
     * @param  array<int, int|string> $faktorIds
     * @return array<string, mixed>
     *
     * @throws DomainException
     */
    public function hitung(int $jumlahPekerja, string $kelasRisiko, array $faktorIds = []): array
    {
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

        $cleanFaktorIds = array_values(array_unique(array_filter(array_map('intval', $faktorIds), fn (int $id) => $id > 0)));
        $faktorHari = 0.0;
        $faktorTerpilih = [];

        if (! empty($cleanFaktorIds)) {
            $faktors = MasterFaktorPenyesuaian::query()
                ->where('is_active', true)
                ->whereIn('id', $cleanFaktorIds)
                ->orderBy('urutan')
                ->get();

            if ($faktors->count() !== count($cleanFaktorIds)) {
                throw new DomainException('Sebagian faktor penyesuaian yang dipilih tidak ditemukan atau tidak aktif.');
            }

            $missingValues = [];
            foreach ($faktors as $faktor) {
                if ($faktor->nilai_hari === null) {
                    $missingValues[] = "{$faktor->kode} ({$faktor->nama})";
                    continue;
                }

                $nilai = (float) $faktor->nilai_hari;
                if ($faktor->tipe === 'tambah') {
                    $faktorHari += $nilai;
                } else {
                    $faktorHari -= $nilai;
                }

                $faktorTerpilih[] = [
                    'id'         => $faktor->id,
                    'kode'       => $faktor->kode,
                    'nama'       => $faktor->nama,
                    'tipe'       => $faktor->tipe,
                    'nilai_hari' => round($nilai, 1),
                ];
            }

            if (! empty($missingValues)) {
                throw new DomainException(
                    'Nilai hari belum ditetapkan pada faktor penyesuaian: ' . implode('; ', $missingValues) . '. Silakan isi nilai_hari di Master Faktor Penyesuaian terlebih dahulu.'
                );
            }
        }

        $totalMandays = round(max(0.0, $mandaysAwal + $faktorHari), 1);
        $warning = null;
        if (! $barisAcuan->is_verified) {
            $warning = "Perhatian: Baris acuan ({$barisAcuan->rentang_label}) belum berstatus terverifikasi oleh admin.";
        }

        return [
            'mandays_awal'    => round($mandaysAwal, 1),
            'faktor_hari'     => round($faktorHari, 1),
            'total_mandays'   => $totalMandays,
            'baris_acuan_id'  => $barisAcuan->id,
            'rentang_label'   => $barisAcuan->rentang_label,
            'is_verified'     => (bool) $barisAcuan->is_verified,
            'warning'         => $warning,
            'faktor_terpilih' => $faktorTerpilih,
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
                // New range is [min, +inf): overlaps if existing max is null OR existing max >= min
                $q->whereNull('jumlah_max')
                    ->orWhere('jumlah_max', '>=', $min);
            } else {
                // New range is [min, max]: overlaps if existing min <= max AND (existing max is null OR existing max >= min)
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
            'tinggi'            => 'Tinggi',
            'menengah', 'sedang' => 'Menengah',
            'rendah'            => 'Rendah',
            default             => null,
        };
    }
}
