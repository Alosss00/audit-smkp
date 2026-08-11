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
        'tanggal_audit',
        'area_audit',
        'status',
        'skor_akhir',
    ];

    protected $casts = [
        'tanggal_audit' => 'date',
        'skor_akhir' => 'decimal:2',
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
        // Load details with kriteria and hierarchy
        $details = $this->auditDetails()->with('kriteria.subElemen.elemen')->get();
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
