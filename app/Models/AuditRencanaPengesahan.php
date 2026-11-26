<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditRencanaPengesahan extends Model
{
    use HasFactory;

    protected $table = 'audit_rencana_pengesahans';

    protected $fillable = [
        'audit_rencana_id',
        'urutan',
        'jabatan',
        'nama',
        'tanggal_ttd',
    ];

    protected $casts = [
        'urutan'      => 'integer',
        'tanggal_ttd' => 'date',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function rencana(): BelongsTo
    {
        return $this->belongsTo(AuditRencana::class, 'audit_rencana_id');
    }
}
