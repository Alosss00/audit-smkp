<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perusahaan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'perusahaans';

    public const JENIS_USAHA_OPTIONS = [
        'tambang'                     => 'Pertambangan (Operasi Produksi / Eksplorasi)',
        'pengolahan_pemurnian'        => 'Pengolahan dan/atau Pemurnian',
        'jasa_pertambangan_inti'      => 'Jasa Pertambangan Inti',
        'jasa_konsultasi_perencanaan' => 'Jasa Konsultasi & Perencanaan',
        'jasa_non_inti'               => 'Jasa Pertambangan Non-Inti',
    ];

    protected $fillable = [
        'nama_perusahaan',
        'kode_perusahaan',
        'kategori',
        'jenis_usaha',
        'jenis_perizinan',
        'jenis_komoditas',
        'alamat_kantor_site',
        'alamat_lokasi_tambang',
        'alamat_kantor_pusat',
        'nama_ktt_default',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    /**
     * Check whether this company is labeled as Kontraktor (Perusahaan Jasa Pertambangan).
     */
    public function isKontraktor(): bool
    {
        return strtolower(trim((string) $this->kategori)) === 'kontraktor';
    }

    /**
     * Suggested default risk class based on jenis_usaha classification (Kepdirjen 185 / materi pelatihan).
     * Can be overridden manually on the Stage I form.
     */
    public function kelasRisikoDefault(): ?string
    {
        return match ($this->jenis_usaha) {
            'tambang', 'pengolahan_pemurnian', 'jasa_pertambangan_inti' => 'Tinggi',
            'jasa_konsultasi_perencanaan'                               => 'Menengah',
            'jasa_non_inti'                                             => 'Rendah',
            default                                                     => null,
        };
    }

    /**
     * Accessor alias for kelasRisikoDefault.
     */
    public function getKelasRisikoDefaultAttribute(): ?string
    {
        return $this->kelasRisikoDefault();
    }

    /**
     * Map jenis_usaha to signatory matrix group for Rencana Audit (026A).
     */
    public function kelompokPenandatangan(): ?string
    {
        return match ($this->jenis_usaha) {
            'tambang'                                                                => 'tambang',
            'pengolahan_pemurnian'                                                   => 'pengolahan_pemurnian',
            'jasa_pertambangan_inti', 'jasa_konsultasi_perencanaan', 'jasa_non_inti' => 'jasa_pertambangan',
            default                                                                  => null,
        };
    }

    /**
     * Accessor alias for kelompokPenandatangan.
     */
    public function getKelompokPenandatanganAttribute(): ?string
    {
        return $this->kelompokPenandatangan();
    }

    /**
     * Relationship to AuditSesi.
     */
    public function auditSesis(): HasMany
    {
        return $this->hasMany(AuditSesi::class, 'perusahaan_id');
    }
}
