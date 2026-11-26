<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLaporanProfil extends Model
{
    use HasFactory;

    protected $table = 'audit_laporan_profils';

    protected $fillable = [
        'audit_laporan_id',
        'kapasitas_produksi',
        'realisasi_produksi',
        'satuan_produksi',
        'jumlah_pekerja',
        'jumlah_pekerja_asing',
        'narasi_kegiatan',
        'narasi_penerapan_smkp',
        'struktur_organisasi_path',
    ];

    protected $casts = [
        'kapasitas_produksi'   => 'decimal:2',
        'realisasi_produksi'   => 'decimal:2',
        'jumlah_pekerja'       => 'integer',
        'jumlah_pekerja_asing' => 'integer',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(AuditLaporan::class, 'audit_laporan_id');
    }

    public function getStrukturOrganisasiUrlAttribute(): ?string
    {
        if ($this->struktur_organisasi_path) {
            return route('attachments.show', ['type' => 'struktur_organisasi', 'id' => $this->id]);
        }

        return null;
    }
}
