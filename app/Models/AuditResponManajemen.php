<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditResponManajemen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'audit_respon_manajemens';

    protected $fillable = [
        'audit_sesi_id',
        'tanggal_respon',
        'nama_penandatangan',
        'jabatan_penandatangan',
        'isi_respon',
        'dokumen_path',
    ];

    protected $casts = [
        'tanggal_respon' => 'date',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function auditSesi(): BelongsTo
    {
        return $this->belongsTo(AuditSesi::class, 'audit_sesi_id');
    }

    public function getDokumenUrlAttribute(): ?string
    {
        if ($this->dokumen_path) {
            return route('attachments.show', ['type' => 'respon_manajemen', 'id' => $this->id]);
        }

        return null;
    }

    /**
     * Peringatan bila tanggal respon manajemen lebih awal dari tanggal selesai audit.
     */
    public function getDateWarning(AuditSesi $sesi): ?string
    {
        if (! $this->tanggal_respon || ! $sesi->tanggal_selesai) {
            return null;
        }

        if ($this->tanggal_respon->lt($sesi->tanggal_selesai)) {
            return 'Tanggal respon manajemen (' . $this->tanggal_respon->format('d/m/Y') . ') lebih awal dari tanggal selesai audit (' . $sesi->tanggal_selesai->format('d/m/Y') . ').';
        }

        return null;
    }
}
