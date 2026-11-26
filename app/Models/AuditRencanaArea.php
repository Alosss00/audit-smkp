<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditRencanaArea extends Model
{
    use HasFactory;

    protected $table = 'audit_rencana_areas';

    protected $fillable = [
        'audit_rencana_id',
        'nama_area',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
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
