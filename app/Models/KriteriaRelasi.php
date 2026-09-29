<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model KriteriaRelasi
 *
 * Merepresentasikan satu relasi terarah (A -> B) antar dua kode kriteria SMKP.
 * Relasi ini bersifat advisory (NON-blocking) dan digunakan sebagai panduan
 * jejak penelusuran audit (audit trail tracing guide).
 *
 * @property int    $id
 * @property string $kriteria_asal_kode      Kode kriteria yang sedang diaudit
 * @property string $kriteria_tujuan_kode    Kode kriteria yang perlu diperiksa
 * @property string $jenis_relasi            'kunci' | 'referensi'
 * @property string $deskripsi_keterkaitan   Narasi sebab-akibat (nullable)
 */
class KriteriaRelasi extends Model
{
    protected $table = 'kriteria_relasi';

    protected $fillable = [
        'kriteria_asal_kode',
        'kriteria_tujuan_kode',
        'jenis_relasi',
        'deskripsi_keterkaitan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // -- Scopes ----------------------------------------------------------------

    /** Hanya relasi bertipe 'kunci' (peringatan aktif di UI). */
    public function scopeKunci(Builder $query): Builder
    {
        return $query->where('jenis_relasi', 'kunci');
    }

    /** Hanya relasi bertipe 'referensi' (popover pasif di UI). */
    public function scopeReferensi(Builder $query): Builder
    {
        return $query->where('jenis_relasi', 'referensi');
    }

    /** Filter berdasarkan kode asal. */
    public function scopeBerawalDari(Builder $query, string $kode): Builder
    {
        return $query->where('kriteria_asal_kode', $kode);
    }

    // -- Relasi ke Model Kriteria (eager-loadable) ------------------------------

    /**
     * Kriteria asal (sumber).
     * Gunakan join via kode_kriteria karena tabel kriteria_relasi menyimpan kode string,
     * bukan ID integer, agar tetap valid ketika master data di-seed ulang.
     */
    public function kriteriaAsal()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_asal_kode', 'kode_kriteria');
    }

    /** Kriteria tujuan (target penelusuran). */
    public function kriteriaTujuan()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_tujuan_kode', 'kode_kriteria');
    }
}
