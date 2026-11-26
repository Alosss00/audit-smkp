<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditRencanaSampel extends Model
{
    use HasFactory;

    protected $table = 'audit_rencana_sampels';

    protected $fillable = [
        'audit_rencana_id',
        'sub_elemen_id',
        'jenis_data',
        'metode',
        'teknik_sampling',
        'metode_penentuan',
        'populasi_n',
        'margin_error_e',
        'jumlah_sampel',
        'deskripsi_sampel',
    ];

    protected $casts = [
        'metode'         => 'array',
        'populasi_n'     => 'integer',
        'margin_error_e' => 'decimal:3',
        'jumlah_sampel'  => 'integer',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function rencana(): BelongsTo
    {
        return $this->belongsTo(AuditRencana::class, 'audit_rencana_id');
    }

    public function subElemen(): BelongsTo
    {
        return $this->belongsTo(SubElemen::class, 'sub_elemen_id');
    }
}
