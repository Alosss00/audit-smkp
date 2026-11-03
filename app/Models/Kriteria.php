<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kriteria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sub_elemen_id',
        'kode_kriteria',
        'deskripsi',
        'nilai_maksimal',
        'persyaratan_dokumen',
        'pedoman_nilai_0',
        'pedoman_nilai_1',
        'pedoman_nilai_2',
        'pedoman_nilai_3',
        'pedoman_nilai_4',
        'pedoman_nilai_json',
        'is_na',
    ];

    protected $casts = [
        'nilai_maksimal'     => 'float',
        'is_na'              => 'boolean',
        'pedoman_nilai_json' => 'array',
    ];

    /**
     * Get the attributes that should be cast (Laravel 11/12 standard).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return $this->casts;
    }

    protected $appends = ['pedoman_array'];

    /**
     * Get unified rubric guidelines array (keyed by score 0..N).
     */
    public function getPedomanArrayAttribute(): array
    {
        if (!empty($this->pedoman_nilai_json) && is_array($this->pedoman_nilai_json)) {
            return $this->pedoman_nilai_json;
        }

        return [
            '0' => $this->pedoman_nilai_0 ?? 'Nilai 0: Tidak ada dokumen, tidak dilaksanakan, dan tidak ada bukti fisik.',
            '1' => $this->pedoman_nilai_1 ?? 'Nilai 1: Ada draft/wacana tetapi belum disahkan atau belum disosialisasikan.',
            '2' => $this->pedoman_nilai_2 ?? 'Nilai 2: Terdokumentasi secara resmi tetapi penerapan di lapangan masih terbatas.',
            '3' => $this->pedoman_nilai_3 ?? 'Nilai 3: Terdokumentasi dan diterapkan penuh tetapi belum dievaluasi secara berkala.',
            '4' => $this->pedoman_nilai_4 ?? 'Nilai 4: Terdokumentasi resmi, diterapkan 100%, dievaluasi berkala, dan ditindaklanjuti.',
        ];
    }

    // -- Relasi Many-to-Many Advisory (pengganti dependency_id) ---------------

    /**
     * Semua relasi yang BERASAL dari kriteria ini (outgoing).
     * Mencakup kedua jenis: 'kunci' dan 'referensi'.
     */
    public function relasiKeluar(): HasMany
    {
        return $this->hasMany(KriteriaRelasi::class, 'kriteria_asal_kode', 'kode_kriteria');
    }

    /**
     * Relasi KUNCI yang berasal dari kriteria ini.
     * Digunakan untuk memuat data banner peringatan di halaman matrix.
     */
    public function relasiKunci(): HasMany
    {
        return $this->hasMany(KriteriaRelasi::class, 'kriteria_asal_kode', 'kode_kriteria')
            ->where('jenis_relasi', 'kunci');
    }

    /**
     * Semua relasi REFERENSI yang berasal dari kriteria ini.
     * Digunakan untuk memuat data popover tooltip di halaman matrix.
     */
    public function relasiReferensi(): HasMany
    {
        return $this->hasMany(KriteriaRelasi::class, 'kriteria_asal_kode', 'kode_kriteria')
            ->where('jenis_relasi', 'referensi');
    }

    // -- Relasi lainnya (tidak berubah) ----------------------------------------

    /**
     * Relationship to SubElemen.
     */
    public function subElemen(): BelongsTo
    {
        return $this->belongsTo(SubElemen::class, 'sub_elemen_id');
    }

    /**
     * Relationship to AuditDetail.
     */
    public function auditDetails(): HasMany
    {
        return $this->hasMany(AuditDetail::class, 'kriteria_id');
    }

    /**
     * Gating rules where this Kriteria acts as upstream/hulu.
     */
    public function gatingRulesAsHulu(): HasMany
    {
        return $this->hasMany(KriteriaGatingRule::class, 'kriteria_hulu_id');
    }

    /**
     * Gating rules where this Kriteria acts as downstream/hilir.
     */
    public function gatingRulesAsHilir(): HasMany
    {
        return $this->hasMany(KriteriaGatingRule::class, 'kriteria_hilir_id');
    }
}
