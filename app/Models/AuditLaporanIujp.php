<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLaporanIujp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'audit_laporan_iujps';

    protected $fillable = [
        'audit_laporan_id',
        'sumber_audit_sesi_id',
        'nama_perusahaan_iujp',
        'nomor_iujp',
        'bidang_usaha',
        'tanggal_audit',
        'skor_pencapaian',
        'jumlah_temuan_kritikal',
        'jumlah_temuan_mayor',
        'jumlah_temuan_minor',
        'ringkasan',
        'dokumen_path',
    ];

    protected $casts = [
        'tanggal_audit'          => 'date',
        'skor_pencapaian'        => 'decimal:2',
        'jumlah_temuan_kritikal' => 'integer',
        'jumlah_temuan_mayor'    => 'integer',
        'jumlah_temuan_minor'    => 'integer',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(AuditLaporan::class, 'audit_laporan_id');
    }

    public function sumberSesi(): BelongsTo
    {
        return $this->belongsTo(AuditSesi::class, 'sumber_audit_sesi_id');
    }

    public function getDokumenUrlAttribute(): ?string
    {
        if ($this->dokumen_path) {
            return route('attachments.show', ['type' => 'laporan_iujp', 'id' => $this->id]);
        }

        return null;
    }
}
