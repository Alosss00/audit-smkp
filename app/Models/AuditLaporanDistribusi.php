<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLaporanDistribusi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'audit_laporan_distribusis';

    public const JENIS_PENERIMA_OPTIONS = [
        'internal_perusahaan' => 'Internal Perusahaan / Manajemen',
        'ktt_pjo'             => 'KTT / PTL / PJO',
        'ka_it'               => 'Kepala Inspektur Tambang (KaIT / ESDM)',
        'auditi'              => 'Auditi / Perwakilan Area',
        'lainnya'             => 'Pihak Lainnya',
    ];

    public const METODE_OPTIONS = [
        'hardcopy'    => 'Hardcopy / Surat Fisik',
        'email'       => 'Email Resmi',
        'sistem_esdm' => 'Sistem Pelaporan ESDM (Manual Catat)',
        'lainnya'     => 'Metode Lainnya',
    ];

    protected $fillable = [
        'audit_laporan_id',
        'penerima_nama',
        'penerima_jabatan',
        'penerima_instansi',
        'jenis_penerima',
        'tanggal_distribusi',
        'metode',
        'nomor_tanda_terima',
        'bukti_path',
        'catatan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal_distribusi' => 'date',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(AuditLaporan::class, 'audit_laporan_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function getJenisPenerimaLabelAttribute(): string
    {
        return self::JENIS_PENERIMA_OPTIONS[$this->jenis_penerima] ?? (string) $this->jenis_penerima;
    }

    public function getMetodeLabelAttribute(): string
    {
        return self::METODE_OPTIONS[$this->metode] ?? (string) $this->metode;
    }

    public function getBuktiUrlAttribute(): ?string
    {
        if ($this->bukti_path) {
            return route('attachments.show', ['type' => 'distribusi_laporan', 'id' => $this->id]);
        }

        return null;
    }
}
