<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditOfi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'audit_ofis';

    /**
     * audit_sesi_id dan nomor_urut sengaja TIDAK dimasukkan ke $fillable
     * karena hanya diisi oleh sistem di dalam transaksi database.
     */
    protected $fillable = [
        'sub_elemen_id',
        'kriteria_id',
        'deskripsi',
        'rekomendasi',
        'referensi',
        'dibuat_oleh',
    ];

    protected $casts = [
        'nomor_urut' => 'integer',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    /**
     * Hitung nomor urut berikutnya yang tidak pernah dipakai ulang (termasuk record soft-deleted).
     */
    public static function nextNomorUrutForSesi(int $sesiId): int
    {
        $max = self::withTrashed()
            ->where('audit_sesi_id', $sesiId)
            ->lockForUpdate()
            ->max('nomor_urut');

        return ((int) $max) + 1;
    }

    public function auditSesi(): BelongsTo
    {
        return $this->belongsTo(AuditSesi::class, 'audit_sesi_id');
    }

    public function subElemen(): BelongsTo
    {
        return $this->belongsTo(SubElemen::class, 'sub_elemen_id');
    }

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
