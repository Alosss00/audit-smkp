<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pica extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * audit_sesi_id dan nomor_urut sengaja TIDAK dimasukkan ke $fillable
     * karena hanya boleh ditetapkan oleh sistem.
     */
    protected $fillable = [
        'audit_detail_id',
        'deskripsi_temuan',
        'deskripsi_ketidaksesuaian',
        'plor_problem',
        'plor_location',
        'plor_objective_evidence',
        'plor_reference',
        'kategori_temuan',
        'kategori_ditetapkan_manual',
        'justifikasi_kategori',
        'akar_masalah',
        'tindakan_koreksi',
        'tindakan_pencegahan',
        'pic_perbaikan',
        'bukti_perbaikan',
        'tenggat_waktu',
        'status',
        'catatan_verifikasi_auditor',
        'diverifikasi_oleh',
        'tanggal_verifikasi',
        'persetujuan_lead_auditor_oleh',
        'tanggal_persetujuan_lead',
        'persetujuan_auditi_nama',
        'tanggal_persetujuan_auditi',
    ];

    protected $casts = [
        'nomor_urut'                 => 'integer',
        'tenggat_waktu'              => 'date',
        'tanggal_verifikasi'         => 'date',
        'tanggal_persetujuan_lead'   => 'date',
        'tanggal_persetujuan_auditi' => 'date',
        'kategori_ditetapkan_manual' => 'boolean',
    ];

    /**
     * Get the attributes that should be cast (Laravel 11/12 standard).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return $this->casts;
    }

    protected $appends = ['bukti_perbaikan_url'];

    /**
     * Accessor for full storage URL of bukti perbaikan file.
     */
    public function getBuktiPerbaikanUrlAttribute(): ?string
    {
        if ($this->bukti_perbaikan) {
            return route('attachments.show', ['type' => 'pica', 'id' => $this->id]);
        }
        return null;
    }

    /**
     * Relationship to AuditDetail.
     */
    public function auditDetail(): BelongsTo
    {
        return $this->belongsTo(AuditDetail::class, 'audit_detail_id');
    }

    public function auditSesi(): BelongsTo
    {
        return $this->belongsTo(AuditSesi::class, 'audit_sesi_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function persetujuanLeadAuditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'persetujuan_lead_auditor_oleh');
    }

    /**
     * Cek apakah 4 elemen PLOR (Problem, Location, Objective Evidence, Reference) telah terisi lengkap.
     */
    public function isPlorLengkap(): bool
    {
        return trim((string) $this->plor_problem) !== ''
            && trim((string) $this->plor_location) !== ''
            && trim((string) $this->plor_objective_evidence) !== ''
            && trim((string) $this->plor_reference) !== '';
    }

    /**
     * Hitung nomor urut temuan berikutnya per sesi audit (tidak pernah dipakai ulang meski dihapus).
     */
    public static function nextNomorUrutForSesi(int $sesiId): int
    {
        $max = self::withTrashed()
            ->where('audit_sesi_id', $sesiId)
            ->lockForUpdate()
            ->max('nomor_urut');

        return ((int) $max) + 1;
    }
}

