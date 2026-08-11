<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditDetail extends Model
{
    use HasFactory;

    protected $table = 'audit_details';

    protected $fillable = [
        'audit_sesi_id',
        'kriteria_id',
        'nilai',
        'is_na',
        'catatan',
        'lampiran',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'is_na' => 'boolean',
    ];

    protected $appends = ['lampiran_url'];

    /**
     * Accessor for full storage URL of attachment.
     */
    public function getLampiranUrlAttribute()
    {
        if ($this->lampiran) {
            return asset('storage/' . $this->lampiran);
        }
        return null;
    }

    /**
     * Relationship to AuditSesi.
     */
    public function auditSesi()
    {
        return $this->belongsTo(AuditSesi::class, 'audit_sesi_id');
    }

    /**
     * Relationship to Kriteria (including soft-deleted criteria so historic audits stay readable).
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id')->withTrashed();
    }
}
