<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pica extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_detail_id',
        'deskripsi_temuan',
        'kategori_temuan',
        'kategori_ditetapkan_manual',
        'justifikasi_kategori',
        'akar_masalah',
        'tindakan_koreksi',
        'tindakan_pencegahan',
        'bukti_perbaikan',
        'tenggat_waktu',
        'status',
        'catatan_verifikasi_auditor',
    ];

    protected $casts = [
        'tenggat_waktu'              => 'date',
        'kategori_ditetapkan_manual' => 'boolean',
    ];

    protected $appends = ['bukti_perbaikan_url'];

    /**
     * Accessor for full storage URL of bukti perbaikan file.
     */
    public function getBuktiPerbaikanUrlAttribute(): ?string
    {
        if ($this->bukti_perbaikan) {
            return asset('storage/' . $this->bukti_perbaikan);
        }
        return null;
    }

    /**
     * Relationship to AuditDetail.
     */
    public function auditDetail()
    {
        return $this->belongsTo(AuditDetail::class, 'audit_detail_id');
    }
}

