<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditRencanaJadwal extends Model
{
    use HasFactory;

    protected $table = 'audit_rencana_jadwals';

    public const STATUS_PELAKSANAAN_OPTIONS = [
        'direncanakan' => 'Direncanakan',
        'terlaksana'   => 'Terlaksana',
        'ditunda'      => 'Ditunda',
        'dibatalkan'   => 'Dibatalkan',
    ];

    protected $fillable = [
        'audit_rencana_id',
        'hari_ke',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'jenis_kegiatan',
        'kegiatan',
        'elemen_id',
        'auditor',
        'auditi_pic',
        'lokasi',
        'status_pelaksanaan',
    ];

    protected $casts = [
        'hari_ke' => 'integer',
        'tanggal' => 'date',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function rencana(): BelongsTo
    {
        return $this->belongsTo(AuditRencana::class, 'audit_rencana_id');
    }

    public function elemen(): BelongsTo
    {
        return $this->belongsTo(Elemen::class, 'elemen_id');
    }
}
