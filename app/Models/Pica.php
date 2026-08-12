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
        'akar_masalah',
        'tindakan_koreksi',
        'tindakan_pencegahan',
        'tenggat_waktu',
        'pic_perbaikan',
        'status',
        'catatan_verifikasi_auditor',
    ];

    protected $casts = [
        'tenggat_waktu' => 'date',
    ];

    /**
     * Relationship to AuditDetail.
     */
    public function auditDetail()
    {
        return $this->belongsTo(AuditDetail::class, 'audit_detail_id');
    }
}
