<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLaporanSnapshot extends Model
{
    use HasFactory;

    protected $table = 'audit_laporan_snapshots';

    public $timestamps = false;

    protected $fillable = [
        'audit_laporan_id',
        'revisi_ke',
        'payload_json',
        'hash_sha256',
        'dibuat_oleh',
        'created_at',
    ];

    protected $casts = [
        'revisi_ke'  => 'integer',
        'created_at' => 'datetime',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(AuditLaporan::class, 'audit_laporan_id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Decode snapshot payload JSON into an associative array.
     *
     * @return array<string, mixed>
     */
    public function getPayloadAttribute(): array
    {
        $decoded = json_decode((string) $this->payload_json, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Verifikasi integritas hash SHA-256 terhadap payload_json tersimpan.
     */
    public function isHashValid(): bool
    {
        $computed = hash('sha256', (string) $this->payload_json);

        return hash_equals((string) $this->hash_sha256, $computed);
    }
}
