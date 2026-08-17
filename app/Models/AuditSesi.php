<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditSesi extends Model
{
    use HasFactory;

    protected $table = 'audit_sesis';

    protected $fillable = [
        'user_id',
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
                'elemen_id' => $elemen->id,
                'kode_elemen' => $elemen->kode_elemen,
                'nama_elemen' => $elemen->nama_elemen,
                'bobot' => (float) $elemen->bobot,
                'total_nilai_aktual' => round($totalNilaiAktual, 2),
                'total_nilai_maks_efektif' => round($totalNilaiMaksEfektif, 2),
                'persentase' => round($persentase, 2),
                'skor_elemen' => round($skorElemen, 2),
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
                'sub_elemen_id' => $sub->id,
                'kode_sub' => $sub->kode_sub,
                'nama_sub' => $sub->nama_sub,
                'total_nilai_aktual' => round($totalNilaiAktual, 2),
                'total_nilai_maks_efektif' => round($totalNilaiMaksEfektif, 2),
                'persentase' => round($persentase, 2),
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
            : $this->auditDetails()->with('kriteria.subElemen.elemen')->get();

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
                        'nilai'          => (float) $d->nilai,
                        'nilai_maksimal' => (float) ($d->kriteria->nilai_maksimal ?? 4),
                        'is_na'          => (bool) $d->is_na,
                        'catatan'        => $d->catatan,
                        'lampiran_url'   => $d->lampiran_url,
                    ];
                }

                $subPct = $subMaks > 0 ? ($subAktual / $subMaks) * 100 : 0;

                $elAktual += $subAktual;
                $elMaks   += $subMaks;

                $subList[] = [
                    'sub_elemen_id'            => $sub->id,
                    'kode_sub'                 => $sub->kode_sub,
                    'nama_sub'                 => $sub->nama_sub,
                    'total_nilai_aktual'       => round($subAktual, 2),
                    'total_nilai_maks_efektif' => round($subMaks, 2),
                    'persentase'               => round($subPct, 2),
                    'details'                  => $subDetails,
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
                'total_nilai_aktual'       => round($elAktual, 2),
                'total_nilai_maks_efektif' => round($elMaks, 2),
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
}
