<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLaporanNarasiElemen extends Model
{
    use HasFactory;

    protected $table = 'audit_laporan_narasi_elemens';

    protected $fillable = [
        'audit_laporan_id',
        'elemen_id',
        'narasi',
    ];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(AuditLaporan::class, 'audit_laporan_id');
    }

    public function elemen(): BelongsTo
    {
        return $this->belongsTo(Elemen::class, 'elemen_id');
    }
}
