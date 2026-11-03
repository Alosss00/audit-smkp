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

    protected $fillable = [
        'nama_perusahaan',
        'kode_perusahaan',
        'kategori',
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
     * Relationship to AuditSesi.
     */
    public function auditSesis(): HasMany
    {
        return $this->hasMany(AuditSesi::class, 'perusahaan_id');
    }
}
