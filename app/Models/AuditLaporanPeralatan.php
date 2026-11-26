<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLaporanPeralatan extends Model
{
    use HasFactory;

    protected $table = 'audit_laporan_peralatans';

    protected $fillable = [
        'audit_laporan_id',
        'kelompok',
        'jenis',
        'tipe',
        'kapasitas',
        'jumlah',
        'urutan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'urutan' => 'integer',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(AuditLaporan::class, 'audit_laporan_id');
    }
}
