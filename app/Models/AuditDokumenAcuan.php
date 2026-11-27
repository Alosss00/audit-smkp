<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditDokumenAcuan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'audit_dokumen_acuans';

    public const JENIS_OPTIONS = [
        'rkab'            => 'RKAB (Rencana Kerja dan Anggaran Biaya)',
        'sop_audit'       => 'SOP / Prosedur Audit Internal SMKP',
        'manual_smkp'     => 'Manual SMKP Minerba',
        'dokumen_lainnya' => 'Dokumen Acuan Lainnya',
    ];

    protected $fillable = [
        'audit_sesi_id',
        'jenis_dokumen',
        'judul',
        'nomor_dokumen',
        'tanggal_dokumen',
        'file_path',
        'catatan',
        'diunggah_oleh',
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function auditSesi(): BelongsTo
    {
        return $this->belongsTo(AuditSesi::class, 'audit_sesi_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS_OPTIONS[$this->jenis_dokumen] ?? (string) $this->jenis_dokumen;
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return route('attachments.show', ['type' => 'dokumen_acuan', 'id' => $this->id]);
        }

        return null;
    }
}
