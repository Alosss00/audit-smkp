<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditSesi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'audit_sesis';

    protected $fillable = [
        'user_id',
        'perusahaan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'area_audit',
        'status',
        'skor_akhir',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'skor_akhir'      => 'decimal:2',
    ];

    /**
     * Relationship to User (Auditor).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship to Perusahaan.
     */
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
    }

    /**
     * Relationship to AuditDetail.
     */
    public function auditDetails()
    {
        return $this->hasMany(AuditDetail::class, 'audit_sesi_id');
    }

    /**
     * Calculate rekap score per element based on SMKP Minerba rules.
     *
     * Rules:
     * - Formula per elemen: (Total Nilai Aktual / Total Nilai Maksimal Efektif) x Bobot Elemen
     * - If is_na = true: nilai is 0, and kriteria's nilai_maksimal is NOT included in denominator.
     * - Division by zero: if Total Nilai Maksimal Efektif = 0, percentage is 0 (and weighted score is 0).
     *
     * @return array
     */
    public function getRekapPerElemen(): array
    {
        // Load details with kriteria and hierarchy, reusing preloaded relationship if available
        $details = $this->relationLoaded('auditDetails')
            ? $this->auditDetails
            : $this->auditDetails()->with('kriteria.subElemen.elemen')->get();

        $elemens = Elemen::orderBy('kode_elemen')->get();

        $rekap = [];

        foreach ($elemens as $elemen) {
            $totalNilaiAktual = 0;
            $totalNilaiMaksEfektif = 0;

            // Filter details belonging to this element
            $elementDetails = $details->filter(function ($detail) use ($elemen) {
                return $detail->kriteria
                    && $detail->kriteria->subElemen
                    && $detail->kriteria->subElemen->elemen_id == $elemen->id;
            });

            foreach ($elementDetails as $detail) {
                if (!$detail->is_na) {
                    $totalNilaiAktual += (float) $detail->nilai;
                    $totalNilaiMaksEfektif += (float) $detail->kriteria->nilai_maksimal;
                }
            }

            if ($totalNilaiMaksEfektif > 0) {
                $persentase = ($totalNilaiAktual / $totalNilaiMaksEfektif) * 100;
                $skorElemen = ($totalNilaiAktual / $totalNilaiMaksEfektif) * (float) $elemen->bobot;
            } else {
                $persentase = 0;
                $skorElemen = 0;
            }

            $rekap[] = [
                'elemen_id'                => $elemen->id,
                'kode_elemen'              => $elemen->kode_elemen,
                'nama_elemen'              => $elemen->nama_elemen,
                'bobot'                    => (float) $elemen->bobot,
                'total_nilai_aktual'       => (int) round($totalNilaiAktual),
                'total_nilai_maks_efektif' => (int) round($totalNilaiMaksEfektif),
                'persentase'               => round($persentase, 2),
                'skor_elemen'              => round($skorElemen, 2),
            ];
        }

        return $rekap;
    }

    /**
     * Calculate rekap score per sub-element for Excel export aggregation.
     *
     * @return array Keyed by sub_elemen_id
     */
    public function getRekapPerSubElemen(): array
    {
        $details = $this->relationLoaded('auditDetails')
            ? $this->auditDetails
            : $this->auditDetails()->with('kriteria.subElemen')->get();
        $subElemens = SubElemen::orderBy('kode_sub')->get();

        $rekap = [];

        foreach ($subElemens as $sub) {
            $totalNilaiAktual = 0;
            $totalNilaiMaksEfektif = 0;

            $subDetails = $details->filter(function ($detail) use ($sub) {
                return $detail->kriteria && $detail->kriteria->sub_elemen_id == $sub->id;
            });

            foreach ($subDetails as $detail) {
                if (!$detail->is_na) {
                    $totalNilaiAktual += (float) $detail->nilai;
                    $totalNilaiMaksEfektif += (float) $detail->kriteria->nilai_maksimal;
                }
            }

            $persentase = $totalNilaiMaksEfektif > 0 ? ($totalNilaiAktual / $totalNilaiMaksEfektif) * 100 : 0;

            $rekap[$sub->id] = [
                'sub_elemen_id'            => $sub->id,
                'kode_sub'                 => $sub->kode_sub,
                'nama_sub'                 => $sub->nama_sub,
                'total_nilai_aktual'       => (int) round($totalNilaiAktual),
                'total_nilai_maks_efektif' => (int) round($totalNilaiMaksEfektif),
                'persentase'               => round($persentase, 2),
            ];
        }

        return $rekap;
    }

    /**
     * Get multi-level hierarchical breakdown (Elemen -> Sub-Elemen -> Sub-Sub Elemen / Kriteria).
     *
     * @return array
     */
    public function getRekapHierarkis(): array
    {
        $details = $this->relationLoaded('auditDetails')
            ? $this->auditDetails
            : $this->auditDetails()->with(['kriteria.subElemen.elemen', 'pica'])->get();

        $elemens = Elemen::with(['subElemens.kriterias'])->orderBy('kode_elemen')->get();

        $hierarkis = [];

        foreach ($elemens as $elemen) {
            $elAktual = 0;
            $elMaks   = 0;
            $subList  = [];

            foreach ($elemen->subElemens as $sub) {
                $subAktual = 0;
                $subMaks   = 0;
                $subDetails = [];

                // Filter details for this sub-element
                $matchedDetails = $details->filter(function ($d) use ($sub) {
                    return $d->kriteria && $d->kriteria->sub_elemen_id == $sub->id;
                });

                foreach ($matchedDetails as $d) {
                    if (!$d->is_na) {
                        $subAktual += (float) $d->nilai;
                        $subMaks   += (float) ($d->kriteria->nilai_maksimal ?? 4);
                    }

                    $subDetails[] = [
                        'id'             => $d->id,
                        'kriteria_id'    => $d->kriteria_id,
                        'kode_kriteria'  => $d->kriteria->kode_kriteria ?? '-',
                        'deskripsi'      => $d->kriteria->deskripsi ?? '-',
                        'nama_kriteria'  => $d->kriteria->deskripsi ?? '-',
                        'nilai'          => (int) round($d->nilai),
                        'nilai_aktual'   => (int) round($d->nilai),
                        'nilai_maksimal' => (int) round($d->kriteria->nilai_maksimal ?? 4),
                        'is_na'          => (bool) $d->is_na,
                        'catatan'        => $d->catatan,
                        'lampiran_url'   => $d->lampiran_url,
                        'lampiran_urls'  => $d->lampiran_urls,
                        'has_pica'       => (bool) $d->pica,
                        'pica_kategori'  => $d->pica ? $d->pica->kategori_temuan : null,
                    ];
                }

                $subPct = $subMaks > 0 ? ($subAktual / $subMaks) * 100 : 0;

                $elAktual += $subAktual;
                $elMaks   += $subMaks;

                $isDirect = count($subDetails) === 1;

                $subList[] = [
                    'sub_elemen_id'            => $sub->id,
                    'kode_sub'                 => $sub->kode_sub,
                    'kode_sub_elemen'          => $sub->kode_sub,
                    'nama_sub'                 => $sub->nama_sub,
                    'nama_sub_elemen'          => $sub->nama_sub,
                    'bobot'                    => (float) ($sub->bobot ?? 0),
                    'total_nilai_aktual'       => (int) round($subAktual),
                    'nilai_aktual'             => (int) round($subAktual),
                    'total_nilai_maks_efektif' => (int) round($subMaks),
                    'nilai_maks_efektif'       => (int) round($subMaks),
                    'persentase'               => round($subPct, 2),
                    'details'                  => $subDetails,
                    'kriterias'                => $subDetails,
                    'is_direct'                => $isDirect,
                    'direct_detail'            => $isDirect ? ($subDetails[0] ?? null) : null,
                ];
            }

            if ($elMaks > 0) {
                $persentase = ($elAktual / $elMaks) * 100;
                $skorElemen = ($elAktual / $elMaks) * (float) $elemen->bobot;
            } else {
                $persentase = 0;
                $skorElemen = 0;
            }

            $hierarkis[] = [
                'elemen_id'                => $elemen->id,
                'kode_elemen'              => $elemen->kode_elemen,
                'nama_elemen'              => $elemen->nama_elemen,
                'bobot'                    => (float) $elemen->bobot,
                'total_nilai_aktual'       => (int) round($elAktual),
                'nilai_aktual'             => (int) round($elAktual),
                'total_nilai_maks_efektif' => (int) round($elMaks),
                'nilai_maks_efektif'       => (int) round($elMaks),
                'persentase'               => round($persentase, 2),
                'skor_elemen'              => round($skorElemen, 2),
                'sub_elemens'              => $subList,
            ];
        }

        return $hierarkis;
    }

    /**
     * Recalculate and update `skor_akhir` on the audit session.
     *
     * @return float
     */
    public function hitungSkorAkhir(): float
    {
        $rekap = $this->getRekapPerElemen();
        $totalSkor = array_sum(array_column($rekap, 'skor_elemen'));

        $finalScore = round($totalSkor, 2);

        $this->skor_akhir = $finalScore;
        $this->save();

        return $finalScore;
    }

    /**
     * Path #1 klasifikasi Mayor: persentase nilai sub elemen < 50% dari maksimalnya.
     * PENTING: pakai strict less-than, bukan <=. Nilai tepat 50% = Minor (dikonfirmasi
     * dari contoh resmi materi Kepdirjen 185: 10/20 = 50% dikategorikan Minor).
     */
    public function hitungKategoriMayorPath1(int $subElemenId): string
    {
        $rekapSubElemen = $this->getRekapPerSubElemen();
        $data = $rekapSubElemen[$subElemenId] ?? null;

        if (!$data || $data['total_nilai_maks_efektif'] <= 0) {
            return 'minor';
        }

        $persentase = $data['total_nilai_aktual'] / $data['total_nilai_maks_efektif'];

        return $persentase < 0.5 ? 'mayor' : 'minor';
    }

    /**
     * Build reusable tree structure of Elemen -> SubElemen -> Kriteria.
     */
    public function buildMatrixTree(): array
    {
        return $this->getRekapHierarkis();
    }

    /**
     * Get list of sub elements with 100% compliance ("Praktik Terbaik").
     */
    public function getSubElemenPraktekTerbaik(): \Illuminate\Support\Collection
    {
        $rekapSubElemen = $this->getRekapPerSubElemen();

        return collect($rekapSubElemen)
            ->filter(fn ($data) =>
                $data['total_nilai_maks_efektif'] > 0 &&
                $data['total_nilai_aktual'] == $data['total_nilai_maks_efektif']
            )
            ->map(function ($data) {
                $subElemen = SubElemen::with('elemen')->find($data['sub_elemen_id']);
                $catatanList = $this->auditDetails()
                    ->whereHas('kriteria', fn ($q) => $q->where('sub_elemen_id', $data['sub_elemen_id']))
                    ->whereNotNull('catatan')
                    ->where('catatan', '!=', '')
                    ->pluck('catatan');

                return [
                    'sub_elemen_id'      => $data['sub_elemen_id'],
                    'kode_sub'           => $data['kode_sub'] ?? ($subElemen->kode_sub ?? '-'),
                    'kode_sub_elemen'    => $data['kode_sub'] ?? ($subElemen->kode_sub ?? '-'),
                    'nama_sub'           => $data['nama_sub'] ?? ($subElemen->nama_sub ?? '-'),
                    'nama_sub_elemen'    => $data['nama_sub'] ?? ($subElemen->nama_sub ?? '-'),
                    'elemen_kode'        => $subElemen->elemen->kode_elemen ?? '-',
                    'elemen_nama'        => $subElemen->elemen->nama_elemen ?? '-',
                    'nama_elemen'        => $subElemen->elemen->nama_elemen ?? '-',
                    'nilai_aktual'       => $data['total_nilai_aktual'],
                    'nilai_maks_efektif' => $data['total_nilai_maks_efektif'],
                    'nilai_maks'         => $data['total_nilai_maks_efektif'],
                    'nilai_label'        => $data['total_nilai_aktual'] . ' / ' . $data['total_nilai_maks_efektif'],
                    'persentase'         => 100.0,
                    'catatan'            => $catatanList->isNotEmpty() ? $catatanList->toArray() : 'Kesesuaian penuh memenuhi standar evaluasi SMKP Minerba Kepdirjen 185.',
                    'keterangan'         => $catatanList->isNotEmpty() ? $catatanList->implode(' | ') : 'Kesesuaian penuh memenuhi standar evaluasi SMKP Minerba Kepdirjen 185.',
                ];
            })
            ->values();
    }

    /**
     * Get findings grouped by category (kritikal, mayor, minor).
     * Rule: Multi-criteria findings within the same sub-element count as 1 sub-element finding for summary statistics,
     * while preserving all individual criteria finding items for PICA remediation execution.
     */
    public function getTemuanPerKategori(): array
    {
        $picas = Pica::whereHas('auditDetail', fn ($q) => $q->where('audit_sesi_id', $this->id))
            ->with(['auditDetail.kriteria.subElemen.elemen'])
            ->get();

        // Group PICA records by sub_elemen_id
        $groupedBySubElemen = $picas->groupBy(function ($pica) {
            return $pica->auditDetail->kriteria->sub_elemen_id ?? 0;
        });

        $kritikalItems = collect();
        $mayorItems    = collect();
        $minorItems    = collect();

        $kritikalSubCount = 0;
        $mayorSubCount    = 0;
        $minorSubCount    = 0;

        foreach ($groupedBySubElemen as $subElemenId => $subPicas) {
            // Determine severity category for this sub-element:
            // 1. Kritikal if any item is kritikal
            // 2. Mayor if any item is mayor or sub-elemen score < 50%
            // 3. Minor otherwise
            $hasKritikal = $subPicas->contains('kategori_temuan', 'kritikal');
            $hasMayor    = $subPicas->contains('kategori_temuan', 'mayor');

            if ($hasKritikal) {
                $kritikalSubCount++;
                $kritikalItems = $kritikalItems->concat($subPicas);
            } elseif ($hasMayor) {
                $mayorSubCount++;
                $mayorItems = $mayorItems->concat($subPicas);
            } else {
                $minorSubCount++;
                $minorItems = $minorItems->concat($subPicas);
            }
        }

        return [
            'kritikal'           => $kritikalItems->values(),
            'mayor'              => $mayorItems->values(),
            'minor'              => $minorItems->values(),
            'total_items'        => $picas->count(),
            'total_sub_temuan'   => $groupedBySubElemen->count(),
            'total_temuan'       => $groupedBySubElemen->count(),
            'kritikal_count'     => $kritikalSubCount,
            'mayor_count'        => $mayorSubCount,
            'minor_count'        => $minorSubCount,
        ];
    }

    /**
     * Hitung persentase progres pengisian matriks penilaian sesi audit.
     *
     * @return float
     */
    public function hitungProgressPenilaian(): float
    {
        if ($this->status === 'selesai') {
            return 100.0;
        }

        $details = $this->relationLoaded('auditDetails')
            ? $this->auditDetails
            : $this->auditDetails()->get();

        $totalKriteria = $details->count();
        if ($totalKriteria === 0) {
            return 0.0;
        }

        $assessedCount = $details->filter(function ($d) {
            return (float) $d->nilai > 0 || $d->is_na || !empty($d->catatan) || !empty($d->lampiran);
        })->count();

        return round(($assessedCount / $totalKriteria) * 100, 1);
    }
}
