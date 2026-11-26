<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditDaftarHadirPeserta extends Model
{
    use HasFactory;

    protected $table = 'audit_daftar_hadir_pesertas';

    public const PERAN_OPTIONS = [
        'auditor' => 'Tim Auditor',
        'auditi'  => 'Auditi / Manajemen',
        'pemandu' => 'Pemandu',
        'tamu'    => 'Tamu / Pengamat',
    ];

    protected $fillable = [
        'audit_daftar_hadir_id',
        'nama',
        'jabatan',
        'unit_instansi',
        'peran',
        'hadir',
        'urutan',
    ];

    protected $casts = [
        'hadir'  => 'boolean',
        'urutan' => 'integer',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function daftarHadir(): BelongsTo
    {
        return $this->belongsTo(AuditDaftarHadir::class, 'audit_daftar_hadir_id');
    }

    public function getPeranLabelAttribute(): string
    {
        return self::PERAN_OPTIONS[$this->peran] ?? (string) $this->peran;
    }
}
